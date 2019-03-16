<?php

\think\Console::addDefaultCommands([
    \myxland\addons\library\SendConfig::class,
]);

use think\Loader;
use think\facade\App;
use think\facade\Env;
use think\facade\Hook;
use think\facade\Config;
use think\facade\Cache;
use think\facade\Route;

// 定义路由
Route::any('plugins/:route', "\\myxland\\addons\\library\\Route@execute");

// 如果插件目录不存在则创建
if (! is_dir(ADDON_PATH)) {
    @mkdir(ADDON_PATH, 0777, true);
}

// 注册类的根命名空间
Loader::addNamespace('addons', ADDON_PATH);


// 闭包初始化行为
/*Hook::add('action_begin', function () {
    // 获取系统配置
    $data   = config('app.app_debug') ? [] : Cache::get('hooks', []);
    $addons = (array) config('addons.hooks');
    if (empty($data)) {
        // 初始化钩子
        foreach ($addons as $key => $values) {
            if (is_string($values)) {
                $values = explode(',', $values);
            } else {
                $values = (array) $values;
            }
            $addons[$key] = array_filter(array_map('get_addon_class', $values));
            Hook::add($key, $addons[$key]);
        }
        Cache::set('hooks', $addons);
    } else {
        Hook::import($data, false);
    }
});*/

/**
 * 处理插件钩子
 *
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    $result = Hook::listen($hook, $params);
    if (count($result) > 0 && $result[0]) {
        return $result;
    }
}

/**
 * 获取插件类的类名
 *
 * @param $name 插件名
 * @param string $type 返回命名空间类型
 * @param string $class 当前类名
 * @return string
 */
function get_addon_class($name, $type = 'hook', $class = null)
{
    $name = Loader::parseName($name,1);

    // 处理多级控制器情况
    if (! is_null($class) && strpos($class, '.')) {
        $class = explode('.', $class);
        foreach ($class as $key => $cls) {
            $class[$key] = Loader::parseName($cls, 1);
        }
        $class = implode('\\', $class);
    } else {
        $class = Loader::parseName(is_null($class) ? $name : $class, 1);
    }
    switch ($type) {
        case 'controller':
            $namespace = "\\addons\\" . $name . "\\controller\\" . $class;
            break;
        default:
            $namespace = "\\addons\\" . $name . "\\" . $class;
    }
    return class_exists($namespace) ? $namespace : '';
}

/**
 * 获取插件类的配置文件数组
 *
 * @param string $name 插件名
 * @return array
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();

        return $addon->getConfig();
    } else {
        return [];
    }
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param $url
 * @param array $param
 * @return bool|string
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 */
function get_addon_url($url, $param = [], $suffix = true, $domain = false)
{
    $url        = parse_url($url);
    $case       = config('url_convert');
    $addons     = $case ? Loader::parseName($url['scheme']) : $url['scheme'];
    $controller = $case ? Loader::parseName($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');
    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    // 生成插件链接新规则
    $actions = "{$addons}-{$controller}-{$action}";
    return url("/plugins/{$actions}", $param, $suffix, $domain);
}