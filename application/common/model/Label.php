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
            return $this->select()->toArray();
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
}
