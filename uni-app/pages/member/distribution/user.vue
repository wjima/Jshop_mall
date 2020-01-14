<template>
	<view class="content">

		<!-- 用户头像header -->
		<view class='member-top'>
			<view class='member-top-c'>
				<view class="fsz50 color-f">{{info.total_settlement_amount}}</view>
				<view class='fsz26 color-d'>累计收入</view>
			</view>
		</view>
		<!-- 用户头像header end -->

		<!-- 其他功能菜单 -->
		<view class='member-grid'>
			<view class='member-item' v-for="(item, index) in orderItems" :key="index">
				<text class='member-item-text'>{{ item.name }}</text>
				<view class="color-o fsz38">{{ item.nums }}</view>
			</view>
		</view>
		<view class='cell-group margin-cell-group right-img'>
			<view class='cell-item' v-for="(item, index) in utilityMenus" :key="index">
				<view class='cell-item-hd' @click="navigateToHandle(item.router)">
					<image class='cell-hd-icon' :src='item.icon'></image>
					<view class='cell-hd-title'>{{ item.name }}</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='cell-item'>
				<view class='cell-item-hd' @click="createPoster()">
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
	import {
		checkLogin
	} from '@/config/mixins.js'
	import {
		apiBaseUrl
	} from '@/config/config.js'
	export default {
		components: {
			jihaiCopyright
		},
		mixins: [checkLogin],
		data() {
			return {
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
					type: 4,
					id: this.info.store,

				}
				//console.log(this.info.store)
				let pages = getCurrentPages()
				let page = pages[pages.length - 1]
				let page_path = 'pages/share/jump';
				// #ifdef H5
				data.source = 1;
				data.return_url = apiBaseUrl + 'wap/' + page_path;
				// #endif

				// #ifdef MP-WEIXIN
				data.source = 2;
				data.return_url = page_path;
				// #endif

				// #ifdef MP-ALIPAY
				data.source = 3;
				data.return_url = page_path;
				// #endif
				
				// #ifdef APP-PLUS || APP-PLUS-NVUE
				data.source = 5;
				data.return_url = apiBaseUrl + 'wap/' + page_path;
				// #endif

				let userToken = this.$db.get('userToken')

				if (userToken) {
					data.token = userToken
				}
				this.$api.createPoster(data, res => {
					if (res.status) {
						this.$common.navigateTo('/pages/share?poster=' + res.data)
					} else {
						this.$common.errorToShow(res.msg)
					}
				})
			},
		},
		//分享
		onShareAppMessage() {
			let myInviteCode = this.myShareCode ? this.myShareCode : '';
			let ins = this.$common.shareParameterDecode('type=3&invite=' + myInviteCode);
			let path = '/pages/share/jump?scene=' + ins;
			return {
				title: this.$store.state.config.share_title,
				// #ifdef MP-ALIPAY
				desc: this.$store.state.config.share_desc,
				// #endif
				imageUrl: this.$store.state.config.share_image,
				path: path
			}
		}
	}
</script>

<style>
	.member-top {
		position: relative;
		width: 100%;
		height: 340upx;
		background-color: #FF7159;
	}

	.bg-img {
		position: absolute;
		width: 100%;
		height: 100%;
	}

	.member-top-c {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		text-align: center;
	}

	.user-head-img {
		display: block;
		width: 160upx;
		height: 160upx;
		border-radius: 50%;
		overflow: hidden;
		background-color: rgba(255, 255, 255, 0.7);
	}

	.user-name {
		font-size: 30upx;
		color: #fff;
	}

	.member-grid {
		background-color: #fff;
		border-top: 2upx solid #eee;
		padding: 20upx 0;
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
</style>
