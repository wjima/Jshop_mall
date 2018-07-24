<?php
/**
 * 微信第三方平台相关配置信息
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/18
 * Time: 下午2:37
 */
return [
    'appid' => 'wxf4a21ecb59766556',
    'appsecret' => 'f597b118050b9486f87d4b17a0d8cc1d',
    'token' => 'bahyuFB7CIIFAu1VqJkfFNosSJEcajND',
    'encrypt_key' => 'ZrGi6Vz1TCpKHo9kDRX9g9PI7dVbOGQL7zjQggXP7ka',
    'authorization_url' => 'https://jshop.jihainet.com/wechat.html',
    'event_url' => 'https://jshop.jihainet.com/wechat/message/callback/$APPID$',
    'requestdomain'=>'https://jshop.jihainet.com',//request合法域名
    'wsrequestdomain'=>'https://jshop.jihainet.com',//socket合法域名
    'uploaddomain'=>'https://jshop.jihainet.com',//uploaddomain合法域名
    'downloaddomain'=>'https://jshop.jihainet.com',//downloaddomain合法域名
    'webviewdomain'=>'https://jshop.jihainet.com',//设置小程序业务域名
    'api_url'=>'https://jshop.jihainet.com/index.php/api.html',//设置小程序业务域名
];