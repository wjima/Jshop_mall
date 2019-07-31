<?php

return [
    'default_image'         => 'https://b2c.jihainet.com/static/images/default.png',
    'upload_path'           => ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'uploads',
    //上传文件限制5M
    'upload_filesize'       => 5242880,
    //分页默认数量
    'page_limit'            => 10,
    //售后，评论等上传图片数量限制
    'image_max'             => 5,
    //商品导入模板
    'goods_import_templete' => ROOT_PATH . 'public' . DS . 'static' . DS . 'templete' . DS . 'goods-csv-import.csv',
    'user_import_templete'  => ROOT_PATH . 'public' . DS . 'static' . DS . 'templete' . DS . 'user-csv-import.csv',
    //快递查询配置参数
    'api_express'           => [
        'key'      => '',
        'customer' => ''
    ],

    'login_fail_num'        => 3,       //登陆失败次数，如果每天登陆失败次数超过次数字，就会显示图片验证码
    'manage_login_fail_num' => 5,       //管理员登陆失败次数，如果超过这个次数，会显示图片验证码，防止暴力破解
    'tocash_money_low'      => '100',       //最低提现金额
    'authorization_url'     => 'https://jshop.jihainet.com', //授权查询地址
    'product'               => 'Jshop-b2c标准版',//产品名称
    'version'               => 'v2.0.6',    //版本号
    'image_storage'         => [
        'type' => 'Local'
    ],
    'file_size'             => '104857600',//100M
    'area_list'             => ROOT_PATH . 'public/static/area.json',//地址库信息地址
];
