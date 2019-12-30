<?php
return [
    'template' => [
        'title' => '模板配置',
        'value' => [
            'order' => [
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
            'cancel' => [
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
            'pay' => [
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
                'title' => '发货通知',
                'data' => [
                    [
                        'title' => '模板ID',
                        'code' => 'template_id'
                    ],
                    [
                        'title' => '快递公司',
                        'code' => 'ship_name'
                    ],
                    [
                        'title' => '快递编号',
                        'code' => 'logi_no'
                    ]
                ]
            ],
            'after_sale' => [
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
    ]
];