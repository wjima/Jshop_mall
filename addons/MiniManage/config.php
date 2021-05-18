<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
return [
    'menu' => [
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了。',
        'value' => [
            'menu_1' => [
                'id' => 'menu_1',
                'parent_menu_id' => '244',
                'name' => '移动后台管理',
                'type' => 'c',
                'sort' => 140,
                'parent_id' => '2',     //如果是控制器的话，默认就写2吧，挂载到后台根节点
                'code' => 'index',  //严格区分大小写,如果是控制器的话，一定要严格按照实际的名称填写，否则可能找不到此节点
                'perm_type' => 1,
                'addons' => 'MiniManage'    //严格区分大小写
            ],
            'menu_2' => [
                'id' => 'menu_2',
                'parent_menu_id' => 'menu_1',
                'name' => '信息列表',
                'type' => 'a',
                'sort' => 100,
                'parent_id' => 'menu_1',    //如果是方法的话，一定要写所归属的控制器的id，生成url的时候，和权限判断的时候，都会用到
                'code' => 'getlist',
                'perm_type' => 1,
                'addons' => 'MiniManage'
            ],
            'menu_3' => [
                'id' => 'menu_3',
                'parent_menu_id' => 'menu_1',
                'name' => '信息通道',
                'type' => 'a',
                'sort' => 100,
                'parent_id' => 'menu_1',    //如果是方法的话，一定要写所归属的控制器的id，生成url的时候，和权限判断的时候，都会用到
                'code' => 'messagecenter',
                'perm_type' => 1,
                'addons' => 'MiniManage'
            ]
        ]
    ],
    'api' => [
        'title' => '插件对外的接口都放到这里，接口请求的时候，会来此判断是否存在此控制器和方法,类似/config/api/api.php',
        'value' => [
            'api' => [      //控制器名称，可以随便起名，不过此控制器集成的基类一定要注意，不要写错了
                'code'   => 'Api',
                'method' => [
                    'test' => [
                        'code'     => 'test',
                        'is_login' => false
                    ],
                    'index' => [
                        'code'     => 'index',
                        'is_login' => false
                    ],
                    'getorderList' => [
                        'code'     => 'getOrderList',
                        'is_login' => false
                    ],
                    'details' => [
                        'code'     => 'details',
                        'is_login' => false
                    ],
                    'ship' => [
                        'code'     => 'ship',
                        'is_login' => false
                    ],
                    'toship' => [
                        'code'     => 'toship',
                        'is_login' => false
                    ],
                    'msmlist' => [
                        'code'     => 'msmlist',
                        'is_login' => false
                    ],
                    'center' => [
                        'code'     => 'center',
                        'is_login' => false
                    ],
                    'tocenter' => [
                        'code'     => 'tocenter',
                        'is_login' => false
                    ],
                    'afterlist' => [
                        'code'     => 'afterlist',
                        'is_login' => false
                    ],
                    'afterdetail' => [
                        'code'     => 'afterdetail',
                        'is_login' => false
                    ],
                    'refund' => [
                        'code'     => 'refund',
                        'is_login' => false
                    ],
                    'reaudit' => [
                        'code'     => 'reaudit',
                        'is_login' => false
                    ],
                    'reship' => [
                        'code'     => 'reship',
                        'is_login' => false
                    ],
                    'confirmReship' => [
                        'code'     => 'confirmReship',
                        'is_login' => false
                    ],
                ]
            ],
            'login' =>[
                'code' => "Login",
                'method' =>[
                    'login' => [
                        'code'     => 'login',
                        'is_login' => false
                    ]
                ]
            ]
        ]
    ],
     'wx_official_appid' => [
        'title' => 'wx_official_appid',
        'value' => 'wxd02de048830633c7'
     ],
     'wx_official_app_secret' => [
        'title' => 'wx_official_app_secret',
        'value' => 'fe553cea3b308e7eefc14f672f5fa18e'
     ],
];
