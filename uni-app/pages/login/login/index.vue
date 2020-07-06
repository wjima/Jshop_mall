<template>
	<view class="content">
		<view class="login-t"><image class="login-logo" :src="logoImage" mode="aspectFill"></image></view>
		<view class="login-m">
			<view class="login-item">
				<input type="number" v-model="mobile" :maxlength="maxMobile" placeholder="请输入手机号码" focus placeholder-class="login-item-i-p fsz26" />
			</view>
			<view class="login-item flc">
				<input class="login-item-input" placeholder-class="login-item-i-p fsz26" type="text" v-model="code" placeholder="请输入验证码" />
				<view :class="sendCodeBtn" @click="sendCode" v-if="verification">发送验证码</view>
				<view class="btn btn-g" v-if="!verification">{{ timer }} 秒后重新获取</view>
			</view>
		</view>
		<view class="login-b">
			<!-- #ifdef H5|APP-PLUS|APP-PLUS-NVUE -->
			<view v-if="user_wx_id">
				<button :class="regButtonClass" @click="toBind()" hover-class="btn-hover">登录</button>
				<view class="color-9 fsz24 agreement">
					登录即代表你同意
					<text @click="goAgreement()" class="color-o">用户协议</text>
					和
					<text @click="goPrivacy()" class="color-o">隐私政策</text>
				</view>
			</view>

			<view v-else>
				<button :class="regButtonClass" @click="login()" hover-class="btn-hover">登录</button>
				<view class="color-9 fsz24 agreement">
					登录即代表你同意
					<text @click="goAgreement()" class="color-o">用户协议</text>
					和
					<text @click="goPrivacy()" class="color-o">隐私政策</text>
				</view>
				<view class="login-other flc">
					<view class="fz12 item" @click="selectLoginType">密码登录</view>
					<view class="fz12 item" @click="toReg">注册</view>
				</view>
			</view>
			<!-- #endif -->
			<!-- #ifdef MP -->
			<button :class="regButtonClass" @click="showTopTips()" hover-class="btn-hover">登录</button>
			<!-- #endif -->
		</view>
	</view>
</template>

<script>
import { goBack, jumpBackPage } from '@/config/mixins.js';
export default {
	mixins: [goBack, jumpBackPage],
	data() {
		return {
			maxMobile: 11,
			mobile: '', // 用户手机号
			code: '', // 短信验证码
			user_wx_id: '', //授权id
			verification: true, // 通过v-show控制显示获取还是倒计时
			timer: 60, // 定义初始时间为60s
			btnb: 'btn btn-square btn-c btn-all', //按钮背景
			type: '', // 有值是第三方登录账号绑定
			isWeixinBrowser: this.$common.isWeiXinBrowser()
		};
	},
	onLoad(option) {
		if (option.user_wx_id) {
			this.user_wx_id = option.user_wx_id;
			uni.setNavigationBarTitle({
				title: '绑定手机号'
			});
		}
	},
	computed: {
		// 验证手机号
		rightMobile() {
			let res = {};
			if (!this.mobile) {
				res.status = false;
				res.msg = '请输入手机号';
			} else if (!/^1[3456789]{1}\d{9}$/gi.test(this.mobile)) {
				res.status = false;
				res.msg = '手机号格式不正确';
			} else {
				res.status = true;
			}
			return res;
		},
		// 动态计算发送验证码按钮样式
		sendCodeBtn() {
			let btn = 'btn btn-g';
			if (this.mobile.length === this.maxMobile && this.rightMobile.status) {
				return btn + ' btn-b';
			} else {
				return btn;
			}
		},
		// 动态更改登录按钮bg
		regButtonClass() {
			return this.mobile && this.mobile.length === this.maxMobile && this.code ? this.btnb + ' btn-b' : this.btnb;
		},
		logoImage() {
			return this.$store.state.config.shop_logo;
		}
	},
	onShow() {
		let _this = this;
		let userToken = _this.$db.get('userToken');
		if (userToken) {
			uni.switchTab({
				url: '/pages/member/index/index'
			});
			return true;
		}
		_this.timer = parseInt(_this.$db.get('timer'));
		if (_this.timer != null && _this.timer > 0) {
			_this.countDown();
			_this.verification = false;
		}
	},
	methods: {
		// 发送短信验证码
		sendCode() {
			if (!this.rightMobile.status) {
				this.$common.errorToShow(this.rightMobile.msg);
			} else {
				this.$common.loadToShow('发送中...');
				setTimeout(() => {
					this.$common.loadToHide();
					this.$api.sms({ mobile: this.mobile, code: 'login' }, res => {
						if (res.status) {
							this.timer = 60;
							this.verification = false;
							this.$common.successToShow(res.msg);
							this.countDown(); // 执行验证码计时
							// this.btnb = 'btn btn-square btn-all btn-b';
						} else {
							this.$common.errorToShow(res.msg);
						}
					});
				}, 1000);
			}
		},
		// 去注册
		toReg() {
			this.$common.redirectTo('/pages/login/register/index');
		},
		// 验证码倒计时
		countDown() {
			let auth_timer = setInterval(() => {
				// 定时器设置每秒递减
				this.timer--; // 递减时间
				uni.setStorage({
					key: 'timer',
					data: this.timer,
					success: function() {}
				});
				if (this.timer <= 0) {
					this.verification = true; // 60s时间结束还原v-show状态并清除定时器
					clearInterval(auth_timer);
				}
			}, 1000);
		},
		// 登录
		login() {
			var _this = this;
			if (!_this.rightMobile.status) {
				_this.$common.errorToShow(_this.rightMobile.msg);
			} else {
				// 短信验证码登录
				if (!_this.code) {
					_this.$common.errorToShow('请输入短信验证码!');
				} else {
					let data = {
						mobile: _this.mobile,
						code: _this.code
					};

					let invicode = _this.$db.get('invitecode');
					if (invicode) {
						data.invitecode = invicode;
					}

					_this.$api.smsLogin(data, res => {
						if (res.status) {
							this.$db.set('userToken', res.data);
							_this.redirectHandler();
						} else {
							_this.$common.errorToShow(res.msg);
						}
					});
				}
			}
		},
		// 重定向跳转 或者返回上一个页面
		redirectHandler() {
			this.$common.successToShow('登录成功!', () => {
				this.$db.set('timer', 0);
				this.$db.del('invitecode');
				// this.handleBack();
				uni.navigateBack({
				    delta: 1
				});
			});
		},
		// 跳转到普通登录
		toLogin() {
			uni.navigateTo({
				url: '../../login/login/index'
			});
		},
		//提交按钮
		showTopTips: function() {
			let _this = this;
			if (_this.mobile == '') {
				_this.$common.errorToShow('请输入手机号码');
				return false;
			}
			if (this.code == '') {
				_this.$common.errorToShow('请输入验证码');
				return false;
			}
			if (_this.user_wx_id == 0) {
				_this.$common.errorToShow('登录失败，请稍后再试', function() {
					uni.navigateBack({
						delta: 1
					});
				});
				return false;
			}
			var platform = 2;
			//1就是h5登陆（h5端和微信公众号端），2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc
			// #ifdef MP-ALIPAY
			platform = 3;
			// #endif
			// #ifdef APP-PLUS || APP-PLUS-NVUE
			platform = 4;
			// #endif
			var data = {
				mobile: _this.mobile,
				code: _this.code,
				platform: platform, //平台id，标识是小程序登陆的
				user_wx_id: _this.user_wx_id //微信小程序接口存不了session，所以要绑定的id只能传到前台
			};
			//有推荐码的话，带上
			var invitecode = _this.$db.get('invitecode');
			if (invitecode) {
				data.invitecode = invitecode;
			}
			_this.$api.smsLogin(data, function(res) {
				if (res.status) {
					_this.$db.set('userToken', res.data);
					_this.redirectHandler();
				} else {
					//报错了
					_this.$common.errorToShow(res.msg);
				}
			});
		},
		// 微信小程序第三方登录账号绑定
		toBind() {
			if (this.mobile == '') {
				this.$common.errorToShow('请输入手机号码');
				return false;
			}
			if (this.code == '') {
				this.$common.errorToShow('请输入验证码');
				return false;
			}

			let data = {
				mobile: this.mobile,
				code: this.code,
				user_wx_id: this.user_wx_id
			};

			// 获取邀请码
			let invicode = this.$db.get('invitecode');
			if (invicode) {
				data.invitecode = invicode;
			}

			this.$api.smsLogin(data, res => {
				if (res.status) {
					this.$db.set('userToken', res.data);
					this.redirectHandler();
				} else {
					this.$common.errorToShow(res.msg);
				}
			});
		},
		// 切换登录方式
		selectLoginType() {
			this.$common.redirectTo('./index1');
		},
		// 跳转到用户协议
		goAgreement() {
			let articleId = this.$store.state.config.user_agreement_id;
			this.$common.navigateTo('/pages/article/index?id_type=1&id=' + articleId);
		},
		// 跳转到隐私政策
		goPrivacy() {
			let articleId = this.$store.state.config.privacy_policy_id;
			this.$common.navigateTo('/pages/article/index?id_type=1&id=' + articleId);
		},
		goForgetpwd() {
			this.$common.navigateTo('/pages/login/forget/forget');
		}
	}
};
</script>

<style lang="scss">
.content {
	/*  #ifdef  H5  */
	height: calc(100vh - 90upx);
	/*  #endif  */
	/*  #ifndef  H5  */
	height: 100vh;
	/*  #endif  */
	background-color: #fff;

	padding: 0upx 100upx;
}
.login-t {
	text-align: center;
	padding: 50upx 0;
}
.login-logo {
	width: 180upx;
	height: 180upx;
	border-radius: 20upx;
	background-color: #f8f8f8;
}
.login-m {
	margin-bottom: 100upx;
}
.login-item {
	border-bottom: 2upx solid #d0d0d0;
	overflow: hidden;
	padding: 10upx;
	color: #333;
	margin-bottom: 30upx;
}
.login-item-input {
	display: inline-block;
	flex: 1;
	box-sizing: border-box;
}
.login-item .btn {
	border: none;
	width: 40%;
	text-align: right;
	padding: 0;
	&.btn-b {
		background: none;
		color: #333 !important;
	}
}
.login-b .btn {
	color: #999;
}
.btn-b {
	color: #fff !important;
}
.login-other {
	margin-bottom: 40upx;
	.item {
		padding: 20upx 0;
	}
}
.btn-square {
	color: #333;
}

.agreement {
	margin: 20rpx 0;
	text-align: center;
	.color-o {
		margin: 0 10rpx;
	}
}
.goforgetpwd {
	width: 100%;
	text-align: right;
}
</style>
