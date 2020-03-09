<template>
	<view class="content">
		<view class="reg-t"><image class="reg-logo" :src="logoImage" mode="aspectFill"></image></view>
		<view class="reg-m">
			<view class="reg-item">
				<input type="number" v-model="mobile" :maxlength="maxMobile" placeholder="请输入手机号码" focus placeholder-class="login-item-i-p fsz26" />
			</view>
			<view class="reg-item flc">
				<input class="reg-item-input" placeholder-class="login-item-i-p fsz26" type="text" v-model="code" placeholder="请输入验证码" />
				<view :class="sendCodeBtn" @click="sendCode" v-if="verification">发送验证码</view>
				<view class="btn btn-g" v-if="!verification">{{ timer }} 秒后重新获取</view>
			</view>
			<view class="reg-item">
				<input class="login-item-input" :password="true" placeholder-class="login-item-i-p fsz26" type="text" v-model="pwd" placeholder="请输入6-16位密码" />
			</view>
			<view class="reg-item">
				<input class="login-item-input" :password="true" placeholder-class="login-item-i-p fsz26" type="text" v-model="repwd" placeholder="请确认密码" />
			</view>
		</view>
		<view class="reg-b">
			<button :class="regButtonClass" @click="toReg()" hover-class="btn-hover">确认</button>
			<!-- <view class="color-9 fsz24 agreement">
				注册即代表你同意 <text @click="goAgreement()" class="color-o">用户协议</text> 和 <text @click="goPrivacy()" class="color-o">隐私政策</text>
			</view> -->
		</view>
		<!-- <view class="registered-item">
			<view class="btn btn-g btn-square registered" @click="toLogin">已有账号，立即登录</view>
		</view> -->
	</view>
</template>

<script>
import { goBack } from '@/config/mixins.js';
export default {
	mixins: [goBack],
	data() {
		return {
			maxMobile: 11,
			mobile: '', // 用户手机号
			code: '', // 短信验证码
			pwd: '', // 用户密码
			repwd: '', //确认密码
			verification: true, // 通过v-show控制显示获取还是倒计时
			timer: 60, // 定义初始时间为60s
			btnb: 'btn btn-c btn-square btn-all' //按钮背景
		};
	},
	onLoad(options) {
		let _this = this;
		_this.timer = parseInt(_this.$db.get('timer'));
		if (_this.timer != null && _this.timer > 0) {
			_this.countDown();
			_this.verification = false;
		}

		if (options.invitecode) {
			this.$db.set('invitecode', options.invitecode);
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
		// 动态更改登录按钮bg
		regButtonClass() {
			return this.mobile && this.mobile.length === 11 && this.pwd && this.code ? this.btnb + ' btn-b' : this.btnb;
		},
		// 动态修改发送验证码按钮
		sendCodeBtn() {
			let btn = 'btn btn-g';
			if (this.mobile.length === this.maxMobile && this.rightMobile.status) {
				return btn + ' btn-b';
			} else {
				return btn;
			}
		},
		logoImage() {
			return this.$store.state.config.shop_logo;
		}
	},
	onShow() {
		let _this = this;
		let userToken = _this.$db.get('userToken');
		if (userToken && userToken != '') {
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
					this.$api.sms({ mobile: this.mobile, code: 'veri' }, res => {
						if (res.status) {
							this.timer = 60;
							this.verification = false;
							this.$common.successToShow(res.msg);
							this.countDown(); // 执行验证码计时
							this.btnb = 'btn btn-square btn-all btn-b';
						} else {
							this.$common.errorToShow(res.msg);
						}
					});
				}, 1000);
			}
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
		toReg() {
			if (!this.rightMobile.status) {
				this.$common.errorToShow(this.rightMobile.msg);
			} else if (!this.code) {
				this.$common.errorToShow('请输入短信验证码');
			} else if (!this.pwd) {
				this.$common.errorToShow('请输入6-16位密码');
			} else if (!this.repwd) {
				this.$common.errorToShow('请确认密码');
			} else {
				let data = {
					mobile: this.mobile,
					code: this.code,
					newpwd: this.pwd,
					repwd: this.repwd
				};
				this.$api.userForgetpwd(data, res => {
					if (res.status) {
						this.$common.successToShow(res.msg, () => {
							this.$common.navigateTo('/pages/login/login/index1');
							//console.log(res.data)
						});
					} else {
						this.$common.errorToShow(res.msg);
					}
				});
			}
		},
		toLogin() {
			this.$common.navigateTo('/pages/login/login/index1');
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
.reg-t {
	text-align: center;
	padding: 50upx 0;
}
.reg-logo {
	width: 180upx;
	height: 180upx;
	border-radius: 20upx;
	background-color: #f8f8f8;
}
.reg-m {
	margin-bottom: 100upx;
}
.reg-item {
	border-bottom: 2upx solid #d0d0d0;
	overflow: hidden;
	padding: 10upx;
	color: #333;
	margin-bottom: 30upx;
	.btn {
		border: none;
		width: 40%;
		text-align: right;
		&.btn-b {
			background: none;
			color: #333 !important;
		}
	}
}
.reg-item-input {
	flex: 1;
}

.reg-b .btn {
	color: #999;
}

.registered-item {
	overflow: hidden;
	width: 100%;
}
.registered {
	float: right;
}
.btn-square {
	color: #333;
	height: 80upx;
	line-height: 80upx;
	padding: 0;
	font-size: $fz12;
}

.agreement {
	margin: 20rpx 0;
	text-align: center;
	.color-o {
		margin: 0 10rpx;
	}
}
</style>
