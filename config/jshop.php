<?php

return [
    'default_image' => '/static/images/default.png',
    'upload_path' => ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'uploads',
    //上传文件限制5M
    'upload_filesize' => 5242880,
    //分页默认数量
    'page_limit' => 10,
    //售后，评论等上传图片数量限制
    'image_max' => 5,
    //商品导入模板
    'goods_import_templete' => ROOT_PATH . 'public' . DS . 'static' . DS . 'templete' .DS. 'goods-csv-import.csv',
    //快递查询配置参数
    'api_express' => [
        'key' => '',
        'customer' => ''
    ],

    'login_fail_num' => 3,              //登陆失败次数，如果每天登陆失败次数超过次数字，就会显示图片验证码
    'tocash_money_low' => '100' ,       //最低提现金额
    'authorization_url'=>'https://jshop.jihainet.com', //授权查询地址
    'product'=>'Jshop-b2c标准版',//产品名称
    'version'=>'v1.0.7',//版本号
    /*
     * 图片存储引擎配置
     * 本地存储

     'image_storage'=>[
        'type'=>'Local'
     ],
      阿里云存储
    'image_storage'=>[
        'type'=>'Aliyun',
        'accessKeyId'=>'',//阿里云accesskeyid，用户AccessKey控制台地址：https://usercenter.console.aliyun.com/#/manage/ak
        'accessKeySecret'=>'',//访问密钥
        'endpoint'=>'',//存储节点
        'bucket'=>'',//空间名称
        'domain'=>'',//所绑定域名
     ],
    */
    'image_storage'=>[
        'type'=>'Local'
    ]
];