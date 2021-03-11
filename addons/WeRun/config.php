<?php
return [
    'min_steps'=>[
        'title'=>'基础步数',
        'value'=>5000,
    ],
    'min_jifen'=>[
        'title'=>'赠送的基础积分',
        'value'=>100,
    ],
    'more_steps'=>[
        'title'=>'每超出的步数',
        'value'=>1000,
    ],
    'more_jifen'=>[
        'title'=>'每超出步数送积分',
        'value'=>20,
    ],
    'menu' =>[
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了,写法和节点管理的写法是一样的。',
        'value' => [
            [
                'id' => 'werun_1',
                'parent_id' => '2',         //如果是控制器的话，默认就写2吧，挂载到后台根节点
                'parent_menu_id' => '238',
                'name' => '计步管理',
                'code' => 'Index',          //严格区分大小写,如果是控制器的话，一定要严格按照实际的名称填写，否则可能找不到此节点
                'type' => 'c',
                'perm_type' => 1,
                'sort' => 140,
                'addons' => 'WeRun',     //严格区分大小写
            ],
            [
                'id' => 'werun_2',
                'parent_id' => 'werun_1',        //如果是方法的话，一定要写所归属的控制器的id，生成url的时候，和权限判断的时候，都会用到
                'parent_menu_id' => 'werun_1',
                'name' => '计步列表',
                'code' => 'index',
                'type' => 'a',
                'perm_type' => 1,
                'sort' => 100,
                'addons' => 'WeRun',
            ],
            [
                'id' => 'werun_3',
                'parent_id' => 'werun_1',        //如果是方法的话，一定要写所归属的控制器的id，生成url的时候，和权限判断的时候，都会用到
                'parent_menu_id' => 'werun_1',
                'name' => '兑换记录',
                'code' => 'log',
                'type' => 'a',
                'perm_type' => 1,
                'sort' => 100,
                'addons' => 'WeRun',
            ]
        ]
    ],
    'api' => [
        'title' => '插件对外的接口都放到这里，接口请求的时候，会来此判断是否存在此控制器和方法,类似/config/api/api.php',
        'value' => [
            'api' => [      //控制器名称，可以随便起名，不过此控制器集成的基类一定要注意，不要写错了
                'code'   => 'Api',
                'method' => [
                    'today' => [
                        'code'     => 'today',
                        'is_login' => true
                    ],
                    'statistic' => [
                        'code'     => 'statistic',
                        'is_login' => true
                    ],
                    'log' => [
                        'code'     => 'log',
                        'is_login' => true
                    ],
                    'update' => [
                        'code'     => 'update',
                        'is_login' => true
                    ],
                    'point' => [
                        'code'     => 'point',
                        'is_login' => true
                    ],
                    'collect' => [
                        'code'     => 'collect',
                        'is_login' => true
                    ],
                ]
            ]
        ]
    ]
];