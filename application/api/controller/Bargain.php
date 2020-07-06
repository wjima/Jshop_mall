<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\BargainRecord;
use Request;
use app\common\model\Bargain as bargainModel;

/**
 * 砍价接口
 * Class Bargain
 * @package app\api\controller
 */
class Bargain extends Api
{


    /**
     * 判断当前用户是否参与
     * @return array
     */
    public function add()
    {
        $bargain_id         = input('id/d', 0);
        $bargainRecordModel = new BargainRecord();
        return $bargainRecordModel->addRecord($bargain_id, $this->userId);
    }

    /**
     * 获取活动列表接口
     * @return array
     */
    public function getList()
    {
        $return_data      = [
            'status' => true,
            'msg'    => '查询成功',
            'data'   => []
        ];
        $bargainModel     = new bargainModel();
        $params           = input('param.');
        $params['status'] = $bargainModel::STATUS_ON;
        $list             = $bargainModel->tableData($params, true);

        if ($list) {
            $return_data['status']        = true;
            $return_data['data']['list']  = $list['data'];
            $return_data['data']['count'] = $list['count'];
            $return_data['msg']           = '查询成功';
        }
        return $return_data;
    }

    /**
     * 获取活动商品详情
     * @return array
     */
    public function getBargainDetial()
    {
        $bargain_id  = input('id/d', 0);
        $record_id   = input('record_id/d', 0); //发起人id
        $type        = input('type', 1); //自己访问还是别人访问

        if (!$bargain_id) {
            // $return_data['msg']    = error_code(10003,true);
            // $return_data['status'] = false;
            return error_code(10003);
        }
        $bargainModel = new bargainModel();
        return $bargainModel->getBargainDetial($bargain_id, $type, $record_id, $this->userId);
    }

    /**
     * 砍一刀
     * @return array
     */
    public function doBargain()
    {
        $bargain_id = input('id/d', 0);
        $record_id  = input('record_id/d', 0);
        $type       = input('type', 1); //默认发起砍价

        $bargainModel = new bargainModel();
        return $bargainModel->doBargain($bargain_id, $type, $this->userId, $record_id);
    }

    /**
     * 查看当前砍价活动的金额
     * @return array
     */
    public function getBargainPrice()
    {
        $return_data = [
            'status' => true,
            'msg'    => '',
            'data'   => [],
        ];
        $bargain_id  = input('id/d', 0);
        $record_id   = input('record_id/d', 0);
        $type        = input('type', 1); //默认发起砍价
        if ($type == '2') {
            $record_id = $this->userId;
        }
        $bargainModel        = new bargainModel();
        $return_data['data'] = $bargainModel->geBargainPrice($bargain_id, $record_id);
        return $return_data;
    }

    /**
     * 砍价记录
     * @return array
     */
    public function getUserBargainLog()
    {

        $page               = input('page', 1);
        $limit              = input('limit', 10);
        $bargainRecordModel = new BargainRecord();
        $where              = [];
        $where[]            = ['user_id', '=', $this->userId];
        return $bargainRecordModel->getList('*', $where, ['ctime' => 'desc'], $page, $limit);
    }

    /**
     * 取消砍价活动
     * @return mixed
     */
    public function cancleBargain()
    {
        $record_id          = input('record_id/d', 0);
        $bargainRecordModel = new BargainRecord();
        return $bargainRecordModel->cancleBargain($record_id, $this->userId);
    }
}
