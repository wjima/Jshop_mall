<template>
	<view class="content">
		<view class="content-c">
			<image class="load-img" src="/static/image/loading.gif" mode=""></image>
			<view class="load-text color-9">信息加载中.....</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			code: '',
			type: '',
			state: ''
		};
	},
	onLoad(options) {
		// 获取url上的参数
		this.code = this.getUrlParam('code');
		this.state = this.getUrlParam('state');
		this.type = options.type;
		var _this = this;
		setTimeout(function() {
			_this.userTrustLogin();
		}, 100);
	},
	methods: {
		// 获取url地址参数
		getUrlParam(paraName) {
			let url = window.location.toString();
			let arrObj = url.split('?');
			if (arrObj.length > 1) {
				let arrPara = arrObj[1].split('&');
				let arr;
				for (let i = 0; i < arrPara.length; i++) {
					arr = arrPara[i].split('=');
					if (arr != null && arr[0] == paraName) {
						if (arr[1].indexOf('#')) {
							let str;
							str = arr[1].split('#');
							return str[0];
						}
						return arr[1];
					}
				}
				return '';
			} else {
				return '';
			}
		},
		// 第三方登录
		userTrustLogin() {
			let data = {
				scope: 1,
				code: this.code,
				state: this.state,
				invitecode: this.$db.get('invitecode') || ''
			};
			this.$api.getOpenId(data, res => {
				if (res.status) {
					if (res.data.token) {
						this.$db.set('userToken', res.data.token);
						this.redirectHandler();
					} else if (res.data.user_wx_id) {
						// 第三方登录去绑定手机号
						// uni.redirectTo({
						// 	url: '/pages/login/login/index?user_wx_id=' + res.data.user_wx_id
						// });
						uni.redirectTo({
							url: '/pages/login/login/mobile?user_wx_id=' + res.data.user_wx_id
						});
					}
				} else {
					// this.$common.errorToShow(res.msg);
					this.$common.errorToShow(res.msg, () => {
						// 绑定手机号
						if (res.data === '11027') {
							this.$db.set('userToken', res.token);
							// this.$common.navigateTo("/pages/login/mobile/index")
							uni.redirectTo({
								url: '/pages/login/login/mobile'
							});
						}
					});
				}
			});
		},
		redirectHandler() {
			this.$db.del('invitecode');
			let redirectPage = this.$db.get('redirectPage');
			if (redirectPage) {
				this.$db.del('redirectPage');
				this.$common.redirectTo(redirectPage);
			} else {
				uni.reLaunch({
					url: '/pages/index/index'
				});
			}
		}
	}
};
</script>

<style>
.content {
	position: relative;
	height: 80vh;
}
.content-c {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	text-align: center;
}
.load-img {
	width: 100upx;
	height: 100upx;
}
.load-text {
	font-size: 26upx;
}
</style>
