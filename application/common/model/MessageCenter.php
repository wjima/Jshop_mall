<?php
namespace app\common\model;

/**
 * 消息中心，所有有消息编码的，都走这里发送
 * Class MessageCenter
 * @package app\common\model
 * @author sin
 */
class MessageCenter extends Common
{
    const SEND_TRUE = 1;        //发送
    const SEND_FALSE= 2;        //不发送


    //商户类型，只有商户类型才存表
    public $seller_tpl = [
        'create_order' => [
            'name' => '下单成功时',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE,
            'wx_tpl_message' => self::SEND_FALSE,           //当type是2的时候，这个字段可能没用
        ],
        'order_payed' => [
            'name' => '订单支付成功时',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE,
            'wx_tpl_message' => self::SEND_FALSE,           //当type是2的时候，这个字段可能没用
        ],
        'remind_order_pay' =>[                              //订单快关闭的时候，提醒用户去支付
            'name' => '订单支付提醒',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE,
            'wx_tpl_message' => self::SEND_FALSE,
        ],
        'delivery_notice' =>[                              //订单快关闭的时候，提醒用户去支付
            'name' => '订单发货通知',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE,
            'wx_tpl_message' => self::SEND_FALSE,
        ],
        'aftersales_pass' =>[                              //售后通过
            'name' => '售后确认通过',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE,
            'wx_tpl_message' => self::SEND_FALSE,
        ],
        'refund_success' =>[                              //退款成功
            'name' => '用户退款成功通知',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE,
            'wx_tpl_message' => self::SEND_FALSE,
        ],
    ];

    //平台类型消息
    public $platform_tpl = [
        'aftersales_add' => [                              //当用户提交售后单的时候，发送
            'name' => '新售后单通知',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE
        ],
        'wx_authorize_notice' => [                              //微信授权通知，成功和失败都通过此发送
            'name' => '微信授权通过时',
            'sms' => self::SEND_TRUE,
            'message' => self::SEND_TRUE
        ],
        'wxapp_tpl_examine_notice' => [                              //微信小程序模板审核结果通知，成功和失败都通过此发送
            'name' => '微信小程序模板审核结果',
            'sms' => self::SEND_TRUE,
            'message' => self::SEND_TRUE
        ],
        'platform_expire' => [                              //平台到期通知
            'name' => '平台到期通知',
            'sms' => self::SEND_TRUE,
            'message' => self::SEND_TRUE
        ],
        'modify_domain' => [                              //平台到期通知
            'name' => '域名设置通知',
            'sms' => self::SEND_FALSE,
            'message' => self::SEND_TRUE
        ],
    ];

    //取得商户消息配置参数,返回layui的格式
    public function getSellerTpl($seller_id)
    {
        //取商户的消息配置信息
        $list = $this->where(array('seller_id' => $seller_id))->select();
        $data = [];
        foreach($this->seller_tpl as $k => $v){
            $v['code'] = $k;
            foreach($list as $vv){
                if($vv['code'] == $k){
                    $v['sms'] = $vv['sms'];
                    $v['message'] = $vv['message'];
                    $v['wx_tpl_message'] = $vv['wx_tpl_message'];
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

    /**
     * 商户发送消息给用户
     * @param $seller_id            商户id
     * @param $user_id              接受者id
     * @param $code                 模板编码
     * @param $params               参数
     */
    public function sendSellerMessage($seller_id,$user_id,$code,$params)
    {
        if(!isset($this->seller_tpl[$code])){
            return error_code(10100);
        }
        $info = $this->seller_tpl[$code];

        //判断用户是否有设置
        $conf = $this->where(['seller_id'=> $seller_id,'code'=>$code])->find();
        if($conf){
            $info['sms'] = $conf['sms'];
            $info['message'] = $conf['message'];
            $info['wx_tpl_message'] = $conf['wx_tpl_message'];
        }
        if($info['sms'] == self::SEND_TRUE){
            //判断短信是否够,如果够，就去发
            $mobile = get_user_info($user_id,'mobile');
            if($mobile){
                $smsModel = new Sms();
                $smsModel->send($mobile,$code,$params);
            }
        }
        //站内消息
        if($info['message'] == self::SEND_TRUE){
            $messageModel = new Message();
            $messageModel->send($user_id,$code,$params,$seller_id);
        }
        if($info['wx_tpl_message'] == self::SEND_TRUE){
            //微信模板消息【小程序，公众号都走这里】
            hook('sendwxmessage', ['params' => [
                'user_id'   => $user_id,
                'code'      => $code,
                'params'    => $params,
                'seller_id' => $seller_id,
            ]]);
        }



        return [
            'status' => true,
            'data' => '',
            'msg' => ''
        ];

    }

    /**
     * 平台发送消息的接口
     * @param $seller_id          接受者的$seller_id 如果是商户的话，这里要传seller_id而不是user_id
     * @param $code             消息编码
     * @param $params           参数
     * @return array
     */
    public function sendPlatformMessage($seller_id,$code,$params)
    {
        $result = [
            'status' => false,
            'data' => '',
            'msg' => ''
        ];

       if(!isset($this->platform_tpl[$code])){
            return error_code(10100);
        }
        $info = $this->platform_tpl[$code];

        $user_id = getSellerInfoById($seller_id,'user_id');
        if($user_id == ""){
            $result['msg'] = "店铺没有找到对应用户";
            return $result;
        }

        if($info['sms'] == self::SEND_TRUE){
            $mobile = get_user_info($user_id,'mobile');
            if($mobile){
                $smsModel = new Sms();
                $smsModel->send($mobile,$code,$params);
            }

        }
        if($info['message'] == self::SEND_TRUE){
            $messageModel = new Message();
            $messageModel->send($user_id,$code,$params,$seller_id);
        }
        $result['status'] = true;

        return $result;
    }

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        $re['sql'] = $this->getLastSql();

        return $re;
    }

    /**
     * 根据输入的查询条件，返回所需要的where
     * @author sin
     * @param $post
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['seller_id']) && $post['seller_id'] != ""){
            $where[] = ['seller_id', 'eq', $post['seller_id']];
        }
        if(isset($post['code']) && $post['code'] != ""){
            $where[] = ['code', 'eq', $post['code']];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = [];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        foreach($list as $k => $v){
            if(isset($this->seller_tpl[$v['code']])){
                $list[$k]['code_name'] = $this->seller_tpl[$v['code']]['name'];
            }else{
                $list[$k]['code_name'] = self::SEND_FALSE;
            }


        }
        return $list;
    }
}