<?php


namespace addons\WelfarePro\model;


use app\common\model\Common;

class WelfareproCouponLog extends Common
{
    public function couponOver($c_id,$tj_user_id,$maxNums){
        //是否有该推荐人该活动的数据
        $info = $this->where('c_id',$c_id)->where('tj_user_id',$tj_user_id)->find();
        if($info && $info['nums'] < $maxNums) {
            return true;
        }
        return false;
    }
}