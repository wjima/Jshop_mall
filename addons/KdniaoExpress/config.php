<?php
return [
    'ebusinessid'=>[
        'title'=>'用户ID',
        'value'=>''
    ],
    'apikey'=>[
        'title'=>'API key',
        'value'=>'',
    ],
    'requesttype'=>[
        'title'=>'指令类型',
        'value'=>'1002',
    ],
    'print_name'=>[
        'title'=>'打印机名称',
        'value'=>'',
    ],
    'is_priview'=>[
        'title'=>'是否预览',
        'value'=>'0',
    ],
    'auto_send'=>[
        'title'=>'打印并发货',
        'value'=>'0',
    ],
    'is_notice'=>[
        'title'=>'通知快递',
        'value'=>'1',
    ],
    'menu' =>[
        'title' => '菜单，此菜单就写到配置文件上吧，也不去可视化编辑了,写法和节点管理的写法是一样的。',
        'value' => [
            [
                'id' => 'KdniaoExpress_1',
                'parent_id' => '2',         //如果是控制器的话，默认就写2吧，挂载到后台根节点
                'parent_menu_id' => '2',
                'name' => '快递鸟插件订单控制器',
                'code' => 'Order',          //严格区分大小写,如果是控制器的话，一定要严格按照实际的名称填写，否则可能找不到此节点
                'type' => 'c',
                'perm_type' => '3',
                'sort' => 140,
                'addons' => 'KdniaoExpress',     //严格区分大小写
            ],
            [
                'id' => 'KdniaoExpress_2',
                'parent_id' => 'KdniaoExpress_1',        //如果是方法的话，一定要写所归属的控制器的id，生成url的时候，和权限判断的时候，都会用到
                'parent_menu_id' => '297',
                'name' => '快递鸟批量打印订单',
                'code' => 'batchPrint',
                'type' => 'a',
                'perm_type' => '2',
                'sort' => 100,
                'addons' => 'KdniaoExpress',
            ],
            [
                'id' => 'KdniaoExpress_3',
                'parent_id' => 'KdniaoExpress_1',
                'parent_menu_id' => 'KdniaoExpress_2',
                'name' => '翻页处理',
                'code' => 'nextPrint',
                'type' => 'a',
                'perm_type' => '3',
                'sort' => 100,
                'addons' => 'KdniaoExpress',
            ],
            [
                'id' => 'KdniaoExpress_4',
                'parent_id' => 'KdniaoExpress_1',
                'parent_menu_id' => 'KdniaoExpress_2',
                'name' => '打印订单',
                'code' => 'printExpress',
                'type' => 'a',
                'perm_type' => '3',
                'sort' => 100,
                'addons' => 'KdniaoExpress',
            ],
            [
                'id' => 'KdniaoExpress_5',
                'parent_id' => 'KdniaoExpress_1',
                'parent_menu_id' => '244',
                'name' => '电子面单管理',
                'code' => 'FaceSheet',
                'type' => 'a',
                'perm_type' => '1',
                'sort' => 100,
                'addons' => 'KdniaoExpress',
            ]
        ]
    ],
];