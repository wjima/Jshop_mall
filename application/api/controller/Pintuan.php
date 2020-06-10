<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\PintuanGoods;
use app\common\model\PintuanRecord;
use app\common\model\PintuanRule;

/**
 * 拼团
 * Class Pintuan
 * @package app\api\controller
 */
class Pintuan extends Api
{
    public function getList()
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];

        $pintuanRuleModel = new PintuanRule();
        $goodsModel = new PintuanGoods();
        $input = input('param.');
        $data = $goodsModel->tableData($input,true);

        $result['data']['list'] = $data['data'];
        $result['data']['count'] = $data['count'];
        return $result;
    }

    /**
     * 获取拼团商品信息
     * @return array|mixed
     */
    public function getGoodsInfo(){
        if(!input('?param.id')){
            return error_code(10000);
        }

        $goodsModel      = new PintuanGoods();
        return $goodsModel->getGoodsInfo(input('param.id'));

    }

    /**
     * 获取货品信息
     * @return array|mixed
     */
    public function getProductInfo()
    {
        if(!input('?param.id')){
            return error_code(10000);
        }

        $goodsModel      = new PintuanGoods();
        return $goodsModel->getProductInfo(input('param.id'));
    }

    public function pintuanRecord(){
        if(!input('?param.rule_id')){
            return error_code(15604);
        }
        $rule_id = input('param.rule_id');

        if(!input('?param.goods_id')){
            return error_code(12009);
        }
        $goods_id = input('param.goods_id');

        $status = input('param.status',0);


        $page      = input('param.page', 1);
        $limit     = input('param.limit', config('jshop.page_limit'));
        $recordModel = new PintuanRecord();
        return $recordModel->getRecord($rule_id,$goods_id,$status,$page,$limit);
    }


    /**
     * 根据订单id取拼团信息，用在订单详情页
     * @return array|mixed
     */
    public function pintuanTeam(){
        if(!input('?param.order_id') && !input('?param.team_id')){
            return error_code(15606);
        }

        $recordModel = new PintuanRecord();
        return $recordModel->getTeamList(input('param.team_id',0),input('param.order_id',0));
    }

}