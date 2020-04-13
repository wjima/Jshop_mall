<?php

namespace addons\Pc\model;

use app\common\model\Article;
use think\Model;
use think\Validate;

class PcFloorAdv extends Model
{

    protected $rule = [
        'cat'      => 'require',
        'floor_id' => 'require',
        'type'     => 'require',
        'val'      => 'require',
        'image'    => 'require',
        'sort'     => 'require|number|between:1,100',
    ];
    protected $msg = [
        'floor_id.require' => '所属楼层必须',
        'type'             => '广告类型必须',
        'val.require'      => '请选择广告对应数据',
        'cat.require'      => '广告类别必须',
        'image'            => '广告图片必须',
        'sort.require'     => '排序必须',
        'sort.number'      => '排序格式错误',
        'sort.between'     => '排序须在1-100之间',
    ];

    const CAT_ADV_URL = 1;  //广告url
    const CAT_ADV_GOODS = 3;  //广告商品
    const CAT_ADV_ARTICLE = 2;  //广告文章

    const TYPE_LEFT = 1; //左侧广告
    const TYPE_BOTTOM = 2; //底部广告

    public $data = [
        'type' => [
            1 => '楼层侧面广告',
            2 => '楼层底部广告',
        ],
        'cat'  => [
            1 => 'Url',
            2 => '文章',
            3 => '商品',
        ],
    ];

    /**
     * @param $post
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
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

    //where搜索条件
    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['floor_id']) && $post['floor_id'] != "") {
            $where[] = ['floor_id', 'eq', $post['floor_id']];
        }
        if (isset($post['cat']) && $post['cat'] != "") {
            $where[] = ['cat', 'eq', $post['cat']];
        }
        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort' => 'Asc', 'id' => 'desc'];
        return $result;
    }


    /**
     * @param $list
     *
     * @return mixed
     */
    protected function tableFormat($list)
    {
        $floorModel = new PcFloor();

        foreach ($list as $k => $v) {
            $list[$k]['floor_name'] = $floorModel->floorName($v['floor_id']);
            $list[$k]['type_name']  = $this->data['type'][$list[$k]['type']];
            $list[$k]['cat_name']   = $this->data['cat'][$list[$k]['cat']];
            $list[$k]['utime']      = getTime($list[$k]['utime']);
            if ($v['cat'] == self::CAT_ADV_ARTICLE) {
                $article         = new Article();
                $list[$k]['val'] = $article->where('id', 'eq', $list[$k]['val'])->value('title');
            }
            if ($v['cat'] == self::CAT_ADV_GOODS) {
                $list[$k]['val'] = get_goods_info($list[$k]['val']);
            }
        }
        return $list;
    }

    //添加
    public function add($data)
    {
        $return   = [
            'status' => false,
            'msg'    => '添加失败',
            'data'   => []
        ];
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $return['msg'] = $validate->getError();
            return $return;
        }

        $rel = $this->allowField(true)->save($data);

        if ($rel) {
            $return['status'] = true;
            $return['msg']    = '添加成功';
        }

        return $return;


    }

    //编辑
    public function edit($data)
    {
        $return = [
            'status' => false,
            'msg'    => '编辑失败',
            'data'   => []
        ];

        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $return['msg'] = $validate->getError();
            return $return;
        }

        $rel = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if ($rel || $rel === 0) {
            $return['status'] = true;
            $return['msg']    = '编辑成功';
        }
        return $return;
    }

    //详细信息
    public function getInfo($id)
    {
        $rel = $this->where('id', 'eq', $id)->find();
        if ($rel['cat'] == self::CAT_ADV_GOODS) {
            $rel['name'] = get_goods_info($rel['val']);
        }
        if ($rel['cat'] == self::CAT_ADV_ARTICLE) {
            $model       = new Article();
            $rel['name'] = $model->where('id', 'eq', $rel['val'])->value('title');
        }
        $rel['image_url'] = _sImage($rel['image']);
        return $rel;
    }

    //批量删除
    public function delFloor($ids = 0)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '数据不存在'
        ];
        $goods  = $this::get($ids);
        if (!$goods) {
            return $result;
        }
        $this->startTrans();
        $res = $this->where(['id' => $ids])->delete();
        if (!$res) {
            $this->rollback();
            $result['msg'] = '删除失败';
            return $result;
        }
        $this->commit();
        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }
}
