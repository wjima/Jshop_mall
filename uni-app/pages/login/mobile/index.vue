<template>
	<view class="content">
		<view class="login-t"><image class="login-logo" :src="logoImage" mode="aspectFill"></image></view>
		<view class="login-m">
			<view class="login-item">
				<input type="number" v-model="mobile" :maxlength="maxMobile" placeholder="请输入手机号码" focus placeholder-class="login-item-i-p fsz26" />
			</view>
			<view class="login-item flc">
				<input class="login-item-input" placeholder-class="login-item-i-p fsz26" type="number" v-model="code" placeholder="请输入验证码" />
				<view :class="sendCodeBtn" @click="sendCode" v-if="verification">发送验证码</view>
				<view class="btn btn-g" v-if="!verification">{{ timer }} 秒后重新获取</view>
			</view>
		</view>
		<view class="login-b">
			<button :class="regButtonClass" @click="toBind()" hover-class="btn-hover">绑定手机号</button>
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
			verification: true, // 通过v-show控制显示获取还是倒计时
			timer: 60, // 定义初始时间为60s
			btnb: 'btn btn-square btn-c btn-all', //按钮背景
		};
	},
	onLoad(option) {
		
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
					this.$api.sms({ mobile: this.mobile, code: 'bind' }, res => {
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
		// 绑定手机号
		toBind() {
			if (this.mobile == '') {
				this.$common.errorToShow('请输入手机号码');
				return false;
			}
			if (this.code == '') {
				this.$common.errorToShow('请输入验证码');
				return false;
			}
			let token=this.$db.get('userToken');
			let data = {
				mobile: this.mobile,
				code: this.code,
				token:token
			};

			this.$api.bindMobile(data, res => {
				if (res.status) {
					this.redirectHandler();
				} else {
					this.$common.errorToShow(res.msg);
				}
			});
		},
		// 重定向跳转 或者返回上一个页面
		redirectHandler() {
			this.$common.successToShow('登录成功!', () => {
				this.$db.set('timer', 0);
				// let redirect = this.$store.state.redirectPage;
				// console.log("绑定手机号页面vuex"+redirect);
				// let redirectPage = this.$db.get('redirectPage');
				// console.log("绑定手机号页面本地"+redirectPage);
				// if (redirectPage) {
				// 	this.$db.del('redirectPage');
				// 	this.$common.redirectTo(redirectPage);
				// } else {
				// 	uni.reLaunch({
				// 		url: '/pages/index/index'
				// 	});
				// }
				// this.handleBack();
				uni.navigateBack({
				    delta: 1
				});
			});
		},
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
