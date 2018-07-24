<?php /*a:3:{s:65:"/Users/apple/W/jshop_b2c/application/manage/view/index/index.html";i:1527476808;s:60:"/Users/apple/W/jshop_b2c/application/manage/view/layout.html";i:1528101664;s:65:"/Users/apple/W/jshop_b2c/application/manage/view/common/menu.html";i:1528096631;}*/ ?>
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
            <script src="/static/lib/echarts/build/dist/echarts.js"></script>
<?php if(($is_author != 'true') && ($store_type == 2)): ?>
<div class="warning-msg">
	<div class="warning-msg-content">
		<i class="iconfont icon-tanhao"></i><p>您还未绑定微信小程序，请及时绑定并体验更多功能。<a href="javascript:void(0);" id="bind">[立即绑定]</a></p>
	</div>
</div>
<?php endif; ?>
<div class="layadmin-tabsbody-item layui-show">
	<div class="layui-fluid">
		<div class="layui-row layui-col-space15">
			<div class="layui-col-md8">
				<div class="layui-row layui-col-space15">
					<div class="layui-col-md6">
						<div class="layui-card">
							<div class="layui-card-header">
								<i class="iconfont icon-caozuo"></i>快捷操作
							</div>
							<div class="layui-card-body">
								<div class="layui-carousel layadmin-carousel layadmin-shortcut">
									<ul class="layui-row layui-col-space10 layui-this">
										<li class="layui-col-xs3">
											<a href="<?php echo url('goods/index'); ?>">
												<i class="iconfont icon-goods"></i>
												<cite>商品</cite>
											</a>
										</li>
										<li class="layui-col-xs3">
											<a href="<?php echo url('order/index'); ?>">
												<i class="iconfont icon-dingdan1"></i>
												<cite>订单</cite>
											</a>
										</li>
										<li class="layui-col-xs3">
											<a href="<?php echo url('user/index'); ?>">
												<i class="iconfont icon-user"></i>
												<cite>会员</cite>
											</a>
										</li>
										<li class="layui-col-xs3">
											<a href="<?php echo url('notice/index'); ?>">
												<i class="iconfont icon-gonggao"></i>
												<cite>公告</cite>
											</a>
										</li>
										<li class="layui-col-xs3">
											<a href="<?php echo url('coupon/index'); ?>">
												<i class="iconfont icon-coupon"></i>
												<cite>促销</cite>
											</a>
										</li>
										<li class="layui-col-xs3">
											<a href="<?php echo url('ship/index'); ?>">
												<i class="iconfont icon-bangzhupeisongfuwu"></i>
												<cite>配送</cite>
											</a>
										</li>
										<li class="layui-col-xs3">
											<a href="<?php echo url('payments/index'); ?>">
												<i class="iconfont icon-zhifu-01"></i>
												<cite>支付方式</cite>
											</a>
										</li>
										<li class="layui-col-xs3">
											<a href="<?php echo url('setting/index'); ?>">
												<i class="iconfont icon-review"></i>
												<cite>店铺设置</cite>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="layui-col-md6">
						<div class="layui-card">
							<div class="layui-card-header">
								<i class="iconfont icon-daiban"></i>待办事项
							</div>
							<div class="layui-card-body">
								<div class="layui-carousel layadmin-carousel layadmin-backlog">
									<ul class="layui-row layui-col-space10 layui-this">
										<li class="layui-col-xs6">
											<a href="<?php echo url('order/index'); ?>" class="layadmin-backlog-body">
												<h3>待支付</h3>
												<p><cite><?php echo htmlentities($unpaid_count); ?></cite></p>
											</a>
										</li>
										<li class="layui-col-xs6">
											<a href="<?php echo url('order/index'); ?>" class="layadmin-backlog-body">
												<h3>待发货</h3>
												<p><cite><?php echo htmlentities($unship_count); ?></cite></p>
											</a>
										</li>
										<li class="layui-col-xs6">
											<a href="<?php echo url('bill_aftersales/index'); ?>" class="layadmin-backlog-body">
												<h3>待售后</h3>
												<p><cite><?php echo htmlentities($after_sales_count); ?></cite></p>
											</a>
										</li>
										<li class="layui-col-xs6">
											<a href="<?php echo url('goods/index'); ?>" class="layadmin-backlog-body">
												<h3>库存报警</h3>
												<p><cite><?php echo htmlentities($goods_statics['totalWarn']); ?></cite></p>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>					
				</div>
			</div>
			<div class="layui-col-md4">
				<div class="layui-card">
					<div class="layui-card-header">
						<i class="iconfont icon-gonggao"></i>系统公告
					</div>
					<div class="layui-card-body layui-text" id="view">
<!--						<table class="layui-table">
							<tbody>
								<tr>
									<td><i>1</i>最新公告内容最新公告内容</td>
								</tr>
								<tr>
									<td><i>2</i>最新公告内容最新公告内容</td>
								</tr>
								<tr>
									<td><i>3</i>最新公告内容最新公告内容</td>
								</tr>
								<tr>
									<td><i>3</i>最新公告内容最新公告内容</td>
								</tr>
								<tr>
									<td><i>3</i>最新公告内容最新公告内容</td>
								</tr>
							</tbody>
						</table>-->
					</div>
				</div>
			</div>
			<div class="layui-col-md6">
				<div class="layui-card">
					<div class="layui-card-header">
						<i class="iconfont icon-dingdan1"></i>订单统计
					</div>
					<div class="layui-card-body">
						<div id="graphic" class="">
					        <div id="main" class="main" style="height: 400px;"></div>
					    </div>
					</div>
				</div>
			</div>
			<div class="layui-col-md6">
				<div class="layui-card">
					<div class="layui-card-header">
						<i class="iconfont icon-user"></i>会员统计
					</div>
					<div class="layui-card-body">
						<div id="graphics" class="">
					        <div id="users" class="main" style="height: 400px;"></div>
					    </div>
					</div>
				</div>
			</div>

			<div class="layui-col-md6">
				<div class="layui-card">
					<div class="layui-card-header">
						<i class="iconfont icon-dingdan1"></i>最近登录
					</div>

					<div class="layui-card-body" id="loginLog"></div>

				</div>
			</div>

			<div class="layui-col-md6">
				<div class="layui-card">
					<div class="layui-card-header">
						<i class="iconfont icon-dingdan1"></i>操作日志
					</div>
					<div class="layui-card-body" id="oplog-table"></div>
				</div>
			</div>

			<script id="demo" type="text/html">
				<table class="layui-table">
					<tbody>
					{{# for(var i = 0; i < d.length; i++){ }}
					<tr>
						<td><i>{{i+1}}</i><a class="notice" href="javascript:;" id="{{d[i].id}}">{{ d[i].title }}</a></td>
					</tr>
					{{#  } }}
					{{#  if(d.length === 0){ }}
					没有最新的数据
					{{#  } }}
					</tbody>
				</table>
			</script>


            <script id="log" type="text/html">

                {{#  if(d.length === 0){ }}
                没有历史记录
                {{#  }else{ }}
                <table class="layui-table">
                    <thead>
                    <tr>
                        <th>状态</th>
                        <th>记录时间</th>
                        <th>登录IP</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{#  layui.each(d, function(index, item){ }}
                    <tr>
                        <td>{{ item.state }}</td>
                        <td>{{ item.ctime }}</td>
                        <td>{{ item.ip }}</td>
                    </tr>
                    {{#  }); }}
                    </tbody>
                </table>
                {{# } }}
            </script>

			<script id="oplog" type="text/html">

				{{#  if(d.length === 0){ }}
				没有操作记录
				{{#  }else{ }}
				<table class="layui-table">
					<thead>
					<tr>
						<th>操作员</th>
						<th>操作时间</th>
						<th>操作内容</th>
						<th>操作IP</th>
					</tr>
					</thead>
					<tbody>
					{{#  layui.each(d, function(index, item){ }}
					<tr>
						<td>{{ item.username }}</td>
						<td>{{ item.ctime }}</td>
						<td>{{ item.desc }}</td>
						<td>{{ item.ip }}</td>
					</tr>
					{{#  }); }}
					</tbody>
				</table>
				{{# } }}
			</script>
			<script type="text/javascript">
				layui.use(['laytpl','layer'],function(){
					var $ = layui.$, laytpl = layui.laytpl,layer = layui.layer;

                    //获取总后台公告消息列表
					$.get("<?php echo url('manage/BackstageNotice/show'); ?>?_ajax=1&limit=5",function(data){
						var getTpl = demo.innerHTML,view = document.getElementById('view');
						laytpl(getTpl).render(data.data, function(html){
							view.innerHTML = html;
						});
					});

                    //获取历史登录记录
                    $.get("<?php echo url('seller/User/userLogList',array('state'=>1)); ?>",function(data){
                        var getTpl = log.innerHTML,view = document.getElementById('loginLog');
                        laytpl(getTpl).render(data.data,function(html){
                            view.innerHTML = html;
                        })
                    });
					JsGet("<?php echo url('seller/OperationLog/getLastLog'); ?>",function (data) {
                        var getTpl = oplog.innerHTML,view = document.getElementById('oplog-table');
                        laytpl(getTpl).render(data.data,function(html){
                            view.innerHTML = html;
                        })
                    });
                    //点击获取公告内容
					$(document).on('click','.notice',function(){
						var id = $(this).attr('id');
						var loadIndex = layer.load(1, {shade: 0.8});
						$.get("<?php echo url('manage/BackstageNotice/getInfo'); ?>?id="+id,function(res) {
							layer.open({
								type: 1,
								title: '公告信息',
								shadeClose: true,
								shade: 0.8,
								maxmin: false, //开启最大化最小化按钮
								area: ['700px', '450px'],
								content: '<div style="padding: 50px; line-height: 22px;font-weight: 300;">' +
								'<h2 style="text-align: center">'+res.title+'</h2><br>' +
								''+res.content+'</div>'
							});

						});
						layer.close(loadIndex);
					});

				});
		        // 路径配置
		        require.config({
		            paths: {
		                echarts: '/static/lib/echarts/build/dist'
		            }
		        });
		        require(
		            ['echarts','echarts/chart/line'],
		            function (ec) {
		                var myChart = ec.init(document.getElementById('main'));
		                var option = {
		                	title: {text:'最近7天订单量统计'},
		                    tooltip: {show:true},
		                    legend: {},
		                    yAxis: [{type:'value'}],
                            xAxis: [],
		                    series: []
		                };
                        $.get('<?php echo url("order/statistics"); ?>').done(function (data) {
                            myChart.setOption({
                                legend: data.legend,
                                xAxis: data.xAxis,
                                series: data.series
                            });
                        });
		                myChart.setOption(option); 
		            }
		        );
		        require(
		            [
		                'echarts',
		                'echarts/chart/line' // 使用柱状图就加载bar模块，按需加载
		            ],
		            
		            
		            function (ec) {
		                // 基于准备好的dom，初始化echarts图表
		                var myChart = ec.init(document.getElementById('users'));
		                
		                var option = {
		                	
		                    tooltip: {
		                        show: true
		                    },
		                    legend: {},
		                    xAxis : [],
		                    yAxis : [],
		                    series : []
		                };
                        $.get('<?php echo url("user/statistics"); ?>').done(function (data) {
                            myChart.setOption({
                                legend: data.legend,
                                xAxis: data.xAxis,
                                series: data.series
                            });
                        });
		                // 为echarts对象加载数据 
		                myChart.setOption(option); 
		            }
		        );
		    </script>
		</div>
	</div>
</div>
<?php if(($closeauthor == 'false') && ($is_author == 'false') && ($store_type == 2)): ?>
<script>
    layui.use(['laytpl','layer'],function(){
        JsGet("<?php echo url('wechat/index'); ?>",function (e) {
            if(e.status){
                window.box = layer.open({
                    type: 1,
                    content: e.data,
                    area: ['400px', '300px'],
                    title: "请先绑定小程序",
                    closeBtn: 1,
                    shadeClose: false,
                    btnAlign:'c',
                    btn:['不绑定，先逛逛'],
                    yes:function (index) {
                        //随便逛逛
                        JsGet("<?php echo url('wechat/closeAuthor'); ?>",function (e) {
                            layer.closeAll();
                        });
                    }
                });
			}else{
                layer.closeAll();
			}
        });
	})
</script>
<?php endif; ?>
<script>
    $("#bind").click(function () {
        JsGet("<?php echo url('wechat/index'); ?>",function (e) {
            window.box = layer.open({
                type: 1,
                content: e.data,
                area: ['400px', '300px'],
                title: "绑定小程序",
                closeBtn: 1,
                shadeClose: false,
                btnAlign:'c',
                btn:['不绑定，先逛逛'],
                yes:function (index) {
                    //随便逛逛
                    JsGet("<?php echo url('wechat/closeAuthor'); ?>",function (e) {
                        layer.closeAll();
                    });
                }
            });
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