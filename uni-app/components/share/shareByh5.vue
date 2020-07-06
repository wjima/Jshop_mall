<template>
	<view style="width: 100%;height: 300upx;background: #FFFFFF;position: absolute;left:0;bottom: 0;">
		<view class="share-pop">
			<view class="share-item" @click="copyAction()">
				<image src="/static/image/share-f.png" mode=""></image>
				<view class="">复制链接</view>
			</view>
			<view class="share-item" @click="createPoster()" v-show="shareType!=10">
				<image src="/static/image/poster.png" mode=""></image>
				<view class="">生成海报</view>
			</view>
		</view>
		<view class="button-bottom">
			<button class="btn btn-w btn-square" @click="close()">关闭</button>
		</view>
	</view>
</template>

<script>
	import {
		h5Url
	} from '@/config/config.js'
	export default {
		props: {
			// 商品id
			goodsId: {
				type: Number,
				default: 0
			},
			// 分享的图片
			shareImg: {
				type: String,
				default: ''
			},
			// 分享标题
			shareTitle: {
				type: String,
				default: ''
			},
			// 分享内容
			shareContent: {
				type: String,
				default: ''
			},
			// 分享链接
			shareHref: {
				type: String,
				default: ''
			},
			//分享类型
			shareType: {
				type: Number,
				default: 1
			},
			//拼团id
			groupId: {
				type: Number,
				default: 0
			},
			//拼团的团队id
			teamId: {
				type: Number,
				default: 0
			},
			//砍价参与类型
			bargainType: {
				type: Number,
				default: 1
			},
			//砍价活动id
			record_id: {
				default: 0
			},
			ifwx: {
				type: Boolean
			}
		},
		mounted() {
			//todo::H5端分享两种 (微信浏览器内引导用户去分享, 其他浏览器)
			this.copyUrl()
		},
		data() {
			return {
				copyUrlink: ''
			}
		},
		methods: {
			// 关闭弹出层
			close() {
				this.$emit('close')
			},
			// 生成海报
			createPoster() {
				let data = {
					page: this.shareType,
					url: h5Url + 'pages/share/jump',
					type: 3,
					client: 1
				}

				if (this.shareType == 2) {
					//商品详情页
					data.params = {
						goods_id: this.goodsId
					};
				} else if (this.shareType == 3) {
					//拼团详情页
					data.params = {
						goods_id: this.goodsId
					};
				} else if (this.shareType == 6) {
					//拼团参团页
					data.params = {
						goods_id: this.goodsId,
						group_id: this.groupId,
						team_id: this.teamId
					};
				} else if (this.shareType == 9) {
					//团购秒杀
					data.params = {
						goods_id: this.goodsId,
						group_id: this.groupId
					}
				} else if (this.shareType == 10) {
					//砍价
					data.params = {
						id: this.goodsId,
						type: this.bargainType,
						record_id: this.record_id
					}
				}
				let userToken = this.$db.get('userToken')
				if (userToken) {
					data.token = userToken
				}
				this.$api.share(data, res => {
					if (res.status) {
						// console.log(res);
						this.close()
						this.$common.navigateTo('/pages/share?poster=' + encodeURIComponent(res.data))
					} else {
						this.$common.errorToShow(res.msg)
					}
				});
			},
			copyUrl() {

				let data = {
					page: this.shareType,
					url: h5Url + 'pages/share/jump',
					type: 1,
					client: 1
				}

				if (this.shareType == 2) {
					//商品详情页
					data.params = {
						goods_id: this.goodsId
					};
				} else if (this.shareType == 3) {
					//拼团详情页
					data.params = {
						goods_id: this.goodsId
					};
				} else if (this.shareType == 6) {
					//拼团参团页
					data.params = {
						goods_id: this.goodsId,
						group_id: this.groupId,
						team_id: this.teamId
					};
				} else if (this.shareType == 9) {
					//团购秒杀
					data.params = {
						goods_id: this.goodsId,
						group_id: this.groupId
					}
				} else if (this.shareType == 10) {
					//砍价
					data.params = {
						id: this.goodsId,
						type: this.bargainType,
						record_id: this.record_id
					}
				}
				let userToken = this.$db.get('userToken')
				if (userToken) {
					data.token = userToken
				}
				let _this = this;
				_this.$api.share(data, res => {
					// this.$emit('copydata', data)
					if (res.status) {
						_this.copyUrlink = res.data
						// let data1 = {
						// 	url: res.data
						// }
						// this.$api.getShareInfo(data1, res => {
						// 	if (res.status) {
						// 		_this.$wx.config({
						// 			debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。  
						// 			appId: res.data.appId, // 必填，公众号的唯一标识  
						// 			timestamp: res.data.timestamp, // 必填，生成签名的时间戳  
						// 			nonceStr: res.data.nonceStr, // 必填，生成签名的随机串  
						// 			signature: res.data.signature, // 必填，签名，见附录1  
						// 			jsApiList: ["updateAppMessageShareData", "updateTimelineShareData"]
						// 		});
						// 		_this.$wx.ready(function() {
						// 			let shareInfo = {
						// 				title: _this.product.name,
						// 				desc: _this.goodsInfo.brief,
						// 				imgUrl: _this.goodsInfo.album[0]
						// 			}
						// 			// 分享朋友
						// 			_this.$wx.updateAppMessageShareData(shareInfo);
						// 			// 分享朋友圈
						// 			_this.$wx.updateTimelineShareData(shareInfo);
						// 		})
						// 	}
						// });

					} else {
						_this.$common.errorToShow('获取分享URL失败');
					}
				});
			},
			copyAction() {
				let _this = this;
				uni.setClipboardData({
					data: this.copyUrlink,
					success: function(succ) {
						_this.$common.successToShow('复制成功');
					},
					fail: function(err) {
						_this.$common.errorToShow('复制分享URL失败');
					}
				})

			},
			// 分享操作
			share() {
				//todo:: h5分享 判断是否是微信浏览器 引导用户完成分享操作 其他浏览器的分享 
			}
		}
	}
</script>

<style>
	.share-pop {
		height: 300upx;
		width: 100%;
		display: flex;
	}

	.share-item {
		flex: 1;
		text-align: center;
		font-size: 26upx;
		color: #333;
		padding: 20upx 0;
	}

	.share-item image {
		width: 80upx;
		height: 80upx;
		margin: 20upx;
	}

	.share-item .btn {
		line-height: 1;
		display: block;
		font-size: 26upx;
		background-color: #fff;
	}
</style>
