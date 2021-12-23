<?php
return [
    'menu' => [
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了。',
        'value' => [
            [
                'id' => 'goods_menu_1',
                'parent_id' => '2',
                'parent_menu_id' => '356',
                'name' => '商品免单管理',
                'type' => 'c',
                'code' => 'index',
                'perm_type' => 1,
                'addons' => 'FreePackage',
                'sort' => 110
            ],
            [
                'id' => 'goods_menu_1',
                'parent_id' => 'goods_menu_1',
                'parent_menu_id' => 'goods_menu_1',
                'name' => '基础设置',
                'type' => 'a',
                'code' => 'setting',
                'perm_type' => 1,
                'addons' => 'FreePackage',
                'sort' => 110
            ],
            [
                'id' => 'goods_menu_2',
                'parent_id' => 'goods_menu_1',
                'parent_menu_id' => 'goods_menu_1',
                'name' => '订单列表',
                'type' => 'a',
                'code' => 'index',
                'perm_type' => 1,
                'addons' => 'FreePackage',
                'sort' => 110
            ]
        ]
    ],
    'setting' => [
        'title' => '分销设置',
        'value' => [
            'combo_status'  => '2',
            'combo_num' => '3',
            'combo_desc'    => '商品满3件免1件，价格最低免单！',
        ]
    ],
    'api' => [
        'title' => '插件对外的接口都放到这里，接口请求的时候，会来此判断是否存在此控制器和方法,类似/config/api/api.php',
        'value' => [
            'api' => [      //控制器名称，可以随便起名，不过此控制器集成的基类一定要注意，不要写错了
                'code'   => 'Api',
                'method' => [
                    'setting' => [
                        'code'     => 'setting',    // 获取套餐商品列表
                        'is_login' => false
                    ],
                    'getcartids' => [
                        'code'     => 'getCartIds',    // 获取套餐商品列表
                        'is_login' => true
                    ],
                ]
            ]
        ]
    ]
];
