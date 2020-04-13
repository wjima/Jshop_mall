<?php

namespace addons\Pc\model;

use app\common\model\Goods;
use think\Model;
use think\Validate;

class PcFloor extends Model
{

    protected $rule = [
        'name' => 'require|max:30',
        'sort' => 'require|number|between:1,100',
        'val'  => 'require'
    ];
    protected $msg = [
        'name.require' => '楼层名称必须',
        'name.max'     => '楼层名最多30个字符',
        'sort.require' => '排序必须',
        'sort.number'  => '排序格式错误',
        'sort.between' => '排序须在1-100之间',
        'val'          => '请选择商品'
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
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', "%" . $post['name'] . "%"];
        }
        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort ASC'];
        return $result;
    }


    /**
     * @param $list
     *
     * @return mixed
     */
    protected function tableFormat($list)
    {
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
        $data['val'] = implode(',', $data['val']);
        $rel         = $this->allowField(true)->save($data);
        if ($rel) {
            $return['status'] = true;
            $return['msg']    = '添加成功';
        }
        return $return;
    }

    //编辑
    public function edit($data)
    {
        $return   = [
            'status' => false,
            'msg'    => '编辑失败',
            'data'   => []
        ];
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $return['msg'] = $validate->getError();
            return $return;
        }
        $data['val'] = implode(',', $data['val']);
        $rel         = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if ($rel || $rel === 0) {
            $return['status'] = true;
            $return['msg']    = '编辑成功';
        }
        return $return;
    }

    //获取所有楼层
    public function getAll()
    {
        return $this->order('sort', 'asc')->select();
    }

    //根据楼层id获取楼层名
    public function getFloorName($id)
    {

        return $this->where('id', 'eq', $id)->value('name');
    }

    //详细信息
    public function getInfo($id)
    {
        $rel = $this->where('id', 'eq', $id)->find();

        $val = [];
        foreach (explode(',', $rel['val']) as $k => $v) {
            $val [] = [
                'id'   => $v,
                'name' => get_goods_info($v),
            ];
        }
        $rel['val'] = $val;
        return $rel;
    }

    //api 获取楼层接口
    public function getFloor($page = 1, $limit = 10, $key_word)
    {
        if ($key_word) {
            $list = $this->order('sort', 'asc')
                ->where('keyword', 'like', "%" . $key_word . "%")
                ->page($page, $limit)
                ->select();
        } else {
            $list = $this->order('sort', 'asc')
                ->page($page, $limit)
                ->select();
        }

        if (count($list) != 0) {
            $advModel   = new PcFloorAdv();
            $goodsModel = new Goods();
            foreach ($list as $k => $v) {
                $val  = explode(',', $v['val']);
                $left = $advModel->field('cat,image,val,sort')->where([['floor_id', 'eq', $v['id']], ['type', 'eq', $advModel::TYPE_LEFT]])->order('sort', 'asc')->select();
                foreach ($left as $k1 => $v1) {
                    $v1['image'] = _sImage($v1['image']);
                }
                $bottom = $advModel->field('cat,image,val,sort')->where([['floor_id', 'eq', $v['id']], ['type', 'eq', $advModel::TYPE_BOTTOM]])->order('sort', 'asc')->select();
                foreach ($bottom as $k2 => $v2) {
                    $v2['image'] = _sImage($v2['image']);
                }
                $v['adv'] = [
                    'left'   => $left,
                    'bottom' => $bottom,
                ];
                $goods    = [];
                foreach ($val as $key => $val) {
                    $goodInfo = $goodsModel->getGoodsDetial($val, 'id,name,spes_desc,sort,image_id');
                    if ($goodInfo['status']) {
                        $goods[] = $goodInfo['data'];
                    }
                }
                $last_names = array_column($goods, 'sort');
                array_multisort($last_names, SORT_ASC, $goods);
                $v['goods'] = $goods;
            }
        }
        return json([
            'msg'    => '获取成功',
            'status' => true,
            'data'   => $list
        ]);
    }

    //获取楼层名
    public function floorName($id)
    {
        return $this->where('id', 'eq', $id)->value('name');
    }
}
