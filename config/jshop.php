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
    'api_express_key' => 'e5572466f046924e506ebf43dbccc786',
    //共享店铺费率
    'rate' => 0.006 ,
    'wxapp' => [
        'appid' => 'wxae5e1c354e373021',
        'appsecret' => 'eb3deb6e771c75547b758323a1c10c6e',
    ],
    'wx_pages' => 'pages/index/index',
    'payment' => [
        'wechatpay' => [
            'appid' => 'wxae5e1c354e373021',    //app_id
            'mch_id' => '1497851492',                     //商户号
            'key' => '12345678901234567890123456789012',
            'sslcert' => '5acb20016c559.pem',
            'sslkey' => '5acb20659e600.pem',
        ],
        'alipay' => [
            'appid' => '',
            'rsa_private_key' => 'MIIEowIBAAKCAQEA18Km/YKsi6vcwLHYarzlILEuXEkYzDXX8JCTM9/4wJNKY+X4kq0HskiAu1eOhMUFRGCM3jb6xpSKL47kjkR6gf4aM+er2pHBWyzNiBWaWJtLMr31HGBquGhbcV28aCCMrA81WVy6TJolzGg+FeHrwPngoLormkCgeG9TMywPjqXDZgsrmO5yz9jvQWkHc0NsnvhZGqBw8sDD5P6ZP/yoVM/5ny3TNKDB6gAuu/aProSLVlptn982vblmL6fcWTfxbXg/Kc7JWDj908ChloJZlIuPmMP+yb2btuuv3R9sExFY25lZF7GU/+FP13rRQVc6E/v/SUl4WuNU+8qg5Pq7HQIDAQABAoIBADzfZDG9/mRhlqSfCIoExThWBNhwnxhuUIBEqeNG222poKFSrrDjNHquTfONHl6DH5tNCjFfnZ0NJ3eHfyBMXavzPv1J7Yh6+ux4VXyBa8x3891bDp507Wcduj7fzVorZMTefZS6vq0CCIwFFQ8jcFo/pYpzO/4HIb8Y2YZkdfcudLFFzBeypTn91f5y49Tb1iAENPUoV/HRiUhG7b9Tjs0tMJq47niGkrWb6IuOMAcFS1qtfIM1jBhAj8ftMqMc6KKXdt04qAjSRFxi3T8pH8I7EukPwBQsiSxNZETg53Sc+gm44WbaX7BLbgB+132mVuxSRLsWrMLlOUEApriXWlkCgYEA72bJapc76Uw7UgQ9oLQT7GBQV2kY0Rj8F7poZVIBL91BMMCIxgQ6MMma8S+FgSPP/jyNCc5+32cyEyt7QR74aNtKuE70GTLW2EY8WgpqvAtdj+M/rVmk2VbVNHog9OPoB6vZknevgCa/+c6zSx2JnOOXhrxZa06Su09UpU3Lj2sCgYEA5rhAUqOC5X5eEHvEDlWFzI/seNroxo9XzixM9fRVRQH0qVvlXEDctK3GfOJwSYiqYi5BCYloU2GKV5QYvo52MsO+IoWvOVxDFs/1EYzVsuwGFLJZ2kImQPALLSES87c6K/XrGJ7nUySSXXvu32FHXKILynfmqnmxbrHVjAkTKZcCgYB0o0WasxgzcDqumPZVp6bVUY+TJtc6eMvJ274lLzD42vl6ATQiFcksH+1neNm4s3QQz/t/PaY+vg59nH+umKI6pCzhY0Y2SLi4InAhBlY+S+NHyVYq5TQV1+KEcMRBzBAuPKICZGGTNE3wLa7Y+Akl8RTQRk/ioiOHMv1wlFiLBQKBgQC9YtlJ/HZiM4y7Mx0INyORe0K9IdSZyOhmkO7LpjioYKaYrppjU8rXdR3NPYK2mbxiw10XmvdMGnEY2rRFF2Pm386fP+VJzHY1r5aXRWfVavDC5HPlOQ91lpVWAiQwcmMJ2t+UYju4f/i1TBoUuyF85HeRR0LNBP+kwc1tGbXlGQKBgAnlO8bPx3viY8pLNMsgclorGVX1j/glCeygvTjS1L3cXAa8u4gdSxdLa6Qw3eJh3inDNL4IZk1Hz4OcZZVTKSONvv8HS6sUs+S0sJPlSPzktVoX89TpK9Ygwj6pPCt2oQfuclMRzVL/akR3DJupjE+ic/z3wowMR5oWftIUQ7Qj',
            //支付宝公钥，此公钥应该是所有商户都统一的，所以这里写一下，每个商户就不用去单独维护了，此公钥非常重要
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqGO790Hr8q9fqr7E/5Xvium3//yfZIK+DiAXoQFLqSHqBGn79QJV0mFwjO3XWHN3ycAs3jv3jv1tdqCZlaUuUGTZuU6U+FRiQKOy7FVLJiiPDBhlVra6avxzWEOVcr7XlMn5nqeVvkmsIdXxdS+mQviS2Reaj4zcyv4bBxHNqlcgiGOKpQiSdh+N552CWW3hQUPFNWNXwTUb/fHYQ8DUUpvrH7mxAdCnrFIOlggaoqiGI8DtSOqOTgWGJ1n40/47xXZ+ipYKFtmI2IbGO+ygIyzcV/AkexnNWasblwwoZcnZmCEGVPJC9ssAtPy7pC9FTWDZZQ2I3UeqnDnP9ENTGwIDAQAB",
        ]
    ],
    'login_fail_num' => 3,              //登陆失败次数，如果每天登陆失败次数超过次数字，就会显示图片验证码
    'tocash_money_low' => '100' ,       //最低提现金额
    'authorization_url'=>'https://jshop.jihainet.com', //授权查询地址
    'product'=>'Jshop-B2C标准版',//产品名称
    'version'=>'v1.0',//版本号
];