<?php
header('Content-type:text/html;charset=utf-8');
session_start();
//错误提示信息
$errorTitle = $_GET['errorTitle'] ? $_GET['errorTitle'] : '出错了';
$errorMsg   = $_GET['errorMsg'] ? $_GET['errorMsg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>安装出错</title>
    <link rel="stylesheet" href="/static/lib/layuiadmin/layui/css/layui.css">
    <link rel="stylesheet" href="style/css/install.css">
</head>
<body>
<div class="layui-main">
    <h1 class="site-h1"><?php echo $errorTitle ? $errorTitle : '出错了'; ?></h1>
    <blockquote class="layui-elem-quote"><?php echo $errorMsg ? $errorMsg : '错误信息'; ?></blockquote>
    <div class="btn-box">
        <a class="layui-btn layui-btn-small layui-btn-normal" href="javascript:history.go(-1);">返回</a>
    </div>
</div>
</body>
</html>