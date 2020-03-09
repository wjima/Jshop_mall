<?php

return [
    'AppID' => [
        'title' => 'AppID',
        'value' => '',
    ],
    'AppKey' => [
        'title' => 'AppKey',
        'value' => '',
    ],
    'SignName' => [
        'title' => '短信签名',
        'value' => '',
    ],
    'template' => [
        'title' => '短信模板',
        'value' => [
            'login' => [
                'title' => '用户登录',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'code' => [
                                'name' => 'code',
                                'description' => '验证码',
                                'order' => '1'
                            ]
                        ]
                    ]
                ]
            ],
            'reg' => [
                'title' => '用户注册',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'code' => [
                                'name' => 'code',
                                'description' => '验证码',
                                'order' => '1'
                            ]
                        ]
                    ]
                ]
            ],
            'veri' => [
                'title' => '用户验证',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'code' => [
                                'name' => 'code',
                                'description' => '验证码',
                                'order' => '1'
                            ]
                        ]
                    ]
                ]
            ],
            'create_order' => [
                'title' => '下单成功',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'order_id' => [
                                'name' => 'order_id',
                                'description' => '订单号',
                                'order' => ''
                            ],
                            'order_amount' => [
                                'name' => 'order_amount',
                                'description' => '总价',
                                'order' => ''
                            ],
                            'ship_id' => [
                                'name' => 'ship_id',
                                'description' => '配送方式',
                                'order' => ''
                            ],
                            'ship_mobile' => [
                                'name' => 'ship_mobile',
                                'description' => '收货人手机',
                                'order' => ''
                            ],
                            'ship_addr' => [
                                'name' => 'ship_addr',
                                'description' => '收货人地址',
                                'order' => ''
                            ],
                            'ship_name' => [
                                'name' => 'ship_name',
                                'description' => '收货人姓名',
                                'order' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'order_payed' => [
                'title' => '支付成功',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'order_id' => [
                                'name' => 'order_id',
                                'description' => '订单号',
                                'order' => ''
                            ],
                            'user_name' => [
                                'name' => 'user_name',
                                'description' => '付款人',
                                'order' => ''
                            ],
                            'pay_time' => [
                                'name' => 'pay_time',
                                'description' => '付款时间',
                                'order' => ''
                            ],
                            'money' => [
                                'name' => 'money',
                                'description' => '付款金额',
                                'order' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'remind_order_pay' => [
                'title' => '催付提醒',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'order_id' => [
                                'name' => 'order_id',
                                'description' => '订单号',
                                'order' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'delivery_notice' => [
                'title' => '订单发货',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'order_id' => [
                                'name' => 'order_id',
                                'description' => '订单号',
                                'order' => ''
                            ],
                            /*'ship_id' => [
                                'name' => 'ship_id',
                                'description' => '配送方式',
                                'order' => ''
                            ],*/
                            'logistics_name' => [
                                'name' => 'logistics_name',
                                'description' => '物流公司',
                                'order' => ''
                            ],
                            'ship_no' => [
                                'name' => 'logi_no',
                                'description' => '物流单号',
                                'order' => ''
                            ],
                            'ship_name' => [
                                'name' => 'ship_name',
                                'description' => '收货人姓名',
                                'order' => ''
                            ],
                            'ship_address' => [
                                'name' => 'ship_address',
                                'description' => '收货人地址',
                                'order' => ''
                            ],
                            'ship_mobile' => [
                                'name' => 'ship_mobile',
                                'description' => '收货人手机',
                                'order' => ''
                            ],
                            'memo' => [
                                'name' => 'memo',
                                'description' => '备注',
                                'order' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'aftersales_pass' => [
                'title' => '售后审核通过',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'order_id' => [
                                'name' => 'order_id',
                                'description' => '订单号',
                                'order' => ''
                            ],
                            'aftersales_id' => [
                                'name' => 'aftersales_id',
                                'description' => '售后单号',
                                'order' => ''
                            ],
                            'status' => [
                                'name' => 'aftersales_status',
                                'description' => '审核状态',
                                'order' => ''
                            ],
                            'mark' => [
                                'name' => 'mark',
                                'description' => '审核备注$',
                                'order' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'refund_success' => [
                'title' => '退款成功',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'order_id' => [
                                'name' => 'source_id',
                                'description' => '订单号',
                                'order' => ''
                            ],
                            'refund_id' => [
                                'name' => 'refund_id',
                                'description' => '退款编号',
                                'order' => ''
                            ],
                            'refund' => [
                                'name' => 'money',
                                'description' => '退款金额',
                                'order' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'seller_order_notice' => [
                'title' => '卖家新订单通知',
                'data' => [
                    'title' => [
                        'title' => '模板ID',
                        'code' => 'template_id',
                        'value' => ''
                    ],
                    'template' => [
                        'title' => '模板变量',
                        'variable' => [
                            'order_id' => [
                                'name' => 'order_id',
                                'description' => '订单号',
                                'order' => ''
                            ],
                            'user_name' => [
                                'name' => 'user_name',
                                'description' => '付款人',
                                'order' => ''
                            ],
                            'pay_time' => [
                                'name' => 'pay_time',
                                'description' => '付款时间',
                                'order' => ''
                            ],
                            'money' => [
                                'name' => 'money',
                                'description' => '付款金额',
                                'order' => ''
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];