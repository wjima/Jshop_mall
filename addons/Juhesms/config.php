<?php
// +----------------------------------------------------------------------
// | Author: lisgroup  https://github.com/lisgroup  407505297@qq.com
// +----------------------------------------------------------------------
/**
 * 聚合短信模板以及配置信息
 */
return [
    'key'=>[
        'title'=>'key',
        'value'=>'',
    ],
    'template' => [
        'value' => [
            'login'    => [
                'title' => '用户登录',
                'data'  => [
                    'title'    => [
                        'title' => '短信模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '验证码:#code#',
                        'variable'=>[
                            'code'=>'code'  //模板变量，对应系统变量名称
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '您正在登陆账号，验证码是#code#，请勿告诉他人。',
                    ],
                ],
            ],
            'reg'    => [
                'title' => '用户注册',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '验证码:#code#',
                        'variable'=>[
                            'code'=>'code'  //模板变量，对应系统变量名称
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '您正在注册账号，验证码是#code#，请勿告诉他人。',
                    ],
                ],
            ],
            'veri'    => [
                'title' => '用户验证',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '验证码:#code#',
                        'variable'=>[
                            'code'=>'code'  //模板变量，对应系统变量名称
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '您的验证码是#code#，请勿告诉他人。',
                    ],
                ],
            ],
            'create_order'    => [
                'title' => '下单成功',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '订单号 :#order_id#，总价 :#order_amount#，配送方式 :#ship_id#收货人手机 :#ship_mobile#，收货人地址 :#ship_addr#，收货人姓名 :#ship_name#',
                        'variable'=>[
                            'order_id'=>'order_id',  //模板变量，对应系统变量名称
                            'order_amount'=>'order_amount',
                            'ship_id'=>'ship_id',
                            'ship_mobile'=>'ship_mobile',
                            'ship_addr'=>'ship_addr',
                            'ship_name'=>'ship_name',
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '恭喜您，订单创建成功,祝您购物愉快。',
                    ],
                ],
            ],
            'order_payed'     => [
                'title' => '支付成功',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '订单号 :#order_id#,付款人 :#user_name#,付款时间 :#pay_time#,付款金额 :#money#',
                        'variable'=>[
                            'order_id'=>'order_id',  //模板变量，对应系统变量名称
                            'user_name'=>'user_name',
                            'pay_time'=>'pay_time',
                            'money'=>'money',
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '恭喜您，订单支付成功,祝您购物愉快。',
                    ],
                ],
            ],
            'remind_order_pay' => [
                'title' => '催付提醒',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '订单号:#order_id#',
                        'variable'=>[
                            'order_id'=>'order_id'
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '恭喜您，订单创建成功,祝您购物愉快。',
                    ],
                ],
            ],
            'delivery_notice' => [
                'title' => '订单发货',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '订单号:#order_id#,物流公司:#logistics_name#,物流单号:#ship_no#,收货人姓名:#ship_name#,收货人地址:#ship_address#,收货人手机:#ship_mobile#,备注:#memo#',
                        'variable'=>[
                            'order_id'=>'order_id',  //模板变量，对应系统变量名称
                            /*'ship_id'=>'ship_id',*/
                            'logistics_name'=>'logistics_name',
                            'ship_no'=>'logi_no',
                            'ship_name'=>'ship_name',
                            'ship_address'=>'ship_address',
                            'ship_mobile'=>'ship_mobile',
                            'memo'=>'memo',
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '恭喜您，订单创建成功,祝您购物愉快。',
                    ],
                ],
            ],
            'aftersales_pass' => [
                'title' => '售后审核通过',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '订单号:#order_id#,售后单号:#aftersales_id#,审核状态:#aftersales_status#,审核备注#mark#',
                        'variable'=>[
                            'order_id'=>'order_id',  //模板变量，对应系统变量名称
                            'aftersales_id'=>'aftersales_id',
                            'status'=>'aftersales_status',
                            'mark'=>'mark'
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '恭喜您，订单创建成功,祝您购物愉快。',
                    ],
                ],
            ],
            'refund_success'  => [
                'title' => '退款成功',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '订单号 :#order_id#，退款编号 :#refund_id#,退款金额 :#refund#',
                        'variable'=>[
                            'order_id'=>'source_id',  //模板变量，对应系统变量名称
                            'refund_id'=>'refund_id',
                            'refund'=>'money'
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '恭喜您，订单创建成功,祝您购物愉快。',
                    ],
                ],
            ],
            'seller_order_notice'=> [
                'title' => '卖家新订单通知',
                'data'  => [
                    'title'    => [
                        'title' => '模板ID',
                        'code'  => 'tpl_id',
                        'value' => '',
                    ],
                    'template' => [
                        'title'   => '模板变量',
                        'content' => '订单号 :#order_id#,付款人 :#user_name#,付款时间 :#pay_time#,付款金额 :#money#',
                        'variable'=>[
                            'order_id'=>'order_id',  //模板变量，对应系统变量名称
                            'user_name'=>'user_name',
                            'pay_time'=>'pay_time',
                            'money'=>'money',
                        ]
                    ],
                    'content'  => [
                        'title' => '模板内容',
                        'value' => '卖家您好，您有新的订单了，请及时处理。',
                    ],
                ],
            ],
        ],
    ],
];