<template>
	<view></view>
</template>
<script>
	export default {
		data() {
			return {};
		},
		onLoad(e) {
            if(e.scene) {
                this.deshare(e.scene);
            } else {
                this.gotoIndex();
                // this.$common.errorToShow('失败', () => {
                // 	uni.navigateBack({
                // 		delta: 1
                // 	});
                // });
            }
		},
		methods: {
            deshare(data) {
                this.$api.deshare({code: data}, res => {
                    if (res.status) {
                        this.saveInviteCode(res.data.userShareCode); //存储邀请码
                        switch(res.data.page) {
                            case '1': //首页
                                this.gotoIndex();
                                break;
                            case '2': //商品
                                this.gotoGoods(res.data.params.goods_id);
                                break;
                            case '3': //拼团
                                this.gotoPinTuan(res.data.params.goods_id, res.data.params.team_id);
                                break;
                            case '4': //店铺邀请
                                this.gotoStore(res.data.params.store);
                                break;
                            case '5': //文章页面
                                this.gotoArticle(res.data.params.article_id, res.data.params.article_type);
                                break;
                            case '6': //参团页面
                                this.gotoInvitationGroup(res.data.params.goods_id, res.data.params.group_id, res.data.params.team_id)
                                break;
                            case '7': //自定义页面
                                this.gotoCustom(res.data.params.page_code);
                                break;
                            case '8': //智能表单
                                this.gotoForm(res.data.params.id)
                                break;
                            case '9': //团购秒杀
                                this.gotoGroup(res.data.params.goods_id, res.data.params.group_id);
                                break;
                            default:
                            	this.gotoIndex();
                            	break;
                        }
                    } else {
                        this.$common.errorToShow('失败', () => {
                        	uni.navigateBack({
                        		delta: 1
                        	});
                        });
                    }
                });
            },
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