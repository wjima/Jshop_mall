<template>
	<view class="content">
		<view class="content-top">
			<view class='cell-group'>
				<view class='cell-item cell-item-mid' v-if="oldPassword">
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>旧密码</view>
					</view>
					<view class='cell-item-bd' style="width: 75%;">
						<input class='cell-bd-input' placeholder='输入旧密码' v-model="pwd" style="width: 100%;"></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>新密码</view>
					</view>
					<view class='cell-item-bd' style="width: 75%;">
						<input class='cell-bd-input' placeholder='输入6-16位新密码' v-model="newPwd" style="width: 100%;"></input>
					</view>
				</view>
				<view class='cell-item cell-item-mid'>
					<view class='cell-item-hd'>
						<view class='cell-hd-title'>确认密码</view>
					</view>
					<view class='cell-item-bd' style="width: 75%;">
						<input class='cell-bd-input' placeholder='输入6-16位新密码' v-model="rePwd" style="width: 100%;"></input>
					</view>
				</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-square btn-b" hover-class="btn-hover2" @click="submitHandler()" :disabled='submitStatus'
			 :loading='submitStatus'>保存</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				pwd: '',
				newPwd: '',
				rePwd: '',
				sex: 0,
				submitStatus: false,
				oldPassword: true
			}
		},
		computed: {},
		methods: {
			// 保存资料
			submitHandler() {
				this.submitStatus = true;
				if (this.oldPassword == true) {
					if (this.pwd === '') {
						this.$common.errorToShow('请输入旧密码')
						this.submitStatus = false;
					} else if (this.newPwd === '') {
						this.$common.errorToShow('请输入新密码')
						this.submitStatus = false;
					} else if (this.rePwd === '') {
						this.$common.errorToShow('请输入重复密码')
						this.submitStatus = false;
					} else {
						this.$api.editPwd({
							pwd: this.pwd,
							newpwd: this.newPwd,
							repwd: this.rePwd
						}, res => {
							if (res.status){
								// this.submitStatus = false;
								this.$common.successToShow(res.msg)
								setTimeout(function(){
									uni.navigateBack({
										delta: 1
									});
								},1000)
							} else {
								// this.submitStatus = false;
								this.$common.errorToShow(res.msg)
							}
						},res => {
							this.submitStatus = false;
						})
					}
				} else {
					if (this.newPwd === '') {
						this.$common.errorToShow('请输入新密码')
						this.submitStatus = false;
					} else if (this.rePwd === '') {
						this.$common.errorToShow('请输入重复密码')
						this.submitStatus = false;
					} else {
						this.$api.editPwd({
							newpwd: this.newPwd,
							repwd: this.rePwd,
						}, res => {
							if (res.status){
								// this.submitStatus = false;
								this.$common.successToShow(res.msg)
								setTimeout(function(){
									uni.navigateBack({
										delta: 1
									});
								},1000)
							} else {
								// this.submitStatus = false;
								this.$common.errorToShow(res.msg)
							}
						},res => {
							this.submitStatus = false;
						})
					}
				}
			}
		},
		onLoad: function() {
			var _this = this;
			_this.$api.userInfo({}, function(res) {
				if (res.status) {
					var the_sex = res.data.sex - 1;
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
