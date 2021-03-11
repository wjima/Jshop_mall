<template>
	<view class="content">
		<view class="cont-top">
			<image src="/static/image/wx/pedometer_bgi.png" mode="" class="bgi"></image>
			<view class="bu-info">
				<view class="fsz32">
					今日步数
				</view>
				<view class="fsz32">
					<text class="num">{{detail.step || ""}}</text>步
				</view>
				<view class="">
					共有{{total_count || 0}}位{{shop_name || ''}}会员参与运动
				</view>
				<view class="">
					我击败了{{detail.persent || '100%'}}会员
				</view>
			</view>
		</view>
		<view class="cont-c">
			<!-- #ifdef MP-WEIXIN -->
			<view class="c-item">
				<button class="btn" open-type="share">
					<image src="/static/image/wx/yaoqing.png" mode=""></image>
					<view class="fsz28">
						邀请好友
					</view>
				</button>
			</view>
			<!-- #endif -->
			<view class="c-item" @click="goNavigateHandle('index')">
				<image src="/static/image/wx/tongji.png" mode=""></image>
				<view class="fsz28">
					我的统计
				</view>
			</view>
			<view class="c-item" @click="goNavigateHandle('change')">
				<image src="/static/image/wx/duihuan.png" mode=""></image>
				<view class="fsz28">
					步数兑换
				</view>
			</view>
		</view>
		<view class="cont-b">
			<view class="list">
				<uni-control :current="tab" :values="items" @clickItem="onClickItem" style-type="text" active-color="#64C9FD"></uni-control>
				<view class="leader" v-for="(item,index) in list" :key="index">
					<view class="leader-l">
						<view class="no">
							<image src="/static/image/wx/no1.png" mode="" class="bgi" v-if="index == 0"></image>
							<image src="/static/image/wx/no2.png" mode="" class="bgi" v-if="index == 1"></image>
							<image src="/static/image/wx/no3.png" mode="" class="bgi" v-if="index == 2"></image>
							<image :src="item.image" mode="" class="avatar"></image>
						</view>
						<view class="">
							<view class="rank">
								<text style="color: #FBB03B;" v-if="index == 0">NO.{{index+1}}</text>
								<text style="color: #C8C8C8;" v-if="index == 1">NO.{{index+1}}</text>
								<text style="color: #D98052;" v-if="index == 2">NO.{{index+1}}</text>
								<text v-if="index > 2">NO.{{index+1}}</text>
							</view>
							<view class="name">
								{{item.nickname || ''}}
							</view>
						</view>
					</view>
					<view class="leader-r">
						{{item.step}}步
					</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	import uniControl from "@/components/uni-segmented-control/uni-control.vue";
	// #ifdef MP-WEIXIN
	import shareByWx from '@/components/share/shareByWx.vue'
	// #endif
	import {
		apiBaseUrl
	} from '@/config/config.js'
	export default {
		components: {
			uniControl,
			// #ifdef MP-WEIXIN
			shareByWx,
			// #endif
		},
		data() {
			return {
				tab: 0,
				items: ["今日牛人运动排行榜", "累计运动排行榜"],
				list: [],
				list_today: [], // 今日牛人运动排行榜
				list_total: [], // 累计运动排行榜,
				detail: {},
				total_count: 0,
				start_time: '',
				city:'大连市',
				shop_name:this.$store.state.config.shop_name,
				info:{}
			}
		},
		computed:{
			shareHref() {
				let pages = getCurrentPages()
				let page = pages[pages.length - 1]
				// #ifdef H5 || MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE
				// console.log(apiBaseUrl + 'wap/' + page.route + '?id=' + this.goodsId);
				return apiBaseUrl + 'wap/' + page.route;
				// #endif
			
			}
		},
		onLoad() {
			// TODO::获取当前是否在小程序中，否则返回到首页
			this.isInWx();
			this.info = {
				image_url:'/static/image/wx/pedometer_bgi.png',
				name:"微信运动",
				brief:"分享描述"
			}
			// #ifdef MP-WEIXIN
			this.getShareUrl();
			// #endif
		},
		//分享
		onShareAppMessage() {
			return {
				title: this.info.name,
				// #ifdef MP-ALIPAY
				desc: this.info.brief,
				// #endif
				imageUrl: this.info.image_url,
				path: this.shareUrl
			}
		},
		onShow() {
			this.getRundata()
		},
		onPullDownRefresh() {
			console.log("下拉刷新")
			// 下拉刷新
			this.getRundata()
		},
		methods: {
			//获取分享URL
			getShareUrl() {
				let data = {
					client: 2,
					url: "/pages/share/jump",
					type: 1,
					page: 31
				};
				let userToken = this.$db.get('userToken');
				if (userToken && userToken != '') {
					data['token'] = userToken;
				}
				this.$api.share(data, res => {
					this.shareUrl = res.data
				});
			},
			compareVersion(v1, v2) {
				v1 = v1.split('.')
				v2 = v2.split('.')
				const len = Math.max(v1.length, v2.length)

				while (v1.length < len) {
					v1.push('0')
				}
				while (v2.length < len) {
					v2.push('0')
				}

				for (let i = 0; i < len; i++) {
					const num1 = parseInt(v1[i])
					const num2 = parseInt(v2[i])

					if (num1 > num2) {
						return 1
					} else if (num1 < num2) {
						return -1
					}
				}
				return 0
			},
			isInWx() {
				// 1. 判断当前是否在微信小程序中。
				// #ifndef MP-WEIXIN
				this.$common.errorToShow('只能在微信小程序中使用哦~', () => {
					this.$common.navigateTo('pages/index/index');
				});
				// #endif 

				// #ifdef MP-WEIXIN
				console.log("当前在小程序中哦")
				// #endif 
				// 2. 判断 当前微信的版本是否支持微信运动
				let version = wx.getSystemInfoSync().SDKVersion
				console.log("当前版本：", version)
				if (this.compareVersion(version, '1.2.0') < 0) {
					this.$common.errorToShow('当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试。', () => {
						this.$common.navigateTo('pages/index/index');
					});
				}
				// 3. 判断当前是否登录
				let userToken = this.$db.get('userToken')
				if (!userToken) {
					this.$common.jumpToLogin()
					return false
				}
				// 4. 获取当前所在位置
				let _this = this;
				uni.request({
					url: 'http://pv.sohu.com/cityjson?ie=utf-8',
					success(res) {
						console.log('res---', res);
						if(res.data.indexOf("cname")>-1){
							let obj = res.data.split('"cname": "')[1];
							let a = obj.split('"};')[0]
							console.log("a",a);
							if(a){
								_this.city = a;
							}
						}
					}
				})
			},

			onClickItem(index) {
				this.tab = index
				if (index == 0) {
					this.list = this.list_today;
				} else if (index == 1) {
					this.list = this.list_total;
				} else {
					this.list = []
				}
			},
			goNavigateHandle(url) {
				if (url == 'index') {
					this.$common.navigateTo('./index?start_time=' + this.start_time)
				} else {
					this.$common.navigateTo('./change')
				}

			},
			today() {
				let data = {
					city: this.city
				}
				this.$api.werunToday(data, res => {
					if (res.status) {
						let today = res.data;
						this.list_today = today.rank.today_rank;
						this.list_total = today.rank.total_rank;
						this.total_count = today.total_count;
						this.start_time = today.first_time;
						this.onClickItem(this.tab)
						if(res.log == 'update'){
							this.getRundata();
						}else{
							this.detail = today.log;
						}

					} else {
						this.$common.errorToShow(res.msg);
					}
				})
				return false;
			},
			getRundata() {
				let _this = this
				uni.authorize({
					scope: 'scope.werun',
					success() {
						wx.getWeRunData({
							success(res) {
								let data = {
									encryptedData: res.encryptedData,
									iv: res.iv
								}
								_this.$api.werunUpdate(data, res => {
									if (res.status) {
										// 更新当前页面的数据
										_this.today();
									} else {
										_this.$common.errorToShow(res.msg);
									}
								})
							},
							fial(res) {
								uni.showModal({
									title: '提示',
									content: '请先关注“微信运动”公众号并设置数据来源，以获取并提供微信步数数据',
									showCancel: false,
									confirmText: '知道了',
									success: (res) => {
										this.$common.navigateTo('pages/index/index');
									}
								})

							}
						})
					},
					fail() {
						uni.showModal({
							content: '检测到您没开启获取微信步数权限，是否去开启？拒绝则跳转到首页。',
							confirmText: "确认",
							cancelText: '取消',
							success: (res) => {
								if (res.confirm) {
									uni.openSetting({
										success: (res) => {
											console.log(res);
										}
									})
								} else {
									this.$common.navigateTo('pages/index/index');
								}
							}
						})
					}
				})
			}
		}
	}
</script>

<style lang="scss" scss>
	.content {
		.cont-top {
			position: relative;

			.bgi {
				width: 100%;
				height: 600rpx;
			}

			.bu-info {
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				top: 130rpx;
				width: 450rpx;
				text-align: center;
				color: #fff;
				font-size: 28rpx;

				.num {
					font-size: 80rpx;
					font-weight: 700;
					margin-right: 12rpx;
				}
			}
		}

		.cont-c {
			width: 100%;
			background-color: #fff;
			padding: 20rpx 0;
			display: flex;
			justify-content: space-around;

			.c-item {
				width: 120rpx;
				text-align: center;

				image {
					width: 60rpx;
					height: 60rpx;
					margin-bottom: 12rpx;
				}
				button{
					background-color: #fff;
					padding: 0;
					line-height: 40rpx;
					border: none;
				}
			}
		}

		.cont-b {
			padding: 20rpx;

			.list {
				border-radius: 16rpx 16rpx 0 0;
				background-color: #fff;
				border-bottom: 2rpx solid #f5f5f5;

				.leader {
					padding: 25rpx;
					display: flex;
					justify-content: space-between;

					.leader-l {
						display: flex;
						align-items: center;

						/*定义body的元素垂直居中*/
						.no {
							position: relative;
							width: 80rpx;
							height: 100rpx;
							margin-right: 26rpx;

							.bgi {
								width: 100%;
								height: 100%;
							}

							.avatar {
								width: 73rpx;
								height: 73rpx;
								border-radius: 50%;
								position: absolute;
								top: 24%;
								left: 49%;
								transform: translateX(-50%);
							}
						}

						.rank {
							color: #666;
							font-size: 28rpx;
						}

						.name {
							font-size: 32rpx;
							color: #000;
						}
					}

					.leader-r {
						display: flex;
						align-items: center;
						/*定义body的元素垂直居中*/
						font-size: 28rpx;
						color: #000;
					}
				}
			}
		}
	}

	.segmented-control.text {
		height: 116rpx;
	}
</style>
