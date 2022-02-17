<template>
	<view><web-view :src="url" @message="eventMessage" @onPostMessage="eventPostMessage"></web-view></view>
</template>

<script>
export default {
	data() {
		return {
			url: ''
		};
	},
	onLoad(options) {
		let url = decodeURIComponent(options.src);
		let login = this.$common.getQueryString('login', url);
		let token = '';
		//检查登录状态
		if (login == 'true') {
			let userToken = this.$db.get('userToken');
			if (!userToken) {
				this.$common.jumpToLogin();
			} else {
				if (url.indexOf('?') > -1) {
					url = url + '&userToken=' + userToken;
				} else {
					url = url + '?userToken=' + userToken;
				}
			}
		}
		this.url = url;
	},
	methods: {
		eventMessage(e) {
			console.log('eventMessage', e);
		},
		eventPostMessage(e) {
			console.log('eventPostMessage', e.detail.data);
		}
	},
	onShow(){
		this.url = this.url;
	},
	onBackPress(options) {
	    console.log('from:' + options.from)
	}
};
</script>

<style></style>
