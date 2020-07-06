<template>
	<view class="content" style="padding-top: 0upx;">

		<!-- #ifdef MP-WEIXIN -->
		<!-- <view class="subscription-notice" v-if="suTip">
			<view>
				<image class="icon" src="/static/image/subscription-tip.png" mode=""></image>
				<text>订阅消息，及时获取订单最新动态</text>
			</view>
			<view>
				<text class="subscription-notice-btn red-price" @click="toSubscription()">消息订阅</text>
				<text class="subscription-notice-btn-close" @click="toClose()">×</text>
			</view>
		</view> -->
		<!-- #endif -->

		<jshop :jdata="pageData"></jshop>
		<jihaiCopyright v-if="copy"></jihaiCopyright>
		<!-- #ifdef H5 || APP-PLUS-NVUE || APP-PLUS -->
		<view class="service" @click="showChat()">
			<image class="icon" src="/static/image/seller-content.png" mode=""></image>
		</view>
		<!-- #endif -->
		<!-- #ifdef MP-WEIXIN -->
		<button class="service" hover-class="none" open-type="contact" bindcontact="showChat" :session-from="kefupara">
			<image class="icon" src="/static/image/seller-content.png" mode=""></image>
		</button>
		<!-- #endif -->
		<!-- #ifdef MP-ALIPAY -->
		<contact-button class="service icon" icon="/static/image/seller-content.png" size="80rpx*80rpx" tnt-inst-id="WKPKUZXG"
		 scene="SCE00040186" hover-class="none" />
		<!-- #endif -->
		<!-- #ifdef MP-TOUTIAO -->
		<!-- 头条客服 -->
		<!-- #endif -->
		<red-bag v-if="redBagShow" @click="handleGet"></red-bag>
	</view>
</template>
<script>
	import jshop from '@/components/jshop/jshop.vue';
	import jihaiCopyright from '@/components/jihai-copyright/jihaiCopyright.vue';
	import uniCountdown from '@/components/uni-countdown/uni-countdown.vue';
	import redBag from '@/components/red-bag/index';
	import {
		goods
	} from '@/config/mixins.js';
	import {
		goBack
	} from '@/config/mixins.js';
	export default {
		mixins: [goods],
		components: {
			jihaiCopyright,
			jshop,
			uniCountdown,
			redBag
		},
		data() {
			return {
				imageUrl: '/static/image/share_image.png', //店铺分享图片
				pageData: [],
				pageCode: 'mobile_home', //页面布局编码
				pintuan: [], //拼团列表,
				redBagShow: false, //红包
				config: '', //配置信息
				userInfo: {}, // 用户信息
				kefupara: '', //客服传递资料
				copy: false,
				suTipStatus: false,
				shareUrl: '/pages/share/jump'
			};
		},
		updated() {
			this.copy = true;
		},
		computed: {
			appTitle() {
				return this.$store.state.config.shop_name;
			},
			// 获取店铺联系人手机号
			shopMobile() {
				return this.$store.state.config.shop_mobile || 0;
			},
			suTip() {
				return this.suTipStatus;
			}
		},
		onLoad(e) {
			this.initData();
			if (this.$store.state.config.shop_name) {
				uni.setNavigationBarTitle({
					title: this.$store.state.config.shop_name || ''
				});
			}
			// 分享朋友和朋友圈
			// #ifdef H5
			if (this.$common.isWeiXinBrowser()) {
				this.shareAll()
			}
			// #endif
		},
		onShow() {
			// #ifdef MP-WEIXIN
			this.userIsSubscription();
			// #endif
		},
		methods: {
			//领取红包
			handleGet() {},
			destroyed() {
				window.removeEventListener('scroll', this.handleScroll);
			},
			goSearch() {
				uni.navigateTo({
					url: './search'
				});
			},
			// 首页初始化获取数据
			initData() {
				//获取首页配置
				this.$api.getPageConfig({
						code: this.pageCode
					},
					res => {
						if (res.status == true) {
							this.pageData = res.data.items;
							//隐藏loading
							setTimeout(() => {
								this.showLoad = false;
							}, 600);
						}
					}
				);

				//判断是开启分销还是原始推广
				this.$api.shopConfig(res => {
					this.config = res;
					// console.log(this.config);
				});

				var _this = this;
				if (this.$db.get('userToken')) {
					this.$api.userInfo({}, res => {
						if (res.status) {
							_this.userInfo = res.data;
							// #ifdef MP-WEIXIN
							//微信小程序打开客服时，传递用户信息
							var kefupara = {};
							kefupara.nickName = res.data.nickname;
							kefupara.tel = res.data.mobile;
							_this.kefupara = JSON.stringify(kefupara);
							// #endif
						}
					});
				}

				// #ifdef MP-WEIXIN
				this.userIsSubscription();
				// #endif

				this.getShareUrl();
			},
			//在线客服,只有手机号的，请自己替换为手机号
			showChat() {
				// #ifdef H5
				let _this = this;
				window._AIHECONG('ini', {
					entId: this.config.ent_id,
					button: false,
					appearance: {
						panelMobile: {
							tone: '#FF7159',
							sideMargin: 30,
							ratio: 'part',
							headHeight: 50
						}
					}
				});
				//传递客户信息
				window._AIHECONG('customer', {
					head: _this.userInfo.avatar,
					名称: _this.userInfo.nickname,
					手机: _this.userInfo.mobile
				});
				window._AIHECONG('showChat');
				// #endif

				// 客服页面
				// #ifdef APP-PLUS || APP-PLUS-NVUE
				this.$common.navigateTo('/pages/member/customer_service/index');
				// #endif

				// 头条系客服
				// #ifdef MP-TOUTIAO
				if (this.shopMobile != 0) {
					let _this = this;
					tt.makePhoneCall({
						phoneNumber: this.shopMobile.toString(),
						success(res) {},
						fail(res) {}
					});
				} else {
					_this.$common.errorToShow('暂无设置客服电话');
				}
				// #endif
			},
			//前往订阅
			toSubscription() {
				this.$common.navigateTo('/pages/member/setting/subscription/index');
			},
			//查询用户订阅
			userIsSubscription() {
				let userToken = this.$db.get('userToken');
				if (userToken && userToken != '') {
					this.$api.subscriptionIsTip(res => {
						if (res.status) {
							if (res.data) {
								this.suTipStatus = true;
							} else {
								this.suTipStatus = false;
							}
						} else {
							this.suTipStatus = true;
						}
					});
				} else {
					this.suTipStatus = true;
				}
			},
			//关闭订阅
			toClose() {
				let userToken = this.$db.get('userToken');
				if (userToken && userToken != '') {
					this.$api.subscriptionCloseTip(res => {
						this.suTipStatus = false;
					});
				} else {
					this.suTipStatus = false;
				}
			},
			//获取分享URL
			getShareUrl() {
				let data = {
					client: 2,
					url: "/pages/share/jump",
					type: 1,
					page: 1,
				};
				let userToken = this.$db.get('userToken');
				if (userToken && userToken != '') {
					data['token'] = userToken;
				}
				this.$api.share(data, res => {
					this.shareUrl = res.data
				});
			},
			// 分享到朋友或朋友圈
			shareAll() {
				// 微信浏览器里面
				// console.log(window.location.href);
				let data = {
					url: window.location.href
				}
				let _this = this;
				this.$api.getShareInfo(data, res => {
					if (res.status) {
						_this.$wx.config({
							debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。  
							appId: res.data.appId, // 必填，公众号的唯一标识  
							timestamp: res.data.timestamp, // 必填，生成签名的时间戳  
							nonceStr: res.data.nonceStr, // 必填，生成签名的随机串  
							signature: res.data.signature, // 必填，签名，见附录1  
							jsApiList: ["updateAppMessageShareData", "updateTimelineShareData"]
						});
						_this.$wx.ready(function() {
							let shareInfo = {
								title: "首页",
								desc: "首页",
								imgUrl: _this.config.shop_default_image
							}
							// 分享朋友
							_this.$wx.updateAppMessageShareData(shareInfo);
							// 分享朋友圈
							_this.$wx.updateTimelineShareData(shareInfo);
						})
					}
				});

			}
		},
		onPullDownRefresh() {
			this.initData();
			uni.stopPullDownRefresh();
		},
		//分享
		onShareAppMessage() {
			return {
				title: this.$store.state.config.share_title,
				// #ifdef MP-ALIPAY
				desc: this.$store.state.config.share_desc,
				// #endif
				imageUrl: this.$store.state.config.share_image,
				path: this.shareUrl
			};
		},
		// #ifdef MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE
		onPageScroll() {
			var _this = this;
			const query = uni.createSelectorQuery();
			query
				.select('.content >>> .search')
				.boundingClientRect(function(res) {
					if (res) {
						if (res.top < 0) {
							_this.$store.commit('searchFixed', true);
						} else {
							_this.$store.commit('searchFixed', false);
						}
					}
				})
				.exec();
		}
		//#endif
	};
</script>

<style>
	.cell-item {
		border: none;
	}

	.cell-ft-text {
		font-size: 22upx;
		color: #999;
	}

	.service {
		width: 80rpx;
		height: 80rpx;
		background-color: #fff;
		border-radius: 50%;
		position: fixed;
		right: 30rpx;
		bottom: 120rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		box-shadow: 0 0 10rpx #ccc;
		padding: 0;
		z-index: 996;
	}

	.service .icon {
		width: 60rpx;
		height: 60rpx;
	}

	.subscription-notice {
		background-color: #ffffff;
		box-shadow: 0 2rpx 18rpx #eeeeee;
		position: relative;
		z-index: 99;
		height: 66rpx;
		line-height: 66rpx;
		padding: 0 20rpx;
		font-size: 28rpx;
		color: #333333;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.subscription-notice>view {
		display: flex;
		align-items: center;
	}

	.subscription-notice .icon {
		width: 32rpx;
		height: 32rpx;
		margin-right: 10rpx;
	}

	.subscription-notice-btn {
		color: #4285f4;
	}

	.subscription-notice-btn-close {
		color: #888888;
		padding-left: 10px;
		font-size: 30rpx;
	}
</style>
