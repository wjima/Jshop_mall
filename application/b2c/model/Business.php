<?php

namespace app\b2c\model;

use think\Model;

class Business extends Model{

    public function addData($data)
    {
        $result = ['status' => true,'msg' => '','data'=>''];

        $rel = $this->where('phone',$data['phone'])->find();

        if( empty($rel) ){
            $business = $this->allowField(true)->save($data);

            if ( false != $business)
            {
                model('common/Sms')->send('15237211882','common',['tpl'=>'新商户 '.$data['name'].' 报名申请入驻,手机号为 '.$data['phone'].' 请尽快处理!']);
                $result['msg'] = '报名成功,我们会在24小时之内跟您取得联系';
            }else{
                $result['status'] = false;
                $result['msg'] = '提交失败请重试!';
            }
        }else{

            $result['status'] = false;
            $result['msg'] = '已经报名过了,勿重复报名';
        }

        return $result;
    }

    /**
     * 获取报名人数
     * @return int|string
     */
    public function dataCount()
    {
        return $this->count();
    }


}