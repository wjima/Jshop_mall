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
                'url' => get_addon_url('Demo://index/index'),
                'sort' => 140
            ],
            'demo_2' =>[
                'id' => 'demo_2',
                'parent_menu_id' => 'demo_1',
                'name' => '演示列2',
                'type' => 'a',
                'url' => get_addon_url('Demo://index/index'),
                'sort' => 100
            ],
            'demo_3' =>[
                'id' => 'demo_3',
                'parent_menu_id' => 'demo_1',
                'name' => '演示列表2',
                'type' => 'a',
                'url' => get_addon_url('Demo://index/index'),
                'sort' => 100
            ],
        ]
    ]
];