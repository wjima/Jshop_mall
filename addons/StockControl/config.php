<?php
return [
    'menu' =>[
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了。',
        'value' => [
            'menu_1' => [
                'id' => 'menu_1',
                'parent_menu_id' => '0',
                'name' => '库存管理',
                'type' => 'a',
                'url' => '',
                'sort' => 110
            ],
            'menu_2' => [
                'id' => 'menu_2',
                'parent_menu_id' => 'menu_1',
                'name' => '入库管理',
                'type' => 'a',
                'url' => get_addon_url('StockControl://Index/stock1index'),
                'sort' => 110
            ],
            'menu_3' => [
                'id' => 'menu_3',
                'parent_menu_id' => 'menu_1',
                'name' => '出库管理',
                'type' => 'a',
                'url' => get_addon_url('StockControl://Index/stock2index'),
                'sort' => 110
            ],
            'menu_4' => [
                'id' => 'menu_4',
                'parent_menu_id' => 'menu_1',
                'name' => '库存盘点',
                'type' => 'a',
                'url' => get_addon_url('StockControl://Index/index'),
                'sort' => 110
            ],
        ]
    ]
];