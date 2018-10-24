<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class Worksheet extends common{

    function add($data){

        return $this->data($data)->allowField(true)->save();
    }
    //全部
    function sheetlist($seller_id,$page,$limit){

            $result = $this->field('id,job_num,create_time,type,title,status')
                ->where('seller_id','eq',$seller_id)
                ->page($page,$limit)
                ->order("id","desc")
                ->select();
            $count = $this->field('*')
                ->where('seller_id','eq',$seller_id)
                ->count();
            $re['count'] = $count;
            $re['data'] = $result;
            return $re;

    }
    //未处理
    function sheetlist1($seller_id,$sta,$page,$limit){

        $result = $this->field('id,job_num,create_time,type,title,status')
            ->where('seller_id','eq',$seller_id)
            ->where('status','eq',$sta)
            ->page($page,$limit)
            ->order("id","desc")
            ->select();
        $count = $this->field('*')
            ->where('seller_id','eq',$seller_id)
            ->where('status','eq',$sta)
            ->count();
        $re['count'] = $count;
        $re['data'] = $result;
        return $re;

    }
    //处理中2
    function sheetlist2($seller_id,$sta,$page,$limit){

        $result = $this->field('id,job_num,create_time,type,title,status')
            ->where('seller_id','eq',$seller_id)
            ->where('status','eq',$sta)
            ->page($page,$limit)
            ->order("id","desc")
            ->select();
        $count = $this->field('*')
            ->where('seller_id','eq',$seller_id)
            ->where('status','eq',$sta)
            ->where('status','eq',$sta)
            ->count();
        $re['count'] = $count;
        $re['data'] = $result;
        return $re;
    }
    //已处理
    function sheetlist3($seller_id,$sta,$page,$limit){

        $result = $this->field('id,job_num,create_time,type,title,status')
            ->where('seller_id','eq',$seller_id)
            ->where('status','eq',$sta)
            ->page($page,$limit)
            ->order("id","desc")
            ->select();
        $count = $this->field('*')
            ->where('seller_id','eq',$seller_id)
            ->where('status','eq',$sta)
            ->where('status','eq',$sta)
            ->count();
        $re['count'] = $count;
        $re['data'] = $result;
        return $re;
    }

    function wsdetail($id,$seller_id){
        $rel = $this->field('seller_id,job_num,create_time,type,title,status,content')
            ->where('id','eq',$id)
            ->find();

        if($rel['seller_id'] !=$seller_id){
            return '';
        }else{
            return $rel;
        }

    }

    //更改工单状态
    function updata($job_num){
        $result = $this->where('job_num','eq',$job_num)
            ->update(['status' => '已处理']);

        return $result;
    }
    //删除工单

    function del($id){
        $result = $this->where('id','eq',$id)
            ->delete();
        return $result;
    }

}