<template>
	<view class="content">
		<view class="login-t">
			<image class="login-logo" :src="logoImage" mode="aspectFill"></image>
		</view>
		<view>
			<view class="login-m">
				<view class="login-item">
					<input type="text" v-model="mobile" placeholder="请输入手机号码或账号" placeholder-class="login-item-i-p fsz26" style="width: 100%" />
				</view>
				<view class="login-item flc">
					<input class="login-item-input" :password="true" placeholder-class="login-item-i-p fsz26" type="text" v-model="pwd"
					 placeholder="请输入密码" />
				</view>
				<view class="login-item" v-if="isCaptcha">
					<input class="login-item-input" placeholder-class="login-item-i-p fsz26" type="text" v-model="captcha" placeholder="输入验证码"
					 style="width: 100%" />
					<img class="codeimg" :src="captchaUrl" alt="" @click="getCodeImg" />
				</view>
				<view class="goforgetpwd color-9 fsz24" @click="goForgetpwd()">忘记密码？</view>
			</view>
			<view class="login-b">
				<button :class="loginButtonClass" @click="loginHandler" hover-class="btn-hover">登录</button>
				<view class="color-9 fsz24 agreement">
					登录即代表你同意
					<text @click="goAgreement()" class="color-o">用户协议</text>
					和
					<text @click="goPrivacy()" class="color-o">隐私政策</text>
				</view>
				<view class="login-other flc">
					<view class="fz12 item" @click="selectLoginType">验证码登录</view>
					<view class="fz12 item" @click="toReg">注册</view>
				</view>
			</view>
		</view>
		<!--  微信浏览器里 -->
		<!-- #ifdef H5 -->
		<template v-if="weixinBrowser">
			<view class="fz12 g5">第三方账号登录：</view>
			<view class="flc third-block">
				<view class="third-item" v-for="(item, key, index) in thirdPartyLogins" :key="index" @click="handleThirdLogin(item)">
					<image class="third-item-img" :src="getThirdLoginImg(key)" mode="aspectFill"></image>
				</view>
			</view>
		</template>
		<!-- #endif -->
		<!-- #ifdef APP-PLUS || APP-PLUS-NVUE -->
		<view class="fz12 g5">第三方账号登录：</view>
		<view class="flc third-block" v-if="thirdPartyLogins.length > 0">
			<view class="third-item" v-for="(item, key, index) in thirdPartyLogins" :key="key" @click="handleThirdLoginApp(item)">
				<image class="third-item-img" src="/static/image/ic-wechat.png" mode="aspectFill" v-if="item == 'weixin'"></image>
			</view>
		</view>
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		baseUrl
	} from '@/config/config.js';
	import {
		goBack,
		jumpBackPage
	} from '@/config/mixins.js';
	export default {
		mixins: [goBack, jumpBackPage],
		data() {
			return {
				maxMobile: 11,
				mobile: '', // 手机号
				pwd: '', // 密码
				isCaptcha: false, // 是否需要验证码
				captcha: '', // 输入的验证码
				captchaUrl: '', // 验证码图片地址
				btnb: 'btn btn-square btn-c btn-all', // 按钮bg
				weixinBrowser: false, // 是否是微信浏览器
				thirdPartyLogins: [] // 第三方登录列表
			};
		},
		onLoad(options) {
			if (options.invitecode) {
				this.$db.set('invitecode', options.invitecode);
			}
			// 判断浏览器环境
			this.weixinBrowser = this.$common.isWeiXinBrowser();

			if (this.weixinBrowser) {
				this.getAuths();
			}
			// #ifdef APP-PLUS || APP-PLUS-NVUE
			this.getAppAuths();
			// #endif
		},
		onShow() {
			if (this.$db.get('userToken')) {
				uni.switchTab({
					url: '/pages/index/index'
				});
			}
		},
		computed: {
			// 动态更改登录按钮bg
			loginButtonClass() {
				return this.mobile && this.mobile.length > 0 && this.pwd ? this.btnb + ' btn-b' : this.btnb;
			},
			logoImage() {
				return this.$store.state.config.shop_logo;
			},
			getThirdLoginImg(key) {
				return key => {
					if (key == 'Wxofficial') {
						return '/static/image/ic-wechat.png';
					} else if (key == 'weixin') {
						return '/static/image/ic-wechat.png';
					}
				};
			}
		},
		methods: {
			// 验证手机号
			rightMobile() {
				let res = {};
				if (!this.mobile) {
					res.status = false;
					res.msg = '请输入手机号';
				} else if (!/^1[3456789]{1}\d{9}$/gi.test(this.mobile)) {
					res.status = false;
					res.msg = '手机号格式不正确';
				} else if (!this.pwd) {
					res.status = false;
					res.msg = '请输入密码';
				} else {
					res.status = true;
				}
				return res;
			},
			// 登录处理
			loginHandler() {
				if (this.mobile && this.mobile.length > 0 && this.pwd) {
					this.toLogin();
					// if (!this.rightMobile().status) {
					// 	this.$common.errorToShow(this.rightMobile().msg);
					// } else {
					// 	this.toLogin();
					// }
				}
			},
			// 获取验证码图片地址
			getCaptchaUrl() {
				this.captchaUrl = this.$config.apiBaseUrl + 'captcha.html';
			},
			getCodeImg() {
				let num = Math.ceil(Math.random() * 1000);
				this.captchaUrl = this.$config.apiBaseUrl + 'captcha.html?' + num;
			},
			// 去注册
			toReg() {
				this.$common.redirectTo('/pages/login/register/index');
			},
			// 去登录
			toLogin() {
				let data = {
					mobile: this.mobile,
					password: this.pwd
				};

				if (this.isCaptcha) {
					data.captcha = this.captcha;
				}

				// 获取邀请码
				let invicode = this.$db.get('invitecode');
				if (invicode) {
					data.invitecode = invicode;
				}

				this.$api.login(data, res => {
					if (res.status) {
						this.$db.set('userToken', res.data);
						this.redirectHandler();
					} else {
						this.$common.errorToShow(res.msg, () => {
							// 绑定手机号
							if (res.data === '11027') {
								this.$db.set('userToken', res.token);
								this.$common.navigateTo("/pages/login/mobile/index")
							}
							// 需要输入验证码 或者 验证码错误刷新
							if (res.data === 10013 || res.data === 10012) {
								this.isCaptcha = true;
							}

							// 登录需要验证码
							if (this.isCaptcha) {
								this.getCaptchaUrl();
							}

						});
					}
				});
			},
			// 重定向跳转 或者返回上一个页面
			redirectHandler() {
				this.$db.del('invitecode');
				// this.handleBack();
				// let pages = getCurrentPages()
				// let page = pages[pages.length - 1]
				// console.log(page);
				// if (page.route.indexOf("pages/login/login/index1") !== -1) {
				// 	uni.navigateBack({
				// 		delta: 2
				// 	});
				// } else {
				// 	uni.navigateBack({
				// 		delta: 1
				// 	});
				// }
				uni.navigateBack({
					delta: 1
				});
			},
			// 登录方式切换
			selectLoginType() {
				this.$common.redirectTo('/pages/login/login/index');
			},
			// 获取第三方登录列表
			getAuths() {
				let data = {
					url: baseUrl + 'wap/pages/author'
				};
				this.$api.getTrustLogin(data, res => {
					if (res.status) {
						this.thirdPartyLogins = res.data;
					}
				});
			},
			// 第三方登录授权
			handleThirdLogin(url) {
				this.$common.redirectTo('');
				let redirect = this.$store.state.redirectPage;
				// console.log("第三方登录vuex"+redirect);
				this.$db.set('redirectPage', redirect);
				// console.log("第三方登录本地存储"+this.$db.get('redirectPage'));
				window.location.href = url;
			},
			//获取APP信任登录
			getAppAuths() {
				let _this = this;
				_this.thirdPartyLogins = [];
				uni.getProvider({
					service: 'oauth',
					success: function(res) {
						if (res.errMsg == 'getProvider:ok') {
							_this.thirdPartyLogins = res.provider;
						}
					}
				});
			},
			//app第三方登录
			handleThirdLoginApp(type) {
				uni.showLoading({
					title: '加载中'
				});
				let _this = this;
				uni.login({
					provider: type,
					success: function(loginRes) {
						// 获取用户信息
						uni.getUserInfo({
							provider: type,
							success: function(infoRes) {
								if (infoRes.errMsg == 'getUserInfo:ok') {
									var data = {
										user: infoRes.userInfo,
										type: type
									};
									var invitecode = _this.$db.get('invitecode');
									if (invitecode) {
										data.invitecode = invitecode;
									}
									_this.$api.appTrustLogin(data, res => {
										uni.hideLoading();
										if (res.status) {
											//判断是否返回了token，如果没有，就说明没有绑定账号，跳转到绑定页面
											if (typeof res.data.token == 'undefined') {
												uni.redirectTo({
													url: '/pages/login/login/index?user_wx_id=' + res.data.user_wx_id
												});
											} else {
												//登陆成功，设置token，并返回上一页
												_this.$db.set('userToken', res.data.token);
												uni.navigateBack({
													delta: 1
												});
												return false;
											}
										} else {
											_this.$common.errorToShow('登录失败，请重试');
										}
									});
								} else {
									uni.hideLoading();
									_this.$common.errorToShow('登录失败，请重试');
								}
							}
						});
					}
				});
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
		font-size: 28upx;
		color: #333;
		margin-bottom: 30upx;
		display: flex;
		align-items: center;
	}

	.login-item-input {
		display: inline-block;
		flex: 1;
		box-sizing: border-box;
	}

	.codeimg {
		width: 210rpx;
	}

	.login-item .btn {
		display: inline-block;
		font-size: 28upx;
		border: none;
		width: 40%;
		padding: 0;
		line-height: 1.7;
		float: right;
	}

	.login-b .btn {
		color: #999;
	}

	.btn-b {
		color: #fff !important;
	}

	.registered-item {
		overflow: hidden;
		width: 100%;
	}

	.registered {
		float: right;
	}

	.registered-item .btn-square {
		color: #333;
	}

	.third-block {
		justify-content: center;
		padding-top: 40upx;

		.third-item {
			width: 80upx;
			height: 80upx;
			background: $g2;
			border-radius: 50%;
			padding: 16upx;

			&-img {
				display: block;
				width: 100%;
				height: 100%;
			}
		}
	}

	.login-other {
		margin-bottom: 40upx;

		.item {
			padding: 20upx 0;
		}
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
