<?php

namespace addons\MiniManage\model;
use org\Curl;
use org\QRcode;
use think\facade\Cache;
use think\Model;

class AdminMessageCenter extends Model
{
    //商户类型，只有商户类型才存表
    public $tpl = [
        'create_order' => [
            'name' => '下单成功时',
            'message' => 2,
            'wx_tpl_message' => 2,           //当type是2的时候，这个字段可能没用
            'overall' => 2
        ],
        'order_payed' => [
            'name' => '订单支付成功时',
            'message' => 2,
            'wx_tpl_message' => 2,           //当type是2的时候，这个字段可能没用
            'overall' => 1
        ],
        'user_register' => [
            'name' => '新用户注册时',
            'message' =>2,
            'wx_tpl_message' => 2,           //当type是2的时候，这个字段可能没用
            'overall' => 2
        ],
        'after_order' => [
            'name' => '用户申请售后时',
            'message' =>2,
            'wx_tpl_message' => 2,           //当type是2的时候，这个字段可能没用
            'overall' => 2
        ],
        'after_ship' => [
            'name' => '用户退货发货时',
            'message' =>2,
            'wx_tpl_message' => 2,           //当type是2的时候，这个字段可能没用
            'overall' => 2
        ],

    ];
    public function getlist(){
       $list = $this->select();
        $data["create_order"] = 2;
        $data["order_payed"] = 2;
        $data["user_register"] = 2;
        $data["all"] = 2;
        $data["after_order"] = 2;
        $data["after_ship"] = 2;
       foreach ($list as $k=>$v){
           if($v["code"] == 'create_order'){
               $data["create_order"] = $v["overall"];
           }
           if($v["code"] == 'order_payed'){
               $data["order_payed"] = $v["overall"];
           }
           if($v["code"] == 'user_register'){
               $data["user_register"] = $v["overall"];
           }
           if($v["code"] == 'after_order'){
               $data["after_order"] = $v["overall"];
           }
           if($v["code"] == 'after_ship'){
               $data["after_ship"] = $v["overall"];
           }
       }
       foreach ($data as $k=>$v){
           if($v == 1){
               $data["all"] = 1;
           }
       }
       return $data;
    }
    public function toedit($change,$code){
        if($code == "all"){
            $res = $this->where("id","neq",0)->update(["overall"=>$change]);
            return true;
        }
        $info = $this->where("code","eq",$code)->find();
        if(!$info){
            $data["code"] = "$code";
            $data["overall"] = $change;
            $data["message"] = 1;
            $data["wx_tpl_message"] = 2;
            $res = $this->insert($data);
        }else{
            $res = $this->where("code","eq",$code)->update(["overall"=>$change]);
        }
        if($res){
            return true;
        }else{
            return false;
        }
    }
    //取得商户消息配置参数,返回layui的格式
    public function getTpl()
    {
        //取商户的消息配置信息
        $list = $this->select();
        $data = [];
        foreach($this->tpl as $k => $v){
            $v['code'] = $k;
            foreach($list as $vv){
                if($vv['code'] == $k){
                    $v['wx_tpl_message'] = $vv['wx_tpl_message'];
                    $v['message'] = $vv['message'];
                    $v['overall'] = $vv['overall'];
                }
            }
            $data[] = $v;
        }

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = count($data);
        $re['data'] = $data;
        return $re;
    }
    public function getqr(){
        $str = request()->domain();
        $url = "https://jihainet.com/b2c/Callback/getqr";
        $data["url"] = $str;
        $curl = new Curl();
        $res = $curl->post($url,$data);
        $res = json_decode($res,true);
        return $res["data"]["url"];
    }

}