<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\model\GoodsParams;

/**
 * 商品参数
 * Class GoodsParams
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:10
 */
class GoodsTypeParams extends Common
{
    /**
     * 更新类型和参数
     * @param int $type_id
     * @param array $params_id
     * @return bool|\think\Collection
     */
    public function updateTypeParams($type_id = 0, $params_id = [])
    {
        if (!$type_id) {
            return false;
        }
        $typeParamsModel = $this->where(['type_id' => $type_id])->field('type_id');
        if ($typeParamsModel->select()) {
            $typeParamsModel->delete();
        }
        if ($params_id) {
            $iData = [];
            foreach ($params_id as $key => $val) {
                $iData[] = [
                    'type_id'   => $type_id,
                    'params_id' => $val
                ];
            }
            return $this->saveAll($iData);
        }
        return true;
    }

    /**
     * 获取类型关联参数
     * @param int $type_id
     * @return array|bool
     */
    public function getRelParams($type_id = 0)
    {
        if (!$type_id) {
            return false;
        }
        $data        = [];
        $paramsModel = new GoodsParams();
        $typeParams  = $this->where(['type_id' => $type_id])->select();

        if (!$typeParams->isEmpty()) {
            foreach ((array)$typeParams->toArray() as $k => $v) {
                $data[$k]['type_id']   = $v['type_id'];
                $data[$k]['params_id'] = $v['params_id'];
                $data[$k]['params']    = $paramsModel->getParamsInfo($v['params_id']);
            }
        }
        return $data;
    }

}
