<?php
/***
 * 一个操作最多只能有3个订阅消息，所以定义的时候，请约束数量
 */
return [
    'template' => [
        'title' => '模板配置',
        'value' => [
            'order' => [
                'type'=>'order',//哪个地方使用
                'title' => '下单通知',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '订单编号',
                        'code' => 'order_id'
                    ],
                    [
                        'title' => '订单金额',
                        'code' => 'order_amount'
                    ],
                    [
                        'title' => '收货人名',
                        'code' => 'ship_name'
                    ],
                    [
                        'title' => '收货电话',
                        'code' => 'ship_mobile'
                    ],
                    [
                        'title' => '收货地址',
                        'code' => 'ship_addr'
                    ]
                ]
            ],
            'remind' => [
                'type'=>'order',//哪个地方使用
                'title' => '催付通知',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '订单编号',
                        'code' => 'order_id'
                    ],
                    [
                        'title' => '订单金额',
                        'code' => 'order_amount'
                    ],
                    [
                        'title' => '下单时间',
                        'code' => 'ctime'
                    ]
                ]
            ],
            'cancel'=>[
                'type'=>'order',//哪个地方使用
                'title' => '取消订单',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '订单编号',
                        'code' => 'order_id'
                    ],
                    [
                        'title' => '下单时间',
                        'code' => 'ctime'
                    ],
                    [
                        'title' => '取消原因',
                        'code' => 'reason'
                    ],
                ]
            ],
            'pay' => [
                'type'=>'order',//哪个地方使用
                'title' => '支付通知',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '订单编号',
                        'code' => 'order_id'
                    ],
                    [
                        'title' => '支付金额',
                        'code' => 'money'
                    ],
                    [
                        'title' => '支付时间',
                        'code' => 'pay_time'
                    ]
                ]
            ],
            'ship' => [
                'type'=>'order',//哪个地方使用
                'title' => '发货通知',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '订单编号',
                        'code' => 'order_id'
                    ],
                    [
                        'title' => '快递公司',
                        'code' => 'logistics_name'
                    ],
                    [
                        'title' => '快递单号',
                        'code' => 'logi_no'
                    ]
                ]
            ],
            'after_sale' => [
                'type'=>'after',//申请售后时使用
                'title' => '售后通知',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '订单编号',
                        'code' => 'order_id'
                    ],
                    [
                        'title' => '订单金额',
                        'code' => 'order_amount'
                    ],
                    [
                        'title' => '售后单号',
                        'code' => 'aftersales_id'
                    ],
                    [
                        'title' => '售后状态',
                        'code' => 'aftersales_status'
                    ]
                ]
            ],
            'refund' => [
                'type'=>'after',//申请售后时使用
                'title' => '退款通知',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '订单编号',
                        'code' => 'source_id'
                    ],
                    [
                        'title' => '售后单号',
                        'code' => 'aftersales_id'
                    ],
                    [
                        'title' => '退款金额',
                        'code' => 'money'
                    ],
                    [
                        'title' => '退款方式',
                        'code' => 'payment_code'
                    ],
                    [
                        'title' => '退款时间',
                        'code' => 'ctime'
                    ]
                ]
            ]
        ]
    ],
    'api' => [
        'title' => '插件对外的接口都放到这里，接口请求的时候，会来此判断是否存在此控制器和方法,类似/config/api/api.php',
        'value' => [
            'api' => [      //控制器名称，可以随便起名，不过此控制器集成的基类一定要注意，不要写错了
                'code'   => 'Api',
                'method' => [
                    'closetip' => [
                        'code'     => 'closetip',
                        'is_login' => true
                    ],
                    'tmpl' => [
                        'code'     => 'tmpl',
                        'is_login' => true
                    ],
                    'settip' => [
                        'code'     => 'settip',
                        'is_login' => true
                    ],
                ]
            ]
        ]
    ]
];