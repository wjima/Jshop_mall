<template>
	<view></view>
</template>
<script>
	export default {
		data() {
			return {};
		},
		onLoad(e) {
			let url = this.$common.shareParameterEncode(e.scene);
			let arr1 = url.split('&');
			
			let type = '',
				invite = '',
				page_code = '',
				id = '',
				id_type = '',
				group_id = '',
				team_id = '';

			for (var i = 0; i < arr1.length; i++) {
				let key = arr1[i].split('=')[0]
				if (key == 'type') {
					type = arr1[i].split('=')[1]
				}
				if (key == 'invite') {
					invite = arr1[i].split('=')[1]
				}
				if (key == 'page_code') {
					page_code = arr1[i].split('=')[1]
				}
				if (key == 'id') {
					id = arr1[i].split('=')[1]
				}
				if (key == 'id_type') {
					id_type = arr1[i].split('=')[1]
				}
				if (key == 'group_id') {
					group_id = arr1[i].split('=')[1]
				}
				if (key == 'team_id') {//拼团参团id
					team_id = arr1[i].split('=')[1]
				}
			}
			
			this.saveInviteCode(invite); //存储邀请码
			switch (type) {
				case '1': //首页
					this.gotoIndex();
					break;
				case '2': //商品详情页面
					this.gotoGoods(id);
					break;
				case '3': //首页
					this.gotoIndex();
					break;
				case '4': //文章页面
					this.gotoArticle(id, id_type);
					break;
				case '5': //拼团页面
					this.gotoPinTuan(id, team_id);
					break;
				case '6': //团购页面
					this.gotoGroup(id, group_id);
					break;
				case '7': //参团页面
					// todo:: 功能暂无后续开发
					// this.gotoInvitationGroup(id, group_id, team_id);
					break;
				case '8': //自定义页面
					this.gotoCustom(page_code);
					break;
				case '9': //店铺邀请
					this.gotoStore(id);
					break;
				case '10': //智能表单
					this.gotoForm(id);
					break;
				default:
					this.gotoIndex();
					break;
			}
		},
		methods: {
			//存储邀请码
			saveInviteCode(invite) {
				if (invite && invite != '') {
					this.$db.set('invitecode', invite);
				}
			},
			//跳转到首页
			gotoIndex() {
				uni.switchTab({
					url: '/pages/index/index'
				});
			},
			//跳转到商品
			gotoGoods(id) {
				if(id && id != ''){
					let url = '/pages/goods/index/index?id=' + id;
					this.$common.redirectTo(url);
				}else{
					this.gotoIndex();
				}
			},
			//跳转到文章
			gotoArticle(id, id_type) {
				if(id && id != ''){
					let url = '/pages/article/index?id=' + id + '&id_type=' + id_type;
					this.$common.redirectTo(url);
				}else{
					this.gotoIndex();
				}
			},
			//跳转到拼团
			gotoPinTuan(id, team_id) {
				if(id && id != ''){
					let url = '/pages/goods/index/pintuan?id=' + id + '&team_id=' + team_id;
					this.$common.redirectTo(url);
				}else{
					this.gotoIndex();
				}
			},
			//跳转到团购
			gotoGroup(id, group_id) {
				if(id && id != ''){
					let url = '/pages/goods/index/group?id=' + id + '&group_id=' + group_id;
					this.$common.redirectTo(url);
				}else{
					this.gotoIndex();
				}
			},
			//跳转到参团
			//todo:: 功能暂无后续开发
			// gotoInvitationGroup(id, group_id, team_id) {
			// 	if(id && id != '' && group_id && group_id != '' && team_id && team_id != ''){
			// 		let url = '/pages/member/order/invitation_group?id=' + id + '&group_id=' + group_id + '&team_id=' + team_id;
			// 		this.$common.redirectTo(url);
			// 	}else{
			// 		this.gotoIndex();
			// 	}
			// },
			//跳转到自定义页
			gotoCustom(page_code) {
				if(page_code && page_code != ''){
					let url = '/pages/index/custom?page_code=' + page_code;
					this.$common.redirectTo(url);
				}else{
					this.gotoIndex();
				}
			},
			gotoStore(id) {
				if(id && id != ''){
					let url = '/pages/member/distribution/my_store?store=' + id;
					this.$common.redirectTo(url);
				}else{
					this.gotoIndex();
				}
			},
			//跳转表单
			gotoForm(id){
				if(id && id != ''){
					let url = '/pages/form/detail/form?id=' + id;
					this.$common.redirectTo(url);
				}else{
					this.gotoIndex();
				}
			}
		}
	};
</script>