<?php
return [
    'aliyunPrefix'=>[
        'title'=>'短信签名',
        'value'=>'',
    ],
    'menu' =>[
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了。',
        'value' => [
            'demo_1' =>[
                'id' => 'demo_1',
                'parent_menu_id' => '238',
                'name' => '演示插件',
                'type' => 'c',
                'perm_type' => 1,
                'url' => get_addon_url('Demo://index/index'),
                'sort' => 140
            ],
            'demo_2' =>[
                'id' => 'demo_2',
                'parent_menu_id' => 'demo_1',
                'name' => '演示列2',
                'type' => 'a',
                'perm_type' => 1,
                'url' => get_addon_url('Demo://index/index'),
                'sort' => 100
            ],
            'demo_3' =>[
                'id' => 'demo_3',
                'parent_menu_id' => 'demo_1',
                'name' => '演示列表2',
                'type' => 'a',
                'perm_type' => '2',
                'url' => get_addon_url('Demo://index/index'),
                'sort' => 100
            ],
            'demo_4' =>[
                'id' => 'demo_4',
                'parent_menu_id' => 'demo_2',
                'name' => '权限菜单上看不见',
                'type' => 'a',
                'perm_type' => '3',
                'url' => get_addon_url('Demo://index/index'),
                'sort' => 100
            ]
        ]
    ]
];