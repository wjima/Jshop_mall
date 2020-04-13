<?php
return [
    'menu' =>[
        'title' => 'PC菜单',
        'value' => [
            'pc_1' =>[
                'id' => 'pc_1',
                'parent_menu_id' => '244',
                'name' => 'PC设置',
                'type' => 'c',
                'url' => get_addon_url('Pc://PcMenuParams/index'),
                'sort' => 140
            ],
            'pc_6' =>[
                'id' => 'pc_6',
                'parent_menu_id' => 'pc_1',
                'name' => '菜单列表',
                'type' => 'a',
                'url' => get_addon_url('Pc://PcMenu/index'),
                'sort' => 100
            ],
            'pc_3' =>[
                'id' => 'pc_3',
                'parent_menu_id' => 'pc_1',
                'name' => '楼层广告列表',
                'type' => 'a',
                'url' => get_addon_url('Pc://PcFloorAdv/index'),
                'sort' => 100
            ],
            'pc_7' =>[
                'id' => 'pc_7',
                'parent_menu_id' => 'pc_1',
                'name' => '楼层列表',
                'type' => 'a',
                'url' => get_addon_url('Pc://PcFloor/index'),
                'sort' => 100
            ],
            'pc_4' =>[
                'id' => 'pc_4',
                'parent_menu_id' => 'pc_1',
                'name' => '友情链接',
                'type' => 'a',
                'url' => get_addon_url('Pc://PcFriendshipLink/index'),
                'sort' => 100
            ],
            'pc_5' =>[
                'id' => 'pc_5',
                'parent_menu_id' => 'pc_1',
                'name' => '基本设置',
                'type' => 'a',
                'url' => get_addon_url('Pc://PcSettings/index'),
                'sort' => 100
            ],
        ]
    ]
];
