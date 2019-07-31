<?php
namespace app\api\controller;
use app\common\controller\Api;
use Request;
use app\common\model\Promotion;

/**
 * 团购秒杀活动接口
 * Class Group
 * @package app\api\controller
 */
class Group extends Api
{
    /**
     * 获取活动列表接口
     * @return array
     */
    public function getList(){
        $return_data = [
            'status' => true,
            'msg'    => '查询成功',
            'data'   => []
        ];
        $promotion = new Promotion();
        $type      = input('type',$promotion::TYPE_GROUP);//默认团购
        $stime     = input('stime','0');//开始时间
        $etime     = input('etime','0');//结束时间
        $params['type'] = $type;
        if($stime){
            $params['stime'] = $stime;
        }
        if($etime){
            $params['etime'] = $etime;
        }
        $list = $promotion->getGroupList($params);
        if($list){
            $return_data['status'] = true;
            $return_data['data'] = $list;
            $return_data['msg'] = '查询成功';
        }

        return $return_data;
    }

    /**
     * 获取活动商品详情
     * @return array
     */
    public function getGoodsDetial()
    {
        $return_data = [
            'status' => true,
            'msg'    => '查询失败',
            'data'   => [],
        ];
        $goods_id    = input('id/d', 0);
        $token       = input('token', '');//token值 会员登录后传

        if (!$goods_id) {
            $return_data['msg']    = '关键参数缺失';
            $return_data['status'] = false;
            return $return_data;
        }
        $promotion   = new Promotion();
        $returnGoods = $promotion->getGroupDetial($goods_id, $token);
        if ($returnGoods['status']) {
            $return_data ['msg']  = '查询成功';
            $return_data ['data'] = $returnGoods['data'];
        } else {
            $return_data['msg']    = $returnGoods['msg'];
            $return_data['status'] = false;
        }
        return $return_data;
    }
}