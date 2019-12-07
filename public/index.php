<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 https://www.jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: sin <sin@jihainet.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
define('ROOT_PATH',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('DS',DIRECTORY_SEPARATOR);
// 插件目录
define('ADDON_PATH', ROOT_PATH . 'addons'   . DIRECTORY_SEPARATOR);

if(!file_exists(ROOT_PATH.'/config/install.lock')&&!file_exists(ROOT_PATH.'/runtime/install.lock')){
    header('Location:/install/');exit;
}
// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';
// 支持事先使用静态方法设置Request对象和Config对象
// 执行应用并响应
Container::get('app')->run()->send();