<?php
return [
    'menu' =>[
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了。',
        'value' => [
            'menu_1' => [
                'id' => 'stock_menu_1',
                'parent_id' => '2',
                'parent_menu_id' => '0',
                'name' => '库存管理',
                'type' => 'c',
                'code' => 'index',          //不区分大小写了,如果是控制器的话，加入控制器名称是驼峰写法，比如IndexOrder，这里要写成index_order,下面的插件名称同样写法
                'perm_type' => 1,
                'addons' => 'stock_control',
                'sort' => 110
            ],
            'menu_5' => [
                'id' => 'stock_menu_5',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '库存盘点',
                'type' => 'a',
//                'url' => get_addon_url('StockControl://Index/stockIndex'),  //当前实时库存
                'code' => 'stockIndex',          //不区分大小写了,如果是控制器的话，加入控制器名称是驼峰写法，比如IndexOrder，这里要写成index_order,下面的插件名称同样写法
                'perm_type' => 1,
                'addons' => 'stock_control',
                'sort' => 110
            ],
            'menu_2' => [
                'id' => 'stock_menu_2',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '入库管理',
                'type' => 'a',
//                'url' => get_addon_url('StockControl://Index/stock1index'),
                'code' => 'stock1Index',          //不区分大小写了,如果是控制器的话，加入控制器名称是驼峰写法，比如IndexOrder，这里要写成index_order,下面的插件名称同样写法
                'perm_type' => 1,
                'addons' => 'stock_control',
                'sort' => 110
            ],
            'menu_3' => [
                'id' => 'stock_menu_3',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '出库管理',
                'type' => 'a',
//                'url' => get_addon_url('StockControl://Index/stock2index'),
                'code' => 'stock2Index',          //不区分大小写了,如果是控制器的话，加入控制器名称是驼峰写法，比如IndexOrder，这里要写成index_order,下面的插件名称同样写法
                'perm_type' => 1,
                'addons' => 'stock_control',
                'sort' => 110
            ],
            'menu_4' => [
                'id' => 'stock_menu_4',
                'parent_id' => 'stock_menu_1',
                'parent_menu_id' => 'stock_menu_1',
                'name' => '库存记录',
                'type' => 'a',
//                'url' => get_addon_url('StockControl://Index/index'),
                'code' => 'index',          //不区分大小写了,如果是控制器的话，加入控制器名称是驼峰写法，比如IndexOrder，这里要写成index_order,下面的插件名称同样写法
                'perm_type' => 1,
                'addons' => 'stock_control',
                'sort' => 110
            ],
        ]
    ]
];