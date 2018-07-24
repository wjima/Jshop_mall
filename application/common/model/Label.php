<?php
/**
 * Created by PhpStorm.
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-08
 * Time: 19:34
 */

namespace app\common\model;
class Label extends Common
{
    /**
     * 保存label数据
     * @param $data ['ids'=>'模型主键id数组','label'=>'标签数组','seller_id'=>'商户id','model'=>'打标签模型']
     * @return array
     */
    public function addData($data)
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数丢失',
        ];
        if (!isset($data['ids'])) {
            return $result;
        }
        if (!isset($data['label'])) {
            $result['msg'] = '请先选择标签';
            return $result;
        }
        $labels = $ids = [];
        foreach ($data['label'] as $key => $val) {
            $labels[$key]['name']  = $val['text'];
            $labels[$key]['style'] = $val['style'];
            if (isset($data['seller_id'])) {
                $labels[$key]['seller_id'] = $data['seller_id'];
            }
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
     * @param int $seller_id
     * @return array
     */
    public function getAllLabel($seller_id = 0)
    {
        if ($seller_id) {
            $this->where(['seller_id' => $seller_id]);
        }
        if (!$this->select()->isEmpty()) {
            return $this->select()->toArray();
        }
        return [];
    }

    /**
     * 根据id获取名称
     * @param string $names 标签名称
     * @param int $seller_id 商户id
     * @return array
     */
    public function getIdsByName($names = '', $seller_id = 0, $isForce = false)
    {
        $label      = [];
        $labelIds   = '';
        $labelArray = explode(',', $names);
        if ($labelArray) {
            foreach ($labelArray as $key => $val) {
                if ($val) {
                    $labelSql = $this->field('id')->where('name', 'like', '%' . $val . '%');
                    if ($seller_id) {
                        $labelSql->where('seller_id', '=', $seller_id);
                    }
                    $id = $labelSql->find();
                    if (!$id && $isForce) {
                        $iData['name']      = $val;
                        $iData['seller_id'] = $seller_id;
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
     * @param int $seller_id
     * @return array
     */
    public function getAllSelectLabel($ids, $model_name, $seller_id = 0)
    {
        $model      = model($model_name);
        $filter[]   = ['id', 'in', $ids];
        $filter[]   = ['seller_id', 'in', $seller_id];
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
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数丢失',
        ];
        if (!isset($data['ids'])) {
            return $result;
        }
        $labels = $ids = [];
        foreach ((array)$data['label'] as $key => $val) {
            $labels[$key]['name']  = $val['text'];
            $labels[$key]['style'] = $val['style'];
            if (isset($data['seller_id'])) {
                $labels[$key]['seller_id'] = $data['seller_id'];
            }
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
