<?php
header('Content-type:text/html;charset=utf-8');
session_start();

if (!empty($_GET)) {
    $_GET = remove_xss($_GET);
}
if (!empty($_POST)) {
    $_POST = remove_xss($_POST);
}
$_COOKIE  = remove_xss($_COOKIE);
$_REQUEST = remove_xss($_REQUEST);

//配置信息
$config = array(
    'version'          => 'v2.8.0',        //版本号
    'indexPage'        => 'step1',         //用户协议
    'checkPage'        => 'step2',         //环境检测
    'createPage'       => 'step3',         //数据库配置
    'importPage'       => 'step4',         //默认管理信息和演示数据
    'endPage'          => 'step5-1',       //安装成功页面
    'errorPage'        => 'step5-2',       //安装失败页面
    'sqlDir'           => './database/',   //数据库所在目录
    'prefix'           => 'jshop_',        //默认表前缀
    'sqlName'          => 'jshop',         //数据库文件名称
    'frontMenuSqlName' => 'front_menu',    //数据库文件名称
    'demoData'         => 'demo',          //演示数据文件名称
    'databaseUrl'      => '../../config/database.php',     //database.php文件地址
    'account'          => 'admin',         //默认账号
    'password'         => '123456',         //默认密码
    //'h5ConfigUrl' => '../wap/static/config.js',       //h5的config.js文件地址
    'limit'            => '50',   //安装时多少条数据一次翻页
    'h5ConfigUrl'      => '../wap/static/config.js',       //h5的config.js文件地址
);


//数据库配置
$db_config = array(
    'DB_HOST'   => '127.0.0.1',
    'DB_PORT'   => '3306',
    'DB_NAME'   => 'jshop',
    'DB_PREFIX' => 'jshop_',
    'DB_USER'   => 'root',
    'DB_PASS'   => '',
);

//错误提示信息
$errorTitle = '出错了';
$errorMsg   = '';
//引入页面
$get = @$_GET['type'] ? $_GET['type'] : $config['indexPage'];
//检测是否已安装
if (file_exists(dirname(dirname(dirname(__FILE__))) . '/config/install.lock') || file_exists(dirname(dirname(dirname(__FILE__))) . '/runtime/install.lock')) {
    $errorTitle = '系统已安装';
    $errorMsg   = '你已经安装过该系统，如需重新安装需要先删除 public/install/install.lock或runtime/install.lock 文件';
    header("Location: /install/install_error.php?errorTitle=" . urlencode($errorTitle) . '&errorMsg=' . $errorMsg);
    exit();
}

//数据库配置
if ($get == $config['importPage']) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $link  = @new mysqli("{$_POST['DB_HOST']}:{$_POST['DB_PORT']}", $_POST['DB_USER'], $_POST['DB_PASS']);
        $error = $link->connect_error;
        if (!is_null($error)) {
            $errorMsg = addslashes($error);
            header("Location: /install/install_error.php?errorTitle=" . urlencode($errorTitle) . '&errorMsg=' . $errorMsg);
            exit();

        } else {
            $link->query("SET NAMES 'utf8'");
            $link->server_info > 5.0 or die("<script>alert('请将您的mysql升级到5.0以上');history.go(-1)</script>");
            $db_config['DB_HOST']   = $_POST['DB_HOST'];
            $db_config['DB_PORT']   = $_POST['DB_PORT'];
            $db_config['DB_NAME']   = $_POST['DB_NAME'];
            $db_config['DB_PREFIX'] = $_POST['DB_PREFIX'];
            $db_config['DB_USER']   = $_POST['DB_USER'];
            $db_config['DB_PASS']   = $_POST['DB_PASS'];
            $_SESSION['db']         = $db_config;

        }
    }
}

//开始安装
if ($get == $config['endPage']) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $return = [
            'status' => false,
            'msg'    => '安装失败',
            'data'   => []
        ];

        if ($_POST['type'] == '1') {
            //连接数据库
            $db   = $_SESSION['db'];
            $link = @new mysqli("{$db['DB_HOST']}:{$db['DB_PORT']}", $db['DB_USER'], $db['DB_PASS']);
            //获取错误信息
            $error = $link->connect_error;
            if (!is_null($error)) {
                $errorMsg      = addslashes($error);
                $return['msg'] = $errorMsg;
                echoJson($return);
            }
            //设置字符集
            $link->query("SET NAMES 'utf8'");
            if ($link->server_info < 5.5) {
                $return['msg'] = '请将您的mysql升级到5.0以上';
                echoJson($return);
            }
            //创建数据库并选中
            if (!$link->select_db($db['DB_NAME'])) {
                $create_sql = 'CREATE DATABASE IF NOT EXISTS ' . $db['DB_NAME'] . ' DEFAULT CHARACTER SET utf8;';
                if (!$link->query($create_sql)) {
                    $return['msg'] = '创建数据库失败';
                    echoJson($return);
                }
                $link->select_db($db['DB_NAME']);
            }
            //导入sql数据并创建表
            if (!file_exists($sqlPath = $config['sqlDir'] . $config['sqlName'] . '.sql')) {
                $return['msg'] = '文件丢失:' . $sqlPath;
                echoJson($return);
            }
            // 获取数据
            $sql_str                     = file_get_contents($sqlPath);
            $sql_array                   = preg_split("/;[\r\n]+/", str_replace($config['prefix'], $db['DB_PREFIX'], $sql_str));
            $total_page                  = ceil(count($sql_array) / $config['limit']);
            $return['data']['page']      = 1;
            $return['data']['totalPage'] = $total_page;
            $return['status']            = true;
            $return['msg']               = '安装中';
            $link->close();
            echoJson($return);

        } elseif ($_POST['type'] == '2') {
            $db   = $_SESSION['db'];
            $link = @new mysqli("{$db['DB_HOST']}:{$db['DB_PORT']}", $db['DB_USER'], $db['DB_PASS']);
            //设置字符集
            $link->query("SET NAMES 'utf8'");
            $link->select_db($db['DB_NAME']);
            // 获取数据
            $sqlPath = $config['sqlDir'] . $config['sqlName'] . '.sql';
            $sql_str = file_get_contents($sqlPath);
            //修改表前缀
            $sql_array = preg_split("/;[\r\n]+/", str_replace($config['prefix'], $db['DB_PREFIX'], $sql_str));
            $start     = ($_POST['page'] - 1) * $config['limit'];
            $end       = $start + $config['limit'];

            //循环query
            $total_page = ceil(count($sql_array) / $config['limit']);
            if ($_POST['page'] <= $total_page) {
                foreach ($sql_array as $k => $v) {
                    if ($k >= $start && $k < $end) {
                        if (!empty($v)) {
                            $link->query($v);
                        }
                    }
                }
            }
            $return['status']            = true;
            $return['msg']               = '安装中';
            $return['data']['page']      = $_POST['page'] + 1;
            $return['data']['totalPage'] = $total_page;
            $return['data']['percent']   = sprintf("%.2f", ($_POST['page'] / $total_page) * 100);
            $link->close();
            echoJson($return);
        } elseif ($_POST['type'] == '3') {//插入默认管理员账号

            $_SESSION['admin_account']  = $_POST['admin_account'];//账号
            $_SESSION['admin_password'] = $_POST['admin_password'];//密码

            $db   = $_SESSION['db'];
            $link = @new mysqli("{$db['DB_HOST']}:{$db['DB_PORT']}", $db['DB_USER'], $db['DB_PASS']);
            //设置字符集
            $link->query("SET NAMES 'utf8'");
            $link->select_db($db['DB_NAME']);
            //插入数据库默认账号密码
            $account      = $_POST['admin_account'];
            $time         = time();
            $password     = md5(md5($_POST['admin_password']) . $time);
            $add_user_sql = "INSERT INTO `" . $db['DB_PREFIX'] . "manage` (`id`, `username`, `password`, `mobile`, `avatar`, `nickname`, `ctime`, `utime`, `status`) VALUES (13, '" . $account . "', '" . $password . "', '', NULL, NULL, " . $time . ", " . $time . ", 1);";
            $link->query($add_user_sql);

            /**插入前端模板菜单**/
            $sqlPath   = $config['sqlDir'] . $config['frontMenuSqlName'] . '.sql';
            $sql_str   = file_get_contents($sqlPath);
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $url       = $http_type . $_SERVER['HTTP_HOST'];
            $sql_str   = str_replace('http://www.b2c.com', $url, $sql_str);
            //修改表前缀
            $sql_array = preg_split("/;[\r\n]+/", str_replace($config['prefix'], $db['DB_PREFIX'], $sql_str));
            foreach ($sql_array as $k => $v) {
                if (!empty($v)) {
                    $link->query($v);
                }
            }

            $return['data']['page']      = 1;
            $return['data']['totalPage'] = 1000;//临时给一个随便默认值
            $return['status']            = true;
            $return['msg']               = '安装中';
            $link->close();
            if ($_POST['demo'] == 'false') {
                doWrite($config);
            }
            echoJson($return);

        } elseif ($_POST['type'] == '4') {//安装演示数据
            $db   = $_SESSION['db'];
            $link = @new mysqli("{$db['DB_HOST']}:{$db['DB_PORT']}", $db['DB_USER'], $db['DB_PASS']);
            //设置字符集
            $link->query("SET NAMES 'utf8'");
            $link->select_db($db['DB_NAME']);
            //判断是否添加演示数据
            $demoPath = $config['sqlDir'] . $config['demoData'] . '.sql';
            $demo_sql = file_get_contents($demoPath);
            //修改表前缀
            $demo_sql_array = preg_split("/;[\r\n]+/", str_replace($config['prefix'], $db['DB_PREFIX'], $demo_sql));
            $total_page     = ceil(count($demo_sql_array) / $config['limit']);
            $start          = ($_POST['page'] - 1) * $config['limit'];
            $end            = $start + $config['limit'];
            //循环query
            if ($_POST['page'] <= $total_page) {
                foreach ($demo_sql_array as $k => $v) {
                    if ($k >= $start && $k < $end) {
                        if (!empty($v)) {
                            $link->query($v);
                        }
                    }
                }
                $return['status']            = true;
                $return['msg']               = '安装中';
                $return['data']['page']      = $_POST['page'] + 1;
                $return['data']['totalPage'] = $total_page;
                $return['data']['percent']   = sprintf("%.2f", ($_POST['page'] / $total_page) * 100);
                $link->close();
                if ($_POST['demo'] == 'true' && $total_page <= $_POST['page']) {
                    doWrite($config);
                }
                echoJson($return);

            } else {
                $return['status']            = true;
                $return['msg']               = '安装中';
                $return['data']['page']      = $_POST['page'] + 1;
                $return['data']['totalPage'] = $total_page;
                $return['data']['percent']   = sprintf("%.2f", ($_POST['page'] / $total_page) * 100);
                echoJson($return);
            }
        }
    }
}

//判断是否有该页面
if (file_exists($url = $get . '.html')) {
    require $url;
} else {
    $errorMsg = '没有该安装页面:' . $url;
    header("Location: /install/install_error.php?errorTitle=" . urlencode($errorTitle) . '&errorMsg=' . urlencode($errorMsg));
    exit();
}
//写入文件
function doWrite($config)
{
    $db     = $_SESSION['db'];
    $db_str = <<<php
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '{$db['DB_HOST']}',
    // 数据库名
    'database'        => '{$db['DB_NAME']}',
    // 用户名
    'username'        => '{$db['DB_USER']}',
    // 密码
    'password'        => '{$db['DB_PASS']}',
    // 端口
    'hostport'        => '{$db['DB_PORT']}',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8mb4',
    // 数据库表前缀
    'prefix'          => '{$db['DB_PREFIX']}',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'collection',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => false,//'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Query类
    'query'           => '\\think\\db\\Query',
];
php;
    //数据库连接配置文件路径
    if (file_exists($config['databaseUrl'])) {
        //删除原配置文件
        unlink($config['databaseUrl']);
    }
    //创建数据库链接配置文件
    file_put_contents($config['databaseUrl'], $db_str);

    //配置H5的host
    /*$scheme = isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'http';
    $host_url = $scheme . '://' . $_SERVER['HTTP_HOST'];
    $h5config = <<<h5
window.host = '{$host_url}';
h5;
    @file_put_contents($config['h5ConfigUrl'], $h5config);*/
    @touch(dirname(dirname(dirname(__FILE__))) . '/runtime/install.lock');
}


function echoJson($data)
{
    echo json_encode($data, true);
    die();
}


/**
 * 过滤XSS攻击
 */
function remove_xss($val)
{
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra  = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val         = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}