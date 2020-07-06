<?php
header('Content-type:text/html;charset=utf-8');
session_start();

//检测是否已安装
if (!file_exists(dirname(dirname(dirname(__FILE__))) . '/config/install.lock') && !file_exists(dirname(dirname(dirname(__FILE__))) . '/runtime/install.lock')) {
    header('Location:/install/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>安装完成 - Jshop小程序商城安装</title>
    <link rel="stylesheet" href="/static/lib/layuiadmin/layui/css/layui.css">
    <link rel="stylesheet" href="style/css/install.css">
</head>
<body>
<div class="layui-main">
    <h1 class="site-h1">安装完成</h1>
    <blockquote class="layui-elem-quote">
        <h1>安装已经成功</h1>

        <p>账号:&nbsp;<?php echo $_SESSION['admin_account']; ?>&nbsp;密码:&nbsp;<?php echo $_SESSION['admin_password']; ?>
            &nbsp;</p>

        <p style="color: red;">请一定要删除或重命名/public/install文件夹!!!</p>
    </blockquote>
    <div class="btn-box">
        <a href="/" class="layui-btn layui-btn-small">前往前台浏览</a>
        <a href="/manage" class="layui-btn layui-btn-small">前往后台登录</a>
    </div>
</div>
</body>
</html>