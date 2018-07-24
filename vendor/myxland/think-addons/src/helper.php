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
// 插件目录
define('ADDON_PATH', ROOT_PATH . 'addons'   . DIRECTORY_SEPARATOR);

// 定义路由
Route::any('plugins/:route', "\\myxland\\addons\\library\\Route@execute");

// 如果插件目录不存在则创建
if (! is_dir(ADDON_PATH)) {
    @mkdir(ADDON_PATH, 0777, true);
}

// 注册类的根命名空间
Loader::addNamespace('addons', ADDON_PATH);

// 闭包自动识别插件目录配置
Hook::add('app_init', function () {
    // 获取开关
    $autoload = (bool) Config::get('addons.autoload', false);
    // 非正是返回
    if (! $autoload) {
        return;
    }
    $seller = session('seller');
    $sellerAddons = getSellerAddons($seller['id']);
    // 当debug时不缓存配置
    $config = config('app.app_debug') ? [] : Cache::get('addons', []);
    if (empty($config)) {
        // 读取addons的配置
        $config = config('addons.');
        // 读取插件目录及钩子列表
        $base = get_class_methods("\\myxland\\Addons");
        $base = $base?$base:[];

        // 读取插件目录中的php文件
        foreach (glob(ADDON_PATH . '*/*.php') as $addons_file) {
            // 格式化路径信息
            $info = pathinfo($addons_file);
            // 获取插件目录名
            $name = pathinfo($info['dirname'], PATHINFO_FILENAME);
            // 找到插件入口文件
            if (strtolower($info['filename']) == strtolower($name)) {
                // 读取出所有公共方法
                $methods = (array) get_class_methods("\\addons\\" . $name . "\\" . $info['filename']);
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);
                // 循环将钩子方法写入配置中
                foreach ($hooks as $hook) {
                    if (! isset($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = [];
                    }
                    // 兼容手动配置项
                    if (is_string($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = explode(',', $config['hooks'][$hook]);
                    }
                    if (! in_array($name, $config['hooks'][$hook])) {
                        $config['hooks'][$hook][] = $name;
                    }
                }
            }
        }

        if($sellerAddons){//过滤商户插件，只显示订阅插件
            foreach((array)$config['hooks'] as $key=>$val){
                foreach($val as $k=>$v){
                    if(!in_array($v,$sellerAddons)){
                        unset($config['hooks'][$key][$k]);
                    }
                }
            }
        }else{//未订阅时，记录空
            $config['hooks'] = [];
        }
        config($config, 'addons');
    }
    config($config, 'addons');
});

// 闭包初始化行为
Hook::add('action_begin', function () {
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
});

/**
 * 处理插件钩子
 *
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    Hook::listen($hook, $params);
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
    $name = Loader::parseName($name);
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
function addon_url($url, $param = [], $suffix = true, $domain = false)
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