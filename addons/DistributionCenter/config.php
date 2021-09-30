<?php
return [
    'menu'    => [
        'title' => '分销中心',
        'value' => [
            [
                'id'             => 'distributioncenter_1',//菜单节点唯一识别码，不能和现有的节点表的id冲突，也不要和其它菜单节点id冲突
                'parent_id'      => '2',//父节点id，如果当前是控制器，那么父节点可以写后端的父节点2，如果是控制器里的方法，就写他所在的控制器的id，全部小写即可
                'parent_menu_id' => '356',//菜单挂载节点，具体功能和节点管理里的一样，是1的时候，并且是控制器的时候是节点表id
                'name'           => '分销推广',
                'code'           => 'Index',//编码，大小写严格模式，注意，主要是控制器，如果是控制器，大小写一定要严格，
                'type'           => 'c',// 如果是控制器写c，如果是方法，就写a
                'perm_type'      => '1',//1主体权限，2半主体权限，3关联权限
                'sort'           => 110,
                'addons'         => 'DistributionCenter'//插件名称，大小写严格模式，
            ],
            [
                'id'             => 'distributioncenter_2',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_1',
                'name'           => '分销设置',
                'code'           => 'setting',
                'type'           => 'a',
                'perm_type'      => '1',
                'sort'           => 40,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_1',
                'name'           => '分销等级',
                'code'           => 'grade',
                'type'           => 'a',
                'perm_type'      => '1',
                'sort'           => 30,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_4',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_1',
                'name'           => '分销商',
                'code'           => 'distribution',
                'type'           => 'a',
                'perm_type'      => '1',
                'sort'           => 10,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_5',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_1',
                'name'           => '分销订单',
                'code'           => 'order',
                'type'           => 'a',
                'perm_type'      => '1',
                'sort'           => 20,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_5_1',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_5',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '取消订单',
                'code'           => 'cancleOrder',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_4_1',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_4',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '保存分销商',
                'code'           => 'doEditDistribution',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_4_2',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_4',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '编辑分销商',
                'code'           => 'editDistribution',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_4_3',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_4',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '删除分销商',
                'code'           => 'delDistribution',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_1',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '编辑分销等级',
                'code'           => 'editGrade',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_2',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '添加分销等级条件',
                'code'           => 'conditionAdd',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_3',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '编辑分销等级条件',
                'code'           => 'conditionEdit',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_4',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '分销条件列表',
                'code'           => 'conditionList',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_5',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '添加分销结果',
                'code'           => 'resultAdd',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_6',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '分销结果编辑',
                'code'           => 'resultEdit',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_7',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '分销结果列表',
                'code'           => 'resultList',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_8',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '分销条件删除',
                'code'           => 'conditionDel',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
            [
                'id'             => 'distributioncenter_3_9',
                'parent_id'      => 'distributioncenter_1',
                'parent_menu_id' => 'distributioncenter_3',//如果权限是附属权限，则这里要写附属到哪个节点上
                'name'           => '分销结果删除',
                'code'           => 'resultDel',
                'type'           => 'a',
                'perm_type'      => '3',
                'sort'           => 100,
                'addons'         => 'DistributionCenter'
            ],
        ]
    ],
    'setting' => [
        'title' => '分销设置',
        'value' => [
            'distribution_level'    => '2',
            'distribution_store'    => '1',
            'distribution_type'     => '3',
            'distribution_money'    => '0',
            'distribution_goods'    => '1',
            'distribution_goods_id' => '25',
            'notes'                 => '       成为Jshop云商分销商后，可以获取佣金，用户只可被推荐一次，越早推荐越返利越多哦。',
            'agreement'             => '分销商申请协议',
            'route'                 => 'distribution_center',
            'commission_type'       => '2',
            'commission_first'      => '1',
            'commission_second'     => '1',
            'commission_third'      => '1',
            'commission_1'          => '0',
            'commission_2'          => '0',
            'commission_3'          => '0'
        ]
    ],
    'api' => [
        'title' => '插件对外的接口都放到这里，接口请求的时候，会来此判断是否存在此控制器和方法,类似/config/api/api.php',
        'value' => [
            'api' => [      //控制器名称，可以随便起名，不过此控制器集成的基类一定要注意，不要写错了
                'code'   => 'Api',
                'method' => [
                    'apply' => [
                        'code'     => 'applyDistribution',// 申请成为分销商接口
                        'is_login' => true
                    ],
                    'info' => [
                        'code'     => 'info',   //查询用户是否可以成为分销商
                        'is_login' => true
                    ],
                    'order' => [
                        'code'     => 'myOrder',    //我推广的订单
                        'is_login' => true
                    ],
                    'getinfo' => [
                        'code'     => 'getStoreInfo',   //获取店铺信息
                        'is_login' => true
                    ],
                    'set' => [
                        'code'     => 'setStore',   //店铺设置
                        'is_login' => true
                    ],
                    'mystore' => [
                        'code'     => 'myStore',    //我的店铺信息接口
                        'is_login' => true
                    ],
                ]
            ]
        ]
    ]

];
