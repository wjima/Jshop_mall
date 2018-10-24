<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Db;
class Wsdetail extends common{


    function adddetail($record){

        $status =   $num = Db::name("worksheet")
            ->field("*")
            ->where('job_num','eq',$record['job_num'])
            ->where("status",'eq','已处理')
            ->find();

        if(!$status){
            return $this->data($record)->allowField(true)->save();
        }else{
            return '';
        }

    }

    function getinfo($seller_id,$id){

        $num = Db::name("worksheet")
            ->field("job_num")
            ->where('id','eq',$id)
            ->find();

        $result = $this->field("id,seller_id,event,create_time,pic_path")
            ->where('job_num','eq',$num['job_num'])
            ->where('seller_id','in',[0,$seller_id])
            ->select();

        return $result;
    }
}