<template>
	<view class="content">

		<view class='member-top'>
			<image class='bg-img' src='/static/image/member-bg.png'></image>
			<view class='member-top-c'>
				<image class='user-head-img' mode="aspectFill" :src='userInfo.avatar'></image>
				<view class="">
					<view class='user-name fsz34'>{{ userInfo.nickname }}</view>
					<view class="fz12 color-f grade" v-if="info.grade_name">
						当前等级：{{ info.grade_name }}
					</view>
					<view class="fz12 color-f grade">
						当前店铺：{{ siteName }}
					</view>
				</view>
			</view>
		</view>
		
		<view class="" style="background-color: #fff;">
			<view class="dist-list">
				<view class="dist-item">
					<view class="dist-num fsz34  color-3">
						{{info.total_settlement_amount||0.00}}
					</view>
					<view class="dist-name fsz26 color-9">
						累计收入(元)
					</view>
					<view class="fsz24 color-3">
						含待结算{{info.freeze_amount||0.00}}元
					</view>
				</view>
				<view class="dist-item" v-for="(item, index) in orderItems" :key="index">
					<view class="dist-num fsz34 color-3">
						{{ item.nums }}
					</view>
					<view class="dist-name fsz26 color-9">
						{{ item.name }}
					</view>
				</view>
			</view>
		</view>
		<view class='cell-group right-img'>
			<view class='cell-item' @click="goWithdraw()">
				<view class='cell-item-hd' >
					<view class='cell-hd-title'>可提现金额（元）</view>
				</view>
				<view class='cell-item-ft'>
					<view class="red-price fsz30">
						{{userInfo.balance||0.00}}
					</view>
					<image class='cell-ft-next icon' src='/static/image/right.png' ></image>
				</view>
			</view>
		</view>
		<!-- 其他功能菜单 -->
		<view class='member-grid margin-cell-group'>
			<view class='member-item'>
				<view class="color-3 fsz38">{{info.today_freeze_amount||0.00}}</view>
				<text class='member-item-text'>今日收益（元）</text>
				
			</view>
			<view class='member-item'>
				<view class="color-3 fsz38">{{info.today_order||0}}</view>
				<text class='member-item-text'>今日订单</text>
				
			</view>
			<view class='member-item'>
				<view class="color-3 fsz38">{{info.today_user||0}}</view>
				<text class='member-item-text'>今日新增客户</text>
				
			</view>
		</view>
		<view class='cell-group margin-cell-group right-img'>
			<view class='cell-item' v-for="(item, index) in utilityMenus" :key="index" @click="navigateToHandle(item.router)">
				<view class='cell-item-hd' >
					<image class='cell-hd-icon' :src='item.icon'></image>
					<view class='cell-hd-title'>{{ item.name }}</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item' @click="createPoster()">
				<view class='cell-item-hd' >
					<image class='cell-hd-icon' src='/static/image/extension.png'></image>
					<view class='cell-hd-title'>我要推广</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
		</view>
		<!-- 其他功能菜单end -->
		<jihaiCopyright></jihaiCopyright>
	</view>
</template>


<script>
	import jihaiCopyright from '@/components/jihai-copyright/jihaiCopyright.vue'
    import { h5Url } from '@/config/config.js'
	import { checkLogin } from '@/config/mixins.js'
	export default {
		components: {
			jihaiCopyright
		},
		mixins: [checkLogin],
		data() {
			return {
				siteName:this.$store.state.config.shop_name,
				// isClerk: false,
				orderItems: {
					freeze: {
						name: '冻结金额',
						nums: "0"
					},
					settlement: {
						name: '已结算金额',
						nums: '0'
					},
					current_month_order: {
						name: '本月订单数',
						nums: '0'
					}
				},
				utilityMenus: {
					invite: {
						name: '我的邀请',
						icon: '/static/image/ic-me-invite.png',
						router: '../invite/list'
					},
					order: {
						name: '推广订单',
						icon: '/static/image/extension_order.png',
						router: './order'
					},
					balance: {
						name: '我的佣金',
						icon: '/static/image/ic-me-balance.png',
						router: '../balance/details?status=5'
					},
					my_store: {
						name: '我的店铺',
						icon: '/static/image/my_store.png',
						router: './my_store'
					},
					store_setting: {
						name: '店铺设置',
						icon: '/static/image/me-ic-set.png',
						router: './store_setting'
					}
				},
				info: {}, //分销商信息
				userInfo: {}, // 用户信息
                shareUrl: '/pages/share/jump'
			}
		},
		onShow() {
			var _this = this;
			if (_this.$store.state.config.distribution_store != '1') {
				delete this.utilityMenus.my_store;
				delete this.utilityMenus.store_setting;
			}
			_this.$api.getDistributioninfo({}, function(res) {
				if (res.status) {
					_this.info = res.data;
					if (res.data.verify != 1) { //审核通过
						_this.$common.redirectTo('/pages/member/distribution/index');
					}
					_this.orderItems.freeze.nums = _this.info.freeze_amount;
					_this.orderItems.settlement.nums = _this.info.settlement_amount;
					_this.orderItems.current_month_order.nums = _this.info.current_month_order;
					if (_this.$store.state.config.distribution_store == '1') {
						_this.utilityMenus.my_store.router = './my_store?store=' + _this.info.store;
					}
					
				} else {
					//报错了
					_this.$common.errorToShow(res.msg);
				}
			});
		},
		onLoad() {
			this.initData()
		},
		methods: {
			navigateToHandle(pageUrl) {
				this.$common.navigateTo(pageUrl)
			},
			orderNavigateHandle(url, tab = 0) {
				if (!this.hasLogin) {
					return this.checkIsLogin()
				}
				this.$store.commit('orderTab', tab)
				this.$common.navigateTo(url)
			},
			goAfterSaleList() {
				if (!this.hasLogin) {
					return this.checkIsLogin()
				}
				this.$common.navigateTo('../after_sale/list')
			},
			// 生成邀请海报
			createPoster() {
                let data = {
                    page: 4,
                    params: {
                        store: this.info.store
                    },
                    type: 3,//海报
                }
                let userToken = this.$db.get('userToken')
                if (userToken) {
                    data.token = userToken
                }
                
                // #ifdef H5 || APP-PLUS || APP-PLUS-NVUE
                data.client = 1;
                data.url = h5Url + 'pages/share/jump'
                // #endif
                
                // #ifdef MP-WEIXIN
                data.client = 2;
				data.url = 'pages/share/jump'
                // #endif
                
                // #ifdef MP-TOUTIAO
                data.client = 4;
                data.url = '/pages/share/jump'
                // #endif
                
                // #ifdef MP-ALIPAY
                data.client = 6;
                data.url = '/pages/share/jump'
                // #endif
                
                this.$api.share(data, res => {
                	if (res.status) {
                		this.$common.navigateTo('/pages/share?poster=' + encodeURIComponent(res.data))
                	} else {
                		this.$common.errorToShow(res.msg)
                	}
                });
			},
			
			initData() {
				// 获取用户信息
				var _this = this
				this.$api.userInfo({}, res => {
					if (res.status) {
						_this.userInfo = res.data
						
					}
                    this.getShareUrl();
				})
			},
			//去提现
			goWithdraw(){
				this.$common.navigateTo('/pages/member/balance/withdraw_cash')
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
            }
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
			}
		}
	}
</script>

<style>
	.member-top {
		position: relative;
		width: 100%;
		height: 200upx;
		/* background-color: #FF7159; */
	}

	.bg-img {
		position: absolute;
		width: 100%;
		height: 100%;
	}

	.member-top-c {
		position: absolute;
		top: 50%;
		left: 50rpx;
		transform: translateY(-50%);
		/* text-align: center; */
		display: flex;
		
	}

	.user-head-img {
		display: block;
		width: 100upx;
		height: 100upx;
		border-radius: 50%;
		overflow: hidden;
		background-color: rgba(255, 255, 255, 0.7);
		margin-right: 20rpx;
	}

	.user-name {
		font-size: 30upx;
		color: #fff;
	}

	.member-grid {
		background-color: #fff;
		/* border-top: 2upx solid #eee; */
		padding: 20upx 0;
	}
	.member-item{
		padding: 0 26rpx;
		text-align: left;
	}
	.margin-cell-group {
		margin: 20upx 0;
		color: #666666;
	}

	.badge {
		left: 80upx;
		top: -6upx;
	}

	button.cell-item-hd {
		background-color: #fff;
		padding: 0;
		line-height: 1.4;
		color: #333;
	}

	button.cell-item-hd:after {
		border: none;
	}

	.login-btn {
		color: #fff;
		width: 160upx;
		height: 50upx;
		line-height: 50upx;
		border-radius: 25upx;
		background: #ff7159;
		font-size: 12px;
		margin-top: 16upx;
	}
	.dist-list{
		overflow: hidden;
		background-color: #fff;
		/* padding: 26rpx 26rpx 0; */
		margin: 0 26rpx;
		border-bottom: 2rpx solid #eee;
	}
	.dist-item{
		width: 50%;
		text-align: left;
		float: left;
		height: 120rpx;
		margin-top: 20rpx;
	}
</style>
