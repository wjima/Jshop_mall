<?php /*a:3:{s:63:"/Users/apple/W/jshop_b2c/application/b2c/view/common/login.html";i:1528096631;s:69:"/Users/apple/W/jshop_b2c/application/b2c/view/common/mini_header.html";i:1523183572;s:69:"/Users/apple/W/jshop_b2c/application/b2c/view/common/mini_footer.html";i:1523183572;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title></title>
    <link rel="stylesheet" href="/static/lib/layui/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/static/css/register.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/style.css"/>
    <script src="/static/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/static/lib/layui/layui.js"></script>
    <script>
        <!-- 定义全局变量 -->
        var Jshop_Host = window.location.host;
        var Jshop_Image = "<?php echo url('images/uploadImage'); ?>";
    </script>
    <script src="/static/js/jshop.js"></script>
</head>

<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo"><img src="/static/images/logo3.png"/> </div>
        <div class="state">
        	为确保您账户的安全及正常使用，依《网络安全法》相关要求，会员账户需绑定手机。
        </div>
    </div>
</div>
<div class="mini-content">
<div class="login-content">
	<div class="login-content-mid">
		<div class="layui-container">
			<div>
				<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
					<ul class="layui-tab-title">
						<li class="layui-this">普通登陆</li>
						<li>手机短信登陆</li>
					</ul>
					<div class="layui-tab-content">
						<div class="layui-tab-item layui-show">
							<div class="layui-tab-content layui-form-pane">
								<form class="layui-form" action="" method="post">
									<div class="layui-form-item">
										<label class="layui-form-label">手机号：</label>
										<div class="layui-input-inline">
											<input type="tel" name="mobile" lay-verify="required|phone" autocomplete="off" class="layui-input">
										</div>
									</div>
									<div class="layui-form-item">
										<label class="layui-form-label">密码：</label>
										<div class="layui-input-inline">
											<input type="password" name="password" autocomplete="off" class="layui-input">
										</div>
									</div>
                                    <div class="layui-form-item" style="display:none;" id="captcha_div">
                                        <label class="layui-form-label">验证码：</label>
                                        <div class="layui-input-inline" style="width:60px;">
                                            <input type="text" name="captcha" autocomplete="off" id="captcha_ipt" class="layui-input">
                                        </div>
                                        <div class="layui-input-inline" style="width:150px;">
                                            <img src="<?php echo captcha_src(); ?>" id="captcha_img" onclick="setCaptcha()" title="点击更换验证码" alt="captcha" style="max-width:150px;cursor:pointer;" />
                                        </div>
                                    </div>
									<div class="layui-form-item">
										<div class="layui-input-inline">
											<input class="layui-btn layui-btn-fluid" lay-submit lay-filter="password_login" type="submit" value="登录" />
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="layui-tab-item">
							<div class="layui-tab-content layui-form-pane">
								<form class="layui-form" action="" method="post">
									<div class="layui-form-item">
										<label class="layui-form-label">手机号：</label>
										<div class="layui-input-inline">
											<input type="tel" id="mobile" name="mobile" lay-verify="required|phone" autocomplete="off" class="layui-input">
										</div>
									</div>
									<div class="layui-form-item identifying-code">
										<label class="layui-form-label">短信验证码：</label>
										<div class="layui-input-inline">
											<input type="text" name="code" lay-verify="" autocomplete="off" class="layui-input">
											<input id="btn" class="layui-btn" type="button" style="width:112px;" value="获取验证码" onclick="setSms(this)" />
										</div>
									</div>
									<div class="layui-form-item">
										<div class="layui-input-inline">
											<input class="layui-btn layui-btn-fluid" lay-submit lay-filter="sms_login" type="submit" value="登录" />
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				没有账号？
				<a href="<?php echo url('b2c/common/reg'); ?>">立即注册</a>

			</div>
		</div>
	</div>
</div>	
		<script>
			layui.use(['element', 'form'], function() {
				//手机号码登陆
				layui.form.on('submit(sms_login)', function(data) {
					$.ajax({
						type: "POST",
						url: "<?php echo url('b2c/common/smsLogin'); ?>",
						data: data.field,
						success: function(data) {
							if(data.status) {
								layer.msg('登陆成功，跳转中...');
								setTimeout("window.location.href='" + data.data + "'; ", 1500);
							} else {
								layer.msg(data.msg);
							}
						}
					});
					return false;
				});
				//密码登陆
				layui.form.on('submit(password_login)', function(data) {
					$.ajax({
						type: "POST",
						url: "<?php echo url('b2c/common/login'); ?>",
						data: data.field,
						success: function(data) {
							if(data.status) {
								layer.msg('登陆成功，跳转中...');
								setTimeout("window.location.href='" + data.data + "'; ", 1500);
							} else {
                                if(data.data == '10013' || data.data == '10012'){
                                    $('#captcha_div').show();
                                }
                                setCaptcha();
								layer.msg(data.msg);
							}
						}
					});
					return false;
				});
			});
			//发送短信验证码
			function setSms(obj) {
				$.ajax({
					type: "POST",
					url: "<?php echo url('b2c/common/sms'); ?>",
					data: "mobile=" + $('#mobile').val() + "&code=login",
					success: function(data) {
						if(data.status) {
							sms_btn_time($(obj));
						}
						layer.msg(data.msg);
					}
				});
			}
            //更改验证码图片
            function setCaptcha() {
                $('#captcha_ipt').val("");
                var timestamp=new Date().getTime();
                $('#captcha_img').attr('src','<?php echo captcha_src(); ?>?'+timestamp);
            }
		</script>
		</div>	
<div class="login-footer">
	<div class="w">
	    <div id="footer-2013">
	        <div class="links">
	            <a rel="nofollow" href="javascript:void(0);">
	                关于我们
	            </a>
	            |
	            <a rel="nofollow" href="javascript:void(0);">
	                联系我们
	            </a>
	            |
	            <a rel="nofollow" href="javascript:void(0);">
	                人才招聘
	            </a>
	            |
	            <a rel="nofollow" href="javascript:void(0);">
	                商家入驻
	            </a>
	            |
	            <a href="javascript:void(0);">
	                友情链接
	            </a>
	            |
	            <a href="javascript:void(0);">
	                销售联盟
	            </a>
	        </div>
	        <div class="copyright">
	            Copyright&nbsp;©&nbsp;2017-2018&nbsp;&nbsp;jshop云商 &nbsp;版权所有
	        </div>
	    </div>
	</div>
</div>
</body>
</html>