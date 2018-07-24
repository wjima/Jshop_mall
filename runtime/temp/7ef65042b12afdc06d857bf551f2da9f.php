<?php /*a:3:{s:64:"/Users/apple/W/jshop_b2c/application/manage/view/user/index.html";i:1527476808;s:60:"/Users/apple/W/jshop_b2c/application/manage/view/layout.html";i:1528101664;s:65:"/Users/apple/W/jshop_b2c/application/manage/view/common/menu.html";i:1528096631;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="novice@jihainet.com"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Jshop管理平台</title>
    <link rel="stylesheet" type="text/css" href="/static/css/iconfont.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/iconfont2.css"/>
    <link rel="stylesheet" href="/static/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/static/lib/layui/css/layui.seller.css">
    <link rel="stylesheet" type="text/css" href="/static/css/style.css"/>
    <script src="/static/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/static/lib/layui/layui.js"></script>
    <script>
        <!-- 定义全局变量 -->
        var Jshop_Host = "<?php echo htmlentities($jshopHost); ?>";
        var Jshop_Image = "<?php echo url('images/uploadImage'); ?>";
        var manage_Image = "<?php echo url('images/manage'); ?>";
    </script>
    <script src="/static/js/jshop.js"></script>
    <script type="text/javascript" charset="utf-8" src="/static/js/ue/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/static/js/ue/ueditor.all.min.js"> </script>


</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">    
	<ul class="layui-nav">
        <li class="layui-nav-item" lay-unselect="">
            <a href="javascript:;">
                <img src="<?php echo _sImage(session('user.avatar')); ?>" class="layui-nav-img"><?php echo session('manage.username'); ?>
            </a>
            <dl class="layui-nav-child">
				<dd><a href="<?php echo url('Manage/information'); ?>">个人中心</a></dd>
			    <dd><a href="<?php echo url('Common/logout'); ?>">退出</a></dd>
			</dl>
        </li>
    </ul>
    <div class="layui-side layui-bg-black">
    	
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree" lay-filter="test">
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="<?php echo url('index/index'); ?>"><i class="iconfont icon-shouyeshouye"></i>首页</a>
    </li>
    <?php if($menu): foreach($menu as $k=>$v): ?>
    <li class="layui-nav-item <?php if($v['selected']): ?> layui-nav-itemed<?php endif; ?>">
        <a class="menu-icon-<?php echo strtolower($v['code']); ?>" href="javascript:;"><i class="iconfont icon-<?php echo strtolower($v['code']); ?>"></i><?php echo htmlentities($v['name']); ?></a>
        <dl class="layui-nav-child">
            <?php foreach($v['children'] as $x=>$y): ?>
            <dd class="<?php if($y['selected']): ?> layui-this<?php endif; ?>">
                <a href="<?php echo htmlentities(get_operation_url($y['id'])); ?>"><?php echo htmlentities($y['name']); ?></a>
            </dd>
            <?php endforeach; ?>
        </dl>
    </li>
    <?php endforeach; endif; ?>

</ul>
        </div>
    </div>

    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            <span class="layui-breadcrumb" style="visibility: visible;">
                <i class="iconfont icon-shouyeshouye"></i>
                <?php if(is_array($nav) || $nav instanceof \think\Collection || $nav instanceof \think\Paginator): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['url'] == ""): ?>
                        <a><cite><?php echo htmlentities($vo['name']); ?></cite></a>
                    <?php else: ?>
                        <a href="<?php echo htmlentities($vo['url']); ?>"><?php echo htmlentities($vo['name']); ?></a>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </span>
            <form class="layui-form seller-form"  action="" >
    <div class="layui-form-item">
        <div class="layui-inline" style="display:none;">
            <label class="layui-form-label">账号：</label>
            <div class="layui-input-inline">
                <input type="text" name="username" lay-verify="title" style="width:100px;" placeholder="请输入账号" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">手机号码：</label>
            <div class="layui-input-inline seller-inline-3">
                <input type="text" name="mobile" lay-verify="title" placeholder="请输入手机号码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">性别：</label>
            <div class="layui-input-inline seller-inline-2" >
                <select name="sex" lay-verify="">
                    <option value=""></option>
                    <option value="1">男</option>
                    <option value="2">女</option>
                    <option value="3">其他</option>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">生日：</label>
            <div class="layui-input-inline seller-inline-2">
                <input type="text" name="birthday" lay-verify="title" placeholder="生日日期" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">昵称：</label>
            <div class="layui-input-inline seller-inline-2">
                <input type="text" name="nickname" lay-verify="title" placeholder="用户昵称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">状态：</label>
            <div class="layui-input-inline seller-inline-3">
                <select name="status" lay-verify="">
                    <option value=""></option>
                    <option value="1">正常</option>
                    <option value="2">停用</option>
                </select>
            </div>
        </div>
        <div class="layui-inline">
	        <div class="layui-input-block">
	            <button class="layui-btn layui-btn-sm" lay-submit lay-filter="*"><i class="iconfont icon-chaxun"></i>筛选</button>
	        </div>
        </div>
    </div>
</form>

<div class="table-body">
    <table id="userTable" lay-filter="test"></table>
</div>

<script>
    layui.use(['form', 'layedit', 'laydate','table'], function(){
        layui.table.render({
            elem: '#userTable',
            height: 'full-330',
            cellMinWidth: '80',
            page: 'true',
            limit:'20',
            url: "<?php echo url('user/index'); ?>?_ajax=1",
            id:'userTable',
            cols: [[
                {type:'numbers'},
//                {field:'username', width:100,title:'账号'},
                {field:'mobile', width:160,title:'手机号码'},
                {field:'sex', width:80,minWidth: 50,title:'性别'},
                {field:'birthday',width:200,title:'生日'},
                {field:'avatar',title:'头像',
                    templet: function(d){
                        if (d.avatar) {
                            return '<a href="javascript:void(0);" onclick=viewImage("'+d.avatar+'")><image style="max-width:30px;max-height:30px;" src="'+d.avatar+'"/></a>';
                        }else{
                            return '<a href="javascript:void(0);" onclick=viewImage("<?php echo config('jshop.default_image'); ?>")><image style="max-width:30px;max-height:30px;" src="<?php echo config('jshop.default_image');?>"/></a>';
                        }
                    }
                },
                {field:'nickname',title:'昵称'},
                {field:'status',title:'状态'}
            ]]
        });
        layui.form.on('submit(*)', function(data){
            layui.table.reload('userTable', {
                where: data.field
                ,page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });
    });
</script>

        </div>
    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        <p>吉海科技 © jihainet.com - 版权所有</p>
        <a href="">问题请求|产品建议请上吉海论坛</a>
        <a href="https://www.kancloud.cn/jihainet/jshop" target="_blank">《使用手册》</a>
    </div>
</div>
<script>
    layui.use('element', function() {
        var element = layui.element;

    });
    
</script>
</body>
</html>