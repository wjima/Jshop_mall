<?php
return [
    'menu' =>[
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了,写法和节点管理的写法是一样的。',
        'value' => [
            [
                'id' => 'userupgrade_1',
                'parent_id' => '2',         //如果是控制器的话，默认就写2吧，挂载到后台根节点
                'parent_menu_id' => '2',
                'name' => '用户等级升级控制器',
                'code' => 'Index',          //严格区分大小写,如果是控制器的话，一定要严格按照实际的名称填写，否则可能找不到此节点
                'type' => 'c',
                'perm_type' => 3,
                'sort' => 140,
                'addons' => 'Userupgrade',     //严格区分大小写
            ],
            [
                'id' => 'userupgrade_2',
                'parent_id' => 'demo_1',        //如果是方法的话，一定要写所归属的控制器的id，生成url的时候，和权限判断的时候，都会用到
                'parent_menu_id' => '553',
                'name' => '用户升级配置',
                'code' => 'index',
                'type' => 'a',
                'perm_type' => 3,
                'sort' => 100,
                'addons' => 'Userupgrade',
            ],
        ]
    ],
];