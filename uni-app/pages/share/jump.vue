<template>
	<view class="container">
		<view class="bg" v-if="hasHb || hasCoupon">
			<image :src="`${url}static/images/welfarepro/bgi.png`" mode="" class="img"></image>
			<!-- <image src="/static/image/welfarepro/bgi.png" mode="" class="img"></image> -->
		</view>
		<view class="red" v-if="hasHb">
			<view class="red_bgi">
				<image :src="`${url}static/images/welfarepro/center.png`" mode="" class="img"></image>
				<!-- <image src="/static/image/welfarepro/center.png" mode="" class="img"></image> -->
			</view>
			<view class="del" @click="goPage(1)">
				<image :src="`${url}static/images/welfarepro/del-jump.png`" mode="" class="del_icon"></image>
				<!-- <image src="/static/image/welfarepro/del-jump.png" mode="" class="del_icon"></image> -->
			</view>
			<view class="logo">
				<image :src='logoImg'></image>
			</view>
			<view class="word">
				<view class="title">
					Jshop商城
				</view>
				<view class="content">
					送您一个红包
				</view>
			</view>
			<view class="btn" @click="receive(1)">
				领取红包
			</view>
		</view>
		<view class="red" v-if="hasCoupon">
			<view class="red_bgi">
				<image :src="`${url}static/images/welfarepro/center.png`" mode="" class="img"></image>
				<!-- <image src="/static/image/welfarepro/center.png" mode="" class="img"></image> -->
			</view>
			<view class="del" @click="goPage(2)">
				<image :src="`${url}static/images/welfarepro/del-jump.png`" mode="" class="del_icon"></image>
				<!-- <image src="/static/image/welfarepro/del-jump.png" mode="" class="del_icon"></image> -->
			</view>
			<view class="logo">
				<image :src='logoImg'></image>
			</view>
			<view class="word">
				<view class="title">
					Jshop商城
				</view>
				<view class="content">
					送您一张优惠券
				</view>
			</view>
			<view class="btn" @click="receive(2)">
				领取优惠券
			</view>
		</view>
	</view>
</template>
<script>
	import {
		apiBaseUrl
	} from '@/config/config.js'
	export default {
		data() {
			return {
				hasHb: false,
				hasCoupon: false,
				data: {},
				welfarepro: ["hb", "coupon"],
				url: ''
			};
		},
		computed: {
			logoImg() {
				return this.$store.state.config.shop_logo;
			}
		},
		onLoad(e) {
			this.url=apiBaseUrl
			if (e.scene) {
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
				this.$api.deshare({
					code: data
				}, res => {
					if (res.status) {
						// console.log(res.data);
						this.saveInviteCode(res.data.userShareCode); //存储邀请码
						this.data = res.data;
						if (res.data.welfarepro && res.data.welfarepro.length != 0) {
							if (res.data.welfarepro.indexOf('hb') > -1) {
								this.hasHb = true
							} else if (res.data.welfarepro.indexOf('coupon') > -1) {
								this.hasCoupon = true
							}
						} else {
							this.goPage()
						}
						//           switch(res.data.page) {
						//               case '1': //首页
						//                   this.gotoIndex();
						//                   break;
						//               case '2': //商品
						//                   this.gotoGoods(res.data.params.goods_id);
						//                   break;
						//               case '3': //拼团
						//                   this.gotoPinTuan(res.data.params.goods_id);
						//                   break;
						//               case '4': //店铺邀请
						//                   this.gotoStore(res.data.params.store);
						//                   break;
						//               case '5': //文章页面
						//                   this.gotoArticle(res.data.params.article_id, res.data.params.article_type);
						//                   break;
						//               case '6': //参团页面
						//                   this.gotoInvitationGroup(res.data.params.goods_id, res.data.params.group_id, res.data.params.team_id)
						//                   break;
						//               case '7': //自定义页面
						//                   this.gotoCustom(res.data.params.page_code);
						//                   break;
						//               case '8': //智能表单
						//                   this.gotoForm(res.data.params.id)
						//                   break;
						//               case '9': //团购秒杀
						//                   this.gotoGroup(res.data.params.goods_id, res.data.params.group_id);
						//                   break;
						// case '10'://邀请砍价
						// 	this.gotoBargain(res.data.params.id, res.data.params.type, res.data.params.record_id);
						// 	break;
						//               default:
						//               	this.gotoIndex();
						//               	break;
						// }
					} else {
						this.$common.errorToShow('失败', () => {
							// uni.navigateBack({
							// 	delta: 1
							// });
							this.gotoIndex();
						});
					}
				});
			},

			// 领取红包或者优惠券
			receive(type) {
				let data = {
					userShareCode: this.data.userShareCode
				}
				if (type == 1) {
					this.$api.getShareHb(data, res => {
						if (res.status) {
							this.$common.successToShow(res.msg, () => {
								this.hasHb = false
								if (this.data.welfarepro.indexOf('coupon') > -1) {
									this.hasCoupon = true
								} else {
									this.goPage()
								}
							});
						} else {
							this.$common.errorToShow(res.msg, () => {
								this.hasHb = false
								if (this.data.welfarepro.indexOf('coupon') > -1) {
									this.hasCoupon = true
								} else {
									this.goPage()
								}
							})
						}
					})
				} else if (type == 2) {
					this.$api.getShareCoupon(data, res => {
						if (res.status) {
							this.$common.successToShow(res.msg, () => {
								this.hasCoupon = false
								this.goPage()
							});
						} else {
							this.$common.errorToShow(res.msg, () => {
								this.hasCoupon = false
								this.goPage()
							})
						}
					})
				}


			},
			//不领取的话跳转到分享的页面
			goPage(type) {
				if (type == 1) {
					if (this.data.welfarepro.indexOf('coupon') > -1) {
						this.hasCoupon = true
					}
				} else {
					console.log(this.data)
					switch (this.data.page) {
						case '1': //首页
							this.gotoIndex();
							break;
						case '2': //商品
							this.gotoGoods(this.data.params.goods_id);
							break;
						case '3': //拼团
							this.gotoPinTuan(this.data.params.goods_id);
							break;
						case '4': //店铺邀请
							this.gotoStore(this.data.params.store);
							break;
						case '5': //文章页面
							this.gotoArticle(this.data.params.article_id, this.data.params.article_type);
							break;
						case '6': //参团页面
							this.gotoInvitationGroup(this.data.params.goods_id, this.data.params.group_id, this.data.params.team_id)
							break;
						case '7': //自定义页面
							this.gotoCustom(this.data.params.page_code);
							break;
						case '8': //智能表单
							this.gotoForm(this.data.params.id)
							break;
						case '9': //团购秒杀
							this.gotoGroup(this.data.params.goods_id, this.data.params.group_id);
							break;
						case '10': //邀请砍价
							this.gotoBargain(this.data.params.id, this.data.params.type, this.data.params.record_id);
							break;
						default:
							this.gotoIndex();
							break;
					}
				}
			},
			//存储邀请码
			saveInviteCode(invite) {
				if (invite && invite != '') {
					this.$db.set('invitecode', invite);
				}
			},
			//跳转到首页
			gotoIndex() {
				uni.navigateTo({
					url: '/pages/index/index'
				});
			},
			//跳转到商品
			gotoGoods(id) {
				if (id && id != '') {
					let url = '/pages/goods/index/index?id=' + id;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			//跳转到文章
			gotoArticle(id, id_type) {
				if (id && id != '') {
					let url = '/pages/article/index?id=' + id + '&id_type=' + id_type;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			//跳转到拼团
			gotoPinTuan(id) {
				if (id && id != '') {
					let url = '/pages/goods/index/pintuan?id=' + id;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			//跳转到团购
			gotoGroup(id, group_id) {
				if (id && id != '') {
					let url = '/pages/goods/index/group?id=' + id + '&group_id=' + group_id;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			//跳转到参团
			gotoInvitationGroup(id, group_id, team_id) {
				if (id && id != '' && group_id && group_id != '' && team_id && team_id != '') {
					let url = '/pages/goods/index/pintuan?id=' + id + '&team_id=' + team_id;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			//跳转到自定义页
			gotoCustom(page_code) {
				if (page_code && page_code != '') {
					let url = '/pages/index/custom?page_code=' + page_code;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			gotoStore(id) {
				if (id && id != '') {
					let url = '/pages/member/distribution/my_store?store=' + id;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			//跳转表单
			gotoForm(id) {
				if (id && id != '') {
					let url = '/pages/form/detail/form?id=' + id;
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			},
			//跳转砍价页面
			gotoBargain(id, type, record_id) {
				if (id && id != '' && type && record_id && record_id != 0) {
					let url = '/pages/bargain/index?id=' + id + '&type=' + type + '&record_id=' + record_id;
					// console.log(url);
					this.$common.redirectTo(url);
				} else {
					this.gotoIndex();
				}
			}

		}
	};
</script>
<style lang="scss">
	page {
		width: 100%;
		height: 100%;

		.container {
			position: relative;
			width: 100%;
			height: 100%;

			.bg {
				width: 100%;
				height: 100%;

				// position: relative;
				image {
					width: 100%;
					height: 100%;
				}
			}

			.red {
				position: absolute;
				top: 45%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: 100%;
				height: 70%;

				.red_bgi {
					width: 100%;
					height: 100%;

					.img {
						width: 100%;
						height: 100%;
					}
				}

				.del {
					position: absolute;
					top: 144rpx;
					right: 82rpx;

					.del_icon {
						width: 60rpx;
						height: 60rpx;
					}
				}

				.logo {
					position: absolute;
					top: 25%;
					left: 50%;
					transform: translateX(-50%);
					width: 180rpx;
					height: 180rpx;
					border-radius: 50%;
					background-color: #fff;

					image {
						position: absolute;
						top: 50%;
						left: 50%;
						transform: translate(-50%, -50%);
						width: 80%;
						height: 80%;
						border-radius: 50%;
					}
				}

				.word {
					position: absolute;
					left: 50%;
					top: 47%;
					transform: translateX(-50%);
					color: #ffe3d6;

					.title {
						font-size: 38rpx;
						text-align: center;
					}

					.content {
						font-size: 50rpx;
						margin-top: 30rpx;
					}
				}

				.btn {
					position: absolute;
					top: 95%;
					left: 50%;
					transform: translateX(-50%);
					width: 65%;
					height: 100rpx;
					line-height: 100rpx;
					text-align: center;
					border-radius: 10rpx;
					font-size: 40rpx;
					color: #fff;
					background-color: #eb573e;
					letter-spacing: 5rpx;
				}
			}
		}
	}
</style>
