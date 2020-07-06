<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group'>
				<view class='cell-item cell-item-mid' v-if="mobile">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>手机号</view>
					</view>
					<view class='cell-item-bd'>
						{{mobile||''}}
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>验证码</view>
					</view>
					<view class='cell-item-bd'>
						<input class="cell-bd-input"  type="text" v-model="code" placeholder="请输入验证码" />
						<view class="btn" @click="sendCode" v-if="verification">发送验证码</view>
						<view class="btn" v-if="!verification">{{ timer }} 秒后重新获取</view>
						
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>新密码</view>
					</view>
					<view class='cell-item-bd' style="width: 75%;">
						<input class='cell-bd-input' placeholder='输入6-16位新密码' v-model="newPwd" style="width: 100%"></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>确认密码</view>
					</view>
					<view class='cell-item-bd' style="width: 75%;">
						<input class='cell-bd-input' placeholder='输入6-16位新密码' v-model="rePwd" style="width: 100%"></input>
					</view>
				</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-square btn-b" hover-class="btn-hover2" @click="submitHandler()" :disabled='submitStatus'
			 :loading='submitStatus'>修改</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				newPwd: '',
				rePwd: '',
				sex: 0,
				submitStatus: false,
				oldPassword: true,
				mobile:'',
				code:'',
				verification: true, // 通过v-show控制显示获取还是倒计时
				timer: 60, // 定义初始时间为60s
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
			}
		},
		methods: {
			// 保存资料
			submitHandler() {
				if(this.submitStatus){
					return false;
				}
				this.submitStatus = true;
				if (this.code === '') {
					this.$common.errorToShow('请输入验证码')
					this.submitStatus = false;
				} else if (this.newPwd === '') {
					this.$common.errorToShow('请输入新密码')
					this.submitStatus = false;
				}else if (this.rePwd === '') {
					this.$common.errorToShow('请输入确认密码')
					this.submitStatus = false;
				} else {
					let data = {
						mobile: this.mobile,
						code: this.code,
						newpwd: this.newPwd,
						repwd: this.rePwd
					};
					this.$api.userForgetpwd(data, res => {
						if (res.status){
							this.submitStatus = false;
							this.$common.successToShow(res.msg)
							setTimeout(function(){
								uni.navigateBack({
									delta: 1
								});
							},1000)
						} else {
							this.$common.errorToShow(res.msg)
								this.submitStatus = false;
						}
					},res => {
						this.submitStatus = false;
					})
				}
			},
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
		},
		onLoad: function() {
			let _this = this;
			_this.timer = parseInt(_this.$db.get('timer'));
			if (_this.timer != null && _this.timer > 0) {
				_this.countDown();
				_this.verification = false;
			}
			_this.$api.userInfo({}, function(res) {
				if (res.status) {
					let the_sex = res.data.sex - 1;
					if (res.data.birthday == null) {
						res.data.birthday = '请选择';
					}
					_this.nickname = res.data.nickname;
					_this.mobile = res.data.mobile;
					_this.sex = the_sex;
					_this.index = the_sex;
					_this.birthday = res.data.birthday;
					_this.avatar = res.data.avatar;
					_this.oldPassword = res.data.password;
					if (_this.birthday != '请选择') {
						_this.date = _this.birthday;
					}
				} else {
					//报错了
					_this.$common.errorToShow(res.msg);
				}
			});
		}
	}
</script>

<style>
.user-head {
	height: 100upx;
}

.user-head-img {
	height: 90upx;
	width: 90upx;
	border-radius: 50%;
}

.cell-hd-title {
	color: #333;
}

.cell-item-bd {
	color: #666;
	font-size: 26upx;
}
.cell-item-hd{
	width: 160rpx;
}
</style>
