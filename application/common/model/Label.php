<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;
class Label extends Common
{

    public $default_label = [
        [
            'name'  => '热卖',
            'style' => 'red',
        ],
        [
            'name'  => '新品',
            'style' => 'green',
        ],
        [
            'name'  => '推荐',
            'style' => 'orange',
        ],
        [
            'name'  => '促销',
            'style' => 'blue',
        ]
    ];
    /**
     * 保存label数据
     * @param $data ['ids'=>'模型主键id数组','label'=>'标签数组','model'=>'打标签模型']
     * @return array
     */
    public function addData($data)
    {
        $result = error_code(10018);
        if (!isset($data['ids'])) {
            return error_code(10051);
        }
        if (!isset($data['label'])) {
            return error_code(10064);
        }
        $labels = $ids = [];
        foreach ($data['label'] as $key => $val) {
            $labels[$key]['name']  = $val['text'];
            $labels[$key]['style'] = $val['style'];
            $label = self::where($labels[$key])->find();
            if ($label) {
                $labels[$key]['id'] = $label['id'];
            } else {
                //插入
                $labels[$key]['id'] = $this->insert($labels[$key], false, true);
            }
            $ids[] = $labels[$key]['id'];
        }
        $model = model($data['model']);
        /* $udata = [
             'label_ids' => implode(',', $ids),
         ];*/
        $dataLabels = $model::where('id', 'in', $data['ids'])->field('id,label_ids')->select();
        if (!$dataLabels->isEmpty()) {
            foreach ($dataLabels as $key => $val) {
                $label_ids          = [];
                $label_ids          = explode(',', $val['label_ids']);
                $label_ids          = array_filter($label_ids);
                $label_ids          = array_unique(array_merge((array)$ids, (array)$label_ids));
                $udata['label_ids'] = implode(',', $label_ids);
                $res                = $model::where('id', 'eq', $val['id'])->update($udata);
            }
        }
        if ($res !== false) {
            $result = [
                'status' => true,
                'data'   => [],
                'msg'    => '操作成功',
            ];
            return $result;
        }
        return $result;
    }

    /**
     * 获取所有标签
     * @return array
     */
    public function getAllLabel()
    {
        if (!$this->select()->isEmpty()) {
            $templabels = $this->select()->toArray();
            foreach ($templabels as $key => $value) {
                foreach($this->default_label as $k=>$v){
                    if($value['name'] == $v['name'] && $value['style'] == $v['style']){
                        unset($this->default_label[$k]);
                    }
                }
            }
            $labels = $templabels + $this->default_label;
            return $labels;
        }
        return [];
    }

    /**
     * 根据id获取名称
     * @param string $names 标签名称
     * @return array
     */
    public function getIdsByName($names = '', $isForce = false)
    {
        $label      = [];
        $labelIds   = '';
        $labelArray = explode(',', $names);
        if ($labelArray) {
            foreach ($labelArray as $key => $val) {
                if ($val) {
                    $labelSql = $this->field('id')->where('name', 'like', '%' . $val . '%');

                    $id = $labelSql->find();
                    if (!$id && $isForce) {
                        $iData['name']      = $val;
                        $this->save($iData);
                        $label_id = $this->getLastInsID();
                        $id['id'] = $label_id;
                    }
                    $label[] = $id;
                }
            }
        }
        if ($label) {
            $ids      = array_column($label, 'id');
            $labelIds = implode(',', $ids);
        }
        return $labelIds;
    }

    /**
     * 获取所有选中数据的标签
     * @param $ids
     * @param $model_name
     * @return array
     */
    public function getAllSelectLabel($ids, $model_name)
    {
        $model      = model($model_name);
        $filter[]   = ['id', 'in', $ids];
        $dataLabels = $model::where($filter)->field('id,label_ids')->select();
        $labels     = [];
        if (!$dataLabels->isEmpty()) {
            foreach ($dataLabels->toArray() as $key => $val) {
                $label_ids = explode(',', $val['label_ids']);
                $labels    = array_merge((array)$label_ids, (array)$labels);
            }
            $label_id = array_unique(array_filter($labels));
            if (!$label_id) {
                return [];
            }
            $labels = self::where('id', 'in', implode(',', $label_id))->select();
            return $labels->toArray();
        } else {
            return [];
        }
    }


    public function delData($data)
    {
        $result = error_code(10023);
        if (!isset($data['ids'])) {
            return error_code(10051);
        }
        $labels = $ids = [];
        foreach ((array)$data['label'] as $key => $val) {
            $labels[$key]['name']  = $val['text'];
            $labels[$key]['style'] = $val['style'];
            $label = self::where($labels[$key])->find();
            if ($label) {
                $labels[$key]['id'] = $label['id'];
                $ids[]              = $labels[$key]['id'];
            }
        }

        $model = model($data['model']);

        $dataLabels = $model::where('id', 'in', $data['ids'])->field('id,label_ids')->select();
        if (!$dataLabels->isEmpty()) {
            foreach ($dataLabels as $key => $val) {
                $label_ids = explode(',', $val['label_ids']);
                if ($label_ids) {
                    $label_ids = array_filter($label_ids);
                }
                $label_ids          = array_unique(array_intersect($ids, $label_ids));
                $udata['label_ids'] = implode(',', $label_ids);
                $res                = $model::where('id', 'eq', $val['id'])->update($udata);
            }
        }
        if ($res !== false) {
            $result = [
                'status' => true,
                'data'   => [],
                'msg'    => '操作成功',
            ];
            return $result;
        }
        return $result;
    }
    public function del($id){
        if($id){
            $res = $this->where("id","eq",$id)->delete();
            if($res){
                return true;
            }
        }else{
            return false;
        }
    }
    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $result['where'] = [];
        $result['field'] = "*";
        $result['order'] = [];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['style'] == "green") {
                $list[$k]['style'] = "绿色";
            }elseif ($v['style'] == "red"){
                $list[$k]['style'] = "红色";
            }elseif ($v['style'] == "orange"){
                $list[$k]['style'] = "橙色";
            }elseif ($v['style'] == "blue"){
                $list[$k]['style'] = "蓝色";
            }
        }
        return $list;
    }
}
