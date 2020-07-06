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
        $params['limit']     = input('limit','0');//每页数量
        $params['page'] = input('param.page', 1);
        $params['type'] = $type;
        $data = $promotion->tableGroupData($params,true);
        $return_data['data']['list'] = $data['data'];
        $return_data['data']['count'] = $data['count'];
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
            'msg'    => '',
            'data'   => [],
        ];
        $goods_id    = input('id/d', 0);
        $group_id    = input('group_id/d', 0);//活动id
        $token       = input('token', '');//token值 会员登录后传

        if (!$goods_id) {

            return error_code(10051);
        }
        $promotion   = new Promotion();
        $returnGoods = $promotion->getGroupDetial($goods_id, $token,'*',$group_id);
        if ($returnGoods['status']) {
            $return_data ['msg']  = '查询成功';
            $return_data ['data'] = $returnGoods['data'];
        } else {
            $return_data['msg']    = $returnGoods['msg'];
            $return_data['status'] = false;
        }
        return $return_data;
    }


    /**
     * 获取货品信息
     * @return array|mixed
     */
    public function getProductInfo()
    {
        if (!input('?param.id')) {
            return error_code(10000);
        }
        $group_id = input('group_id/d', 0);//活动id
        $token    = input('token', '');//token值 会员登录后传
        $type     = input('type', '');//token值 会员登录后传

        $promotion = new Promotion();
        return $promotion->getProductInfo(input('param.id'), $token, $type, $group_id);
    }
}