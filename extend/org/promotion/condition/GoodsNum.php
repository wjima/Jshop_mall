<?php

namespace org\promotion\condition;


class GoodsNum implements Condition
{
    function jshop($params, &$cart, $promotionInfo){
        $re = false;

        //统计商品数量
        $goods = [];
        foreach($cart['list'] as $k => $v){
            if(isset($v['products']['promotion_list'][$promotionInfo['id']]) && $v['products']['promotion_list'][$promotionInfo['id']] && $v['is_select']){
                if(!isset($goods[$v['products']['goods_id']])){
                    $goods[$v['products']['goods_id']] = 0;
                }
                $goods[$v['products']['goods_id']] += $v['nums'];
            }
        }

        foreach($goods as $goods_id => $nums){
            foreach($cart['list'] as $k => &$v){
                if(isset($v['products']['promotion_list'][$promotionInfo['id']]) && $v['products']['promotion_list'][$promotionInfo['id']] && $v['products']['goods_id'] == $goods_id){
                    if($nums >= $params['num']){
                        if(!$re){
                            $re = true;
                        }
                    }else{
                        $v['products']['promotion_list'][$promotionInfo['id']] = false;
                    }
                }
            }
        }
        return $re;
    }

    function manageCheck($params){
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];
        if($params['num'] == '' || $params['num'] == '0'){
            $result['msg'] = "请输入数量";
            return $result;
        }
        if($params['num'] < 2){
            $result['msg'] = "请输入大于2的整数";
            return $result;
        }

        $result['status'] = true;
        return $result;
    }

    function getMsg($params)
    {
        return "购买所有商品";
    }

}
