<?php

namespace addons\Pc\model;

use app\common\model\Article;
use app\common\model\ArticleType;
use app\common\model\Goods;
use think\Db;
use think\Model;
use think\Validate;

class PcMenu extends Model
{

    protected $rule = [
        'name' => 'require|max:40',
        'type' => 'require',
        'sort' => 'require|number|between:1,100',

    ];

    protected $msg = [
//        'name.require' => '菜单名称必须',
//        'name.max'     => '菜单名称不能超过40个字符',
'type'         => '菜单类型必须',
'sort.require' => '排序必须',
'sort.number'  => '排序必须是数字',
'sort.between' => '排序须在1-100之间',
    ];
    const TYPE_URL = 1; // URL
    const TYPE_ARTICLE_TYPE = 2; //文章分类id
    const TYPE_ARTICLE = 3; //文章详情
    const TYPE_GOODS = 4; //商品列表
    const TYPE_GOODS_INFO = 5; //商品详情
    const TYPE_TL_MENU = 6;//二级菜单
    public $data = [
        'type' => [
            1 => 'Url',
            2 => '文章分类',
            3 => '文章详情',
            4 => '商品列表',
            5 => '商品详情',
            //            6 => '二级菜单',
        ],
        'code' => [
            'top'    => '顶部菜单',
            'bottom' => '底部菜单',
            'nav'    => '导航菜单',
        ],
    ];

    /**
     *  后台分类 树形列表
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = 20; // 后台列表分页数量默认50条
        }
        $tableWhere = $this->tableWhere($post);
        $list       = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data       = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code']  = 0;
        $re['msg']   = '';
        $re['count'] = $list->total();
        $re['data']  = $data;
        return $re;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     *
     * @param $post
     *
     * @return mixed
     * @author sin
     */
    protected function tableWhere($post)
    {
        $result['where'] = [];
        $where           = [];
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }
        if (isset($post['code']) && $post['code'] != "") {
            $where[] = ['code', 'eq', $post['code']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['id' => 'desc', 'sort' => 'asc'];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     *
     * @param $list
     *
     * @return mixed
     * @author sin
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['pid'] == 0) {
                $v['f_name'] = $this->data['code'][$v['code']];
                $v['addSon'] = 'true';
            } else {
                $v['f_name'] = $this->data['code'][$v['code']] . '-' . $this->where('id', 'eq', $v['pid'])->value('name');
                $v['addSon'] = 'false';
            }
            $v['type_name'] = $this->data['type'][$v['type']];
            $v              = $this->formatData($v, true);
        }
        return $list;
    }

    public function addData($data)
    {
        $result   = [
            'status' => true,
            'msg'    => '保存成功',
            'data'   => []
        ];
        $data     = $this->formatData($data);
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data)) {
                $result['status'] = false;
                $result['msg']    = '保存失败';
            }
        }
        return $result;

    }


    /*
     * 根基类型获取数据
     *
     * @param $data pc导航单条数据
     * @param $val  true 获取对应导航值 false 赋值
     *
     * */
    public function formatData($data, $val = false)
    {
        if (empty($data)) {
            return [];
        }
        if (!$val) {
            if ($data['type'] == self::TYPE_URL) {
                $data['val'] = $data['url'];
            } else if ($data['type'] == self::TYPE_ARTICLE_TYPE) {
                $data['val'] = $data['article_type'];
            } else if ($data['type'] == self::TYPE_ARTICLE) {
                $data['val'] = $data['article_id'];
            } else if ($data['type'] == self::TYPE_GOODS) {
                $goods_ids   = implode(',', array_keys($data['goods']));
                $data['val'] = $goods_ids;
            } else if ($data['type'] == self::TYPE_GOODS_INFO) {
                $data['val'] = $data['goods_id'];
            }
        } else {
            if ($data['type'] == self::TYPE_URL) {
                $data['url']  = trim($data['val']);
                $data['data'] = trim($data['val']);
                $data['val']  = trim($data['val']);
            } else if ($data['type'] == self::TYPE_ARTICLE_TYPE) {
                $articleTypeModel     = new ArticleType();
                $articleType          = $articleTypeModel->field("id,type_name")->where(['id' => $data['val']])->find();
                $data['article_type'] = $data['val'];
                $data['data']         = $data['val'];
                $data['val']          = $articleType['type_name'];
            } else if ($data['type'] == self::TYPE_ARTICLE) {
                $articleModel       = new Article();
                $articleInfo        = $articleModel->field('id,title')->where(['id' => $data['val']])->find();
                $data['article_id'] = $data['val'];
                $data['data']       = $data['val'];
                $data['val']        = $articleInfo['title'];
            } else if ($data['type'] == self::TYPE_GOODS) {
                $goodsModel = new Goods();
                $goods_ids  = explode(',', $data['val']);
                foreach ($goods_ids as $k => $v) {
                    $goodInfo = $goodsModel->getGoodsDetial($v, 'id,name,spes_desc,sort,image_id');
                    if ($goodInfo['status']) {
                        $goodsInfo[] = $goodInfo['data'];
                    } else {
                        $goodsInfo[] = [];
                    }
                }
                $goods_name         = array_column($goodsInfo, 'name');
                $goods_name         = implode(',', $goods_name);
                $data['goods_info'] = $goodsInfo;
                $data['data']       = $goodsInfo;
                $data['goods']      = $data['val'];
                $data['val']        = $goods_name;
            } else if ($data['type'] == self::TYPE_GOODS_INFO) {
                $goodsModel       = new Goods();
                $goodsInfo        = $goodsModel->field('id,name')->where(['id' => $data['val']])->find();
                $data['goods_id'] = $data['val'];
                $data['data']     = $data['val'];
                $data['val']      = $goodsInfo['name'];
            }
        }
        return $data;
    }

    public function formatChildrenData($data)
    {
        if (empty($data)) {
            return [];
        }
        $pdata = $this->where(['pid' => $data['id']])->select();
        // 如果根据id查询不到pid 说明该导航没有子导航
        if (!$pdata) {
            $data['children'] = [];
        } else {
            foreach ($pdata as &$v) {
                $v = $this->formatData($v, true);
            }
            $data['children'] = $pdata;
        }
        return $data;
    }

    /**
     *  修改
     *
     * @param $data
     *
     * @return array
     */
    public function editData($data)
    {
        $result   = [
            'status' => true,
            'msg'    => '编辑失败',
            'data'   => []
        ];
        $data     = $this->formatData($data);
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            if ($this->allowField(true)->save($data, ['id' => $data['id']])) {
                $pdata['code'] = $data['code'];
                $this->where(['pid' => $data['id']])->update($pdata);
                $result['status'] = true;
                $result['msg']    = '编辑成功';
                return $result;
            }
        }
        return $result;
    }

    /*
     * 根据id获取导航数据
     * */
    public function getPcMenu($id = 0)
    {
        $data = $this->where(['id' => $id])->find();
        if (!$data) {
            return false;
        }
        return $this->formatData($data, true);
    }


    public function articleTypeList()
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => []
        ];

        $list = $this->field('id,type_name')->select();

        $result['data']['list'] = $list;

        return $result;
    }

    public function comments()
    {
        return $this->hasMany('PcMenuParams');
    }

    public function getMenuName($id)
    {
        $menu = '';
        $rel  = $this->where('id', 'eq', $id)->find();
        if ($rel) {
            $menu = $rel['name'];
            if ($rel['pid'] != 0) {

                $menu1 = $this->where('id', 'eq', $rel['pid'])->value('name');
                $menu  = $menu1 . '-' . $menu;
            }
        }
        return $menu;
    }

    //api获取所有菜单
    public function getMenu()
    {
        $menu = $this->data['code'];
        foreach ($menu as $k => &$v) {
            $where   = [];
            $where[] = ['pid', 'eq', 0];
            $where[] = ['code', 'eq', $k];
            $v       = $this->where($where)->order('sort', 'asc')->select();
            foreach ($v as &$v1) {
                $v1 = $this->formatData($v1, true);
                $v1 = $this->formatChildrenData($v1);
            }
        }
        return $menu;
    }

    public function getTreeList()
    {
        $list      = $this->select();
        $menuModel = new \addons\Pc\model\PcMenuParams();
        foreach ($list as $k => $v) {
            $rel = $menuModel->where('type', 'eq', $v['id'])->find();
            if ($rel) {
                $v['isSon'] = 'true';
            } else {
                $v['isSon'] = 'false';
            }
        }
        return $list;
    }
}
