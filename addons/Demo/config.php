<?php
return [
    'aliyunPrefix'=>[
        'title'=>'短信签名',
        'value'=>'',
    ],
    'menu' =>[
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了,写法和节点管理的写法是一样的。',
        'value' => [
            [
                'id' => 'demo_1',
                'parent_id' => '2',         //如果是控制器的话，默认就写2吧，挂载到后台根节点
                'parent_menu_id' => '238',
                'name' => '演示插件',
                'code' => 'Index',          //严格区分大小写,如果是控制器的话，一定要严格按照实际的名称填写，否则可能找不到此节点
                'type' => 'c',
                'perm_type' => 1,
                'sort' => 140,
                'addons' => 'demo',     //严格区分大小写
            ],
            [
                'id' => 'demo_2',
                'parent_id' => 'demo_1',        //如果是方法的话，一定要写所归属的控制器的id，生成url的时候，和权限判断的时候，都会用到
                'parent_menu_id' => 'demo_1',
                'name' => '演示列2',
                'code' => 'index',
                'type' => 'a',
                'perm_type' => 1,
                'sort' => 100,
                'addons' => 'demo',
            ],
            [
                'id' => 'demo_3',
                'parent_id' => 'demo_1',
                'parent_menu_id' => 'demo_1',
                'name' => '演示列表2',
                'code' => 'index2',
                'type' => 'a',
                'perm_type' => '2',
                'sort' => 100,
                'addons' => 'demo',
            ],
            [
                'id' => 'demo_4',
                'parent_id' => 'demo_1',
                'parent_menu_id' => 'demo_2',
                'name' => '权限菜单上看不见',
                'code' => 'index3',
                'type' => 'a',
                'perm_type' => '3',
                'sort' => 100,
                'addons' => 'demo',
            ]
        ]
    ],
    'api' => [
        'title' => '插件对外的接口都放到这里，接口请求的时候，会来此判断是否存在此控制器和方法,类似/config/api/api.php',
        'value' => [
            'api' => [      //控制器名称，可以随便起名，不过此控制器集成的基类一定要注意，不要写错了
                'code'   => 'Api',
                'method' => [
                    'zouqi' => [
                        'code'     => 'jshop',
                        'is_login' => true
                    ]
                ]
            ]
        ]
    ]
];