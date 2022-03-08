<?php
/**
 * 物流公司
 */

namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\Logistics;
use app\common\model\Ship as ShipModel;
use Request;
use \app\common\model\Area;

class Ship extends Manage
{


    public function index()
    {
        if (Request::isAjax()) {
            $shiModel = new ShipModel();
            $filter   = input('request.');
            return $shiModel->tableData($filter);
        }
        return $this->fetch();
    }


    public function add()
    {

        $logisticsModel = new Logistics();
        $logisticsList  = $logisticsModel->getAll();
        $this->assign('logisticsList', $logisticsList);
        if (Request::isPost()) {
            $return_data = [
                'status' => false,
                'msg'    => '',
                'data'   => '',
            ];

            $type     = input('type/d', '1');
            $area_fee = [];
            if ($type == ShipModel::TYPE_PART) {
                $area_id                 = input('area_id/a', []);
                $firstunit_area_price    = input('firstunit_area_price/a', []);
                $continueunit_area_price = input('continueunit_area_price/a', []);
                foreach ($area_id as $key => $val) {
                    if ($val) {
                        //$val = htmlspecialchars_decode($val);
                        $area_data                                 = json_decode($val, true);
                        $areaids                                   = $this->getAreaIds($area_data);//取出所有id
                        $area_fee[$key]['area_value']              = $val;
                        $area_fee[$key]['area']                    = implode($areaids, ',');
                        $area_fee[$key]['firstunit_area_price']    = $firstunit_area_price[$key];
                        $area_fee[$key]['continueunit_area_price'] = $continueunit_area_price[$key];
                    }
                }
                if (count($area_fee) <= 0) {
//                    $return_data['msg'] = '请选择配送地区';
                    return error_code(13316);
                }
            }
            $status = input('post.status');
            $status = ($status == 'on') ? ShipModel::STATUS_YES : ShipModel::STATUS_NO;
            //存储添加内容
            $data     = [
                'name'               => input('post.name'),
                'logi_code'          => input('post.logi_code'),
                'free_postage'       => input('post.free_postage', '2'),
                'has_cod'            => input('post.has_cod', '1'),
                'is_def'             => input('post.is_def', '2'),
                'status'             => $status,
                'firstunit'          => input('post.firstunit', '500'),
                'continueunit'       => input('post.continueunit', '500'),
                'firstunit_price'    => input('post.firstunit_price', '0'),
                'continueunit_price' => input('post.continueunit_price', '0'),
                'type'               => $type,
                'def_area_fee'       => input('post.def_area_fee', '1'),
                'sort'               => input('post.sort'),
                'goodsmoney'         => input('post.goodsmoney'),
                'area_fee'           => $area_fee,
            ];
            $shiModel = new ShipModel();
            $result   = $shiModel->add($data);
            if ($result['status'] !== false) {
                $return_data = [
                    'status' => true,
                    'msg'    => '添加成功',
                    'data'   => $result,
                ];
            } else {
                $return_data['msg'] = $result['msg'];
            }
            return $return_data;
        }
        return $this->fetch('add');
    }


    public function edit()
    {
        $logisticsModel = new Logistics();
        $logisticsList  = $logisticsModel->getAll();
        $this->assign('logisticsList', $logisticsList);
        $shiModel = new ShipModel();
        if (Request::isPost()) {
            //保存编辑后的数据
            $return_data =  error_code(10004);
            $id          = input('post.id/d', '0');
            if (!$id) {
                $return_data['msg'] = error_code(10004,true);
                return $return_data;
            }
            $type     = input('type/d', '1');
            $area_fee = [];
            if ($type == ShipModel::TYPE_PART) {
                $area_id                 = input('area_id/a', []);
                $firstunit_area_price    = input('firstunit_area_price/a', []);
                $continueunit_area_price = input('continueunit_area_price/a', []);

                foreach ($area_id as $key => $val) {
                    if ($val) {
                        //$val = htmlspecialchars_decode($val);
                        $area_data                                 = json_decode($val, true);
                        $areaids                                   = $this->getAreaIds($area_data);//取出所有id
                        $area_fee[$key]['area_value']              = $val;
                        $area_fee[$key]['area']                    = implode($areaids, ',');
                        $area_fee[$key]['firstunit_area_price']    = $firstunit_area_price[$key];
                        $area_fee[$key]['continueunit_area_price'] = $continueunit_area_price[$key];
                    }
                }
                if (count($area_fee) <= 0) {
//                    $return_data['msg'] = '请选择配送地区';
                    return error_code(13316);
                }
            }
            $status = input('post.status');
            $status = ($status == 'on') ? ShipModel::STATUS_YES : ShipModel::STATUS_NO;
            //存储添加内容
            $data     = [
                'id'                 => $id,
                'name'               => input('post.name'),
                'logi_code'          => input('post.logi_code'),
                'free_postage'       => input('post.free_postage', '2'),
                'has_cod'            => input('post.has_cod', '1'),
                'is_def'             => input('post.is_def', '2'),
                'status'             => $status,
                'firstunit'          => input('post.firstunit', '500'),
                'continueunit'       => input('post.continueunit', '500'),
                'firstunit_price'    => input('post.firstunit_price', '0'),
                'continueunit_price' => input('post.continueunit_price', '0'),
                'type'               => $type,
                'def_area_fee'       => input('post.def_area_fee', '1'),
                'sort'               => input('post.sort'),
                'goodsmoney'         => input('post.goodsmoney'),
                'area_fee'           => $area_fee,
            ];
            $shiModel = new ShipModel();
            $result   = $shiModel->toSave($data, $id);
            if ($result['status'] !== false) {
                $return_data = [
                    'status' => true,
                    'msg'    => '保存成功',
                    'data'   => $result,
                ];
            } else {
                $return_data['msg'] = $result['msg'];
            }
            return $return_data;

        }
        $filter = [
            'id' => input('param.id/d'),
        ];
        $data   = $shiModel->getInfo($filter);
        $this->assign('data', $data);
        return $this->fetch('edit');
    }


    public function del()
    {
        $return_data =  error_code(10023);
        $shiModel    = new ShipModel();
        $id          = input('post.id/d');
        $filter      = [
            'id' => $id,
        ];
        $res         = $shiModel->where($filter)->delete();
        if (!$res) {
            return $return_data;
        }
        $return_data['msg']    = '删除成功';
        $return_data['status'] = true;
        return $return_data;
    }

    public function getArea()
    {
        $return_data    = [
            'code' => 0,
            'msg'  => '获取成功',
            'data' => [],
        ];
        $parent_id      = input('nodeId', '0');
        $areas          = input('ids/s', '');//当前所有选择的节点
        $currentChecked = input('ischecked', '');//当前节点状态，0未选中，1全选，2半选
        $checked        = [];   //组装新数据
        if ($areas != '') {
            $areas = json_decode($areas, true);
            if (is_array($areas) && $areas) {
                foreach ($areas as $key => $val) {
                    $checked[$val['id']] = $val;
                }
            }
        }
        $area     = new Area();
        $areaList = $area->getTreeArea($checked, $parent_id, $currentChecked);
        if ($areaList['status']) {
            $return_data['data'] = $areaList['data'];
        }
        return $return_data;
    }


    /**
     * 获取所有地区id
     */
    private function getAreaIds($areaids = '')
    {
        $ids       = [];
        $areaModel = new Area();
        foreach ($areaids as $key => $val) {
            $ids[] = $val['id'];
            if ($val['ischecked'] == '1') {//全选中
                if ($val['pid'] <= 0) {//一级
                    $areaModel->getAllChildArea($val['id'], $ids);
                }
            }
        }
        $ids = array_unique($ids);
        return $ids;
    }


}