<?php
namespace app\api\controller;
use app\common\controller\Api;
use Request;

/**
 * 购物车
 * Class Cart
 * @package app\api\controller
 * @author keinx
 */
class Cart extends Api
{
    /**
     * 单个加入购物车
     * @return array
     */
    public function add()
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];

        if(!input("?param.product_id")){
            $result['msg'] = '请输入货品id';
            return $result;
        }
        if(!input("?param.nums")){
            $result['msg'] = '请输入货品数量';
            return $result;
        }


        $type = input('param.type',1);          //1是累加，2是覆盖




        return model('common/Cart')->add($this->userId,input('product_id'),input('nums'),$type);

    }


    /**
     * 移除购物车
     * @param array ids
     * @return array
     */
    public function del()
    {
        $ids = input('param.ids',"");
        $user_id = $this->userId;

        $result = model('common/Cart')->del($user_id,$ids);
        if($result)
        {
            $return_data = array(
                'status' => true,
                'msg' => '移除购物车成功',
                'data' => $result
            );
        }
        else
        {
            $return_data = array(
                'status' => false,
                'msg' => '移除购物车失败',
                'data' => $result
            );
        }
        return $return_data;
    }


    /**
     * 获取购物车列表
     * @return mixed
     */
    public function getList()
    {
        return (model('common/Cart')->info($this->userId, input('ids',""), input('display', ''), input('area_id', false), input('point', 0), input('coupon_code', '')));
    }


    /**
     * 设置购物车数量接口
     * @return mixed
     */
    public function setNums()
    {
        $input['user_id'] = $this->userId;
        $input['id'] = input('id');
        $input['nums'] = input('nums', 1);
        if($input['nums'] <= 0)
        {
            $input['nums'] = 1;
        }
        $result = model('common/Cart')->setNums($input);
        if(!$result['status']){
            return $result;
        }
        return model('common/Cart')->info($this->userId,  input('param.ids',""));

    }
}