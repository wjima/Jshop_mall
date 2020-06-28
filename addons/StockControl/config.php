<?php
return [
    'menu' => [
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了。',
        'value' => [
            [
                'id' => 'stock_menu_1',
                'parent_id' => '2',
                'parent_menu_id' => '0',
                'name' => '库存管理',
                'type' => 'c',
                'code' => 'index',
                'perm_type' => 1,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_5',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '库存盘点',
                'type' => 'a',
                'code' => 'stockIndex',
                'perm_type' => 1,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_5_1',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_5',
                'name' => '修改库存',
                'type' => 'a',
                'code' => 'editStock',
                'perm_type' => 2,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_2',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '入库管理',
                'type' => 'a',
                'code' => 'stock1Index',
                'perm_type' => 1,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_2_1',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_2',
                'name' => '新增入库单',
                'type' => 'a',
                'code' => 'stock1Add',
                'perm_type' => 2,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_2_2',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_2',
                'name' => '查看出入单详情',
                'type' => 'a',
                'code' => 'stockView',
                'perm_type' => 2,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_3',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '出库管理',
                'type' => 'a',
                'code' => 'stock2Index',
                'perm_type' => 1,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_3_1',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_3',
                'name' => '新增出库单',
                'type' => 'a',
                'code' => 'stock2Add',
                'perm_type' => 2,
                'addons' => 'StockControl',
                'sort' => 110
            ],
            [
                'id' => 'stock_menu_4',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '库存记录',
                'type' => 'a',
                'code' => 'index',
                'perm_type' => 1,
                'addons' => 'StockControl',
                'sort' => 110
            ],
        ]
    ]
];
