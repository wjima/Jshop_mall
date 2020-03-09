<template>
	<view class="content">

		<!-- 用户头像header -->
		<view class='member-top'>
			<image class='bg-img' src='/static/image/member-bg.png'></image>
			<view class='member-top-c'>
				<template v-if="hasLogin">
					<image class='user-head-img' mode="aspectFill" :src='userInfo.avatar'></image>
					<view class='user-name'>{{ userInfo.nickname }}</view>
					<view class="fz12 grade" v-if="userInfo.grade_name">
						{{userInfo.grade_name}}
					</view>
				</template>
				<template v-else>
					<!-- #ifdef H5 || APP-PLUS -->
					<image class='user-head-img' mode="aspectFill" :src='$store.state.config.shop_logo'></image>
					<view class="login-btn" @click="toLogin">
						登录/注册
					</view>
					<!-- #endif -->
					<!-- #ifdef MP-WEIXIN -->
					<view class="user-head-img">
						<open-data type="userAvatarUrl"></open-data>
					</view>
					<view>
						<button class="login-btn" hover-class="btn-hover" @click="goLogin()">授权登录</button>
					</view>
					<!-- #endif -->
					<!-- #ifdef MP-ALIPAY -->
					<view class="user-head-img"></view>
					<view>
						<button class="login-btn" open-type="getAuthorize" @click="getALICode" hover-class="btn-hover">授权登录</button>
					</view>
					<!-- #endif -->
					<!-- #ifdef MP-TOUTIAO -->
					<image class='user-head-img' mode="aspectFill" :src='$store.state.config.shop_logo'></image>
					<view class="login-btn" @click="goLogin()">
						登录/注册
					</view>
					<!-- #endif -->
				</template>

			</view>
		</view>
		<!-- 用户头像header end -->

		<!-- 订单列表信息 -->
		<view class='cell-group'>
			<view class='cell-item right-img' @click="orderNavigateHandle('../order/orderlist')">
				<view class='cell-item-hd'>
					<image class='cell-hd-icon' src='/static/image/userorder.png'></image>
					<view class='cell-hd-title'>我的订单</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
		</view>

		<view class='member-grid'>
			<view class='member-item' v-for="(item, index) in orderItems" :key="index" @click="orderNavigateHandle('../order/orderlist', index + 1)">
				<view class="badge color-f" v-if="item.nums">{{ item.nums }}</view>
				<image class='member-item-icon' :src='item.icon'></image>
				<text class='member-item-text'>{{ item.name }}</text>
			</view>
			<view class='member-item' @click="goAfterSaleList">
				<view class="badge color-f" v-if="afterSaleNums != 0">{{afterSaleNums}}</view>
				<image class='member-item-icon' src='/static/image/me-ic-evaluate.png'></image>
				<text class='member-item-text'>退换货</text>
			</view>
		</view>
		<!-- 订单列表end -->

		<!-- 其他功能菜单 -->
		<!-- 列表样式 -->
		<view class="" v-if="list == 1">
			<view class='cell-group margin-cell-group right-img'>
				<view class='cell-item' v-for="(item, index) in utilityMenus" :key="index" v-if="!item.unshowItem">
					<view class='cell-item-hd' @click="navigateToHandle(item.router)">
						<image class='cell-hd-icon' :src='item.icon'></image>
						<view class='cell-hd-title'>{{ item.name }}</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group right-img' v-if="isClerk">
				<view class='cell-item' v-for="(item, index) in clerk" :key="index">
					<view class='cell-item-hd' @click="navigateToHandle(item.router)">
						<image class='cell-hd-icon' :src='item.icon'></image>
						<view class='cell-hd-title'>{{ item.name }}</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
			</view>
			<view class='cell-group margin-cell-group right-img' v-if="order">
				<view class='cell-item' v-for="(item, index) in order" :key="index">
					<view class='cell-item-hd' @click="navigateToHandle(item.router)">
						<image class='cell-hd-icon' :src='item.icon'></image>
						<view class='cell-hd-title'>{{ item.name }}</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<!-- #ifdef H5 || APP-PLUS-NVUE || APP-PLUS -->
				<view class='cell-item'>
					<view class='cell-item-hd' @click="showChat()">
						<image class='cell-hd-icon' src='/static/image/me-ic-phone.png'></image>
						<view class='cell-hd-title'>联系客服</view>
					</view>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<!-- #endif -->
				<!-- #ifdef MP-WEIXIN -->
				<view class='cell-item'>
					<button class="cell-item-hd " hover-class="none" open-type="contact" bindcontact="showChat" :session-from="kefupara">
						<image src='/static/image/me-ic-phone.png' class='cell-hd-icon'></image>
						<view class='cell-hd-title'>联系客服</view>
					</button>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<!-- #endif -->
				<!-- #ifdef MP-TOUTIAO -->
				<view class='cell-item'>
					<button class="cell-item-hd " hover-class="none" open-type="contact" bindcontact="showChat" :session-from="kefupara">
						<image src='/static/image/me-ic-phone.png' class='cell-hd-icon'></image>
						<view class='cell-hd-title'>联系客服</view>
					</button>
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<!-- #endif -->
				<!-- #ifdef MP-ALIPAY -->
				<view class='cell-item'>
					<contact-button icon="/static/image/kefu2.png" size="170rpx*76rpx" tnt-inst-id="WKPKUZXG" scene="SCE00040186"
					 class="cell-item-hd " hover-class="none" />
					<view class='cell-item-ft'>
						<image class='cell-ft-next icon' src='/static/image/right.png'></image>
					</view>
				</view>
				<!-- #endif -->
			</view>
			
		</view>
		
		<!-- 表格样式 -->
		<view class="margin-cell-group" v-else>
			<view class="sale-block bgf">
				<view class="sale-title">
					<image class='cell-hd-icon' src='/static/image/service.png'></image>
					<view class="">
						我的服务
					</view>
				</view>
				<view class="flc sale-list">
					<view class="item tc" v-for="(item,i) in utilityMenus" :key="i" v-if="(!item.unshowItem && i != 'invoice') || (!item.unshowItem && i == 'invoice' && invoice_switch == 1)">
						<view class="" @click="navigateToHandle(item.router)">
							<view class="">
								<image class='cell-hd-icon' :src='item.icon'></image>
							</view>
							<view class="text">
								<text class="">{{item.name}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="sale-block bgf" v-if="store_switch && isClerk">
				<view class="sale-title">
					<image class='cell-hd-icon' src='/static/image/shop.png'></image>
					<view class="">
						门店管理
					</view>
				</view>
				<view class="flc sale-list">
					<view class="item tc" v-for="(item,i) in clerk" :key="i">
						<view class="" @click="navigateToHandle(item.router)">
							<view class="">
								<image class='cell-hd-icon' :src='item.icon'></image>
							</view>
							<view class="text">
								<text class="">{{item.name}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="sale-block bgf">
				<view class="sale-title">
					<image class='cell-hd-icon' src='/static/image/other.png'></image>
					<view class="">
						其他
					</view>
				</view>
				<view class="flc sale-list">
                    <!-- 微信小程序消息订阅 -->
                    <!-- #ifdef MP-WEIXIN -->
                    <view class="item tc" v-if="isTip">
                    	<view class="" @click="navigateToHandle('../setting/subscription/index')">
                    		<view class="">
                    			<image class='cell-hd-icon' src='/static/image/subscription.png'></image>
                    		</view>
                    		<view class="text">
                    			<text class="">消息订阅</text>
                    		</view>
                    	</view>
                    </view>
                    <!-- #endif -->
					<view class="item tc" v-for="(item,i) in order" :key="i" v-if="!item.unshowItem">
						<view class="" @click="navigateToHandle(item.router)">
							<view class="">
								<image class='cell-hd-icon' :src='item.icon'></image>
							</view>
							<view class="text">
								<text class="">{{item.name}}</text>
							</view>
						</view>
					</view>
					<!-- #ifdef H5 || APP-PLUS || APP-PLUS-NVUE -->
					<view class="item tc">
						<view @click="showChat">
							<view class="">
								<image src='/static/image/me-ic-phone.png' class='cell-hd-icon'></image>
							</view>
							<view class="text">
								<text class="">联系客服</text>
							</view>
						</view>
					</view>
					<!-- #endif -->
					<!-- #ifdef MP-WEIXIN -->
					<!-- todo:: 微信客服 -->
					<!-- #endif -->
					<!-- #ifdef MP-TOUTIAO -->
					<!-- todo:: 头条客服 -->
					<!-- #endif -->
					<!-- #ifdef MP-ALIPAY -->
					<!-- todo:: 支付宝客服 -->
					<!-- #endif -->
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
	export default {
		components: {
			jihaiCopyright
		},
		mixins: [checkLogin],
		data() {
			return {
				open_id: '',
				hasLogin: false,
				userInfo: {}, // 用户信息
				kefupara: '', //客服传递资料
				afterSaleNums: 0,
				isClerk: false,
				alipayNoLogin: true,
				alipayName: '',
				alipayAvatar: '',
				config:'',//配置信息
				orderItems: [{
						name: '待付款',
						icon: '/static/image/me-ic-obligation.png',
						nums: 0
					},
					{
						name: '待发货',
						icon: '/static/image/me-ic-sendout.png',
						nums: 0
					},
					{
						name: '待收货',
						icon: '/static/image/me-ic-receiving.png',
						nums: 0
					},
					{
						name: '待评价',
						icon: '/static/image/me-ic-evaluate.png',
						nums: 0
					}
				],
				utilityMenus: {
					distribution: {
						name: '分销中心',
						icon: '/static/image/distribution.png',
						router: '../distribution/user',
						unshowItem: false
					},
					coupon: {
						name: '我的优惠券',
						icon: '/static/image/ic-me-coupon.png',
						router: '../coupon/index',
						unshowItem: false
					},
					balance: {
						name: '我的余额',
						icon: '/static/image/ic-me-balance.png',
						router: '../balance/index',
						unshowItem: false
					},
					invoice: {
						name: '我的发票',
						icon: '/static/image/ic-me-invoice.png',
						router: '../invoice/index',
						unshowItem: false
					},
					integral: {
						name: '我的积分',
						icon: '/static/image/integral.png',
						router: '../integral/index',
						unshowItem: false
					},
					address: {
						name: '地址管理',
						icon: '/static/image/me-ic-site.png',
						router: '../address/list',
						unshowItem: false
					},
					collection: {
						name: '我的收藏',
						icon: '/static/image/ic-me-collect.png',
						router: '../collection/index',
						unshowItem: false
					},
					history: {
						name: '我的足迹',
						icon: '/static/image/ic-me-track.png',
						router: '../history/index',
						unshowItem: false
					},
					
				},
				clerk: [{
						name: '提货单列表',
						icon: '/static/image/ic-me-take.png',
						router: '../take_delivery/list'

					},
					{
						name: '提货单核销',
						icon: '/static/image/me-ic-about.png',
						router: '../take_delivery/index'
					}
				],
				order: {
					invite: {
						name: '邀请好友',
						icon: '/static/image/ic-me-invite.png',
						router: '../invite/index',
						unshowItem: true
					},
					setting: {
						name: '系统设置',
						icon: '/static/image/me-ic-set.png',
						router: '../setting/index',
						unshowItem: false
					}
				},
				list: 2,
                suTipStatus: false
			}
		},
		onShow() {
			this.initData()
		},
		methods: {
			goLogin(){
				uni.navigateTo({
					url:'/pages/login/choose/index'
				})
			},
			getUserInfo(e) {
				let _this = this
				//return false;
				if (e.detail.errMsg == 'getUserInfo:fail auth deny') {
					_this.$common.errorToShow('未授权')
				} else {
					var data = {
						open_id: _this.open_id,
						iv: e.detail.iv,
						edata: e.detail.encryptedData,
						signature: e.detail.signature
					}
					//有推荐码的话，带上
					var invitecode = _this.$db.get('invitecode')
					if (invitecode) {
						data.invitecode = invitecode
					}
					_this.toWxLogin(data)
				}
			},
			getALICode() {
				let that = this
				uni.login({
					scopes: 'auth_user',
					success: (res) => {
						if (res.authCode) {
							uni.getUserInfo({
								provider: 'alipay',
								success: function(infoRes) {
									if (infoRes.errMsg == "getUserInfo:ok") {
										let user_info = {
											'nickname': infoRes.nickName,
											'avatar': infoRes.avatar
										}
										that.aLiLoginStep1(res.authCode, user_info);
									}
								},
								fail: function(errorRes) {
									this.$common.errorToShow('未取得用户昵称头像信息');
								}
							});
						} else {
							this.$common.errorToShow('未取得code');
						}
					},
					fail: function(res) {
						this.$common.errorToShow('用户授权失败my.login');
					}
				});
			},
			getWxCode() {
				let that = this
				uni.login({
					scopes: 'auth_user',
					success: function(res) {
						if (res.code) {
							that.wxLoginStep1(res.code)
						} else {
							this.$common.errorToShow('未取得code')
						}
					},
					fail: function(res) {
						this.$common.errorToShow('用户授权失败wx.login')
					}
				})
			},
			wxLoginStep1(code) {
				this.$api.login1({
					code
				}, res => {
					if (res.status) {
						this.open_id = res.data
					} else {
						this.$common.errorToShow(res.msg, function() {
							uni.navigateBack({
								delta: 1
							})
						})
					}
				})
			},
			aLiLoginStep1(code, user_info) {
				let data = {
					'code': code,
					'user_info': user_info
				}
				this.$api.alilogin1(data, res => {
					this.alipayNoLogin = false;
					if (res.status) {
						this.open_id = res.data.user_wx_id
						//判断是否返回了token，如果没有，就说明没有绑定账号，跳转到绑定页面
						if (!res.data.hasOwnProperty('token')) {
							this.$common.redirectTo('/pages/login/login/index?user_wx_id=' + res.data.user_wx_id);
						} else {
							this.$db.set('userToken', res.data.token)
							this.initData()
						}
					} else {
						this.$common.errorToShow(res.msg)
					}
				})
			},
			toWxLogin(data) {
				let _this = this
				_this.$api.login2(data, function(res) {
					if (res.status) {
						//判断是否返回了token，如果没有，就说明没有绑定账号，跳转到绑定页面
						if (typeof res.data.token == 'undefined') {
							uni.redirectTo({
								url: '/pages/login/login/index?user_wx_id=' + res.data.user_wx_id
							})
						} else {
							_this.$db.set('userToken', res.data.token)
							_this.initData()
						}
					} else {
						_this.$common.errorToShow('登录失败，请重试')
					}
				})
			},
			toLogin() {
				this.$common.navigateTo('../../login/login/index1')
			},
			initData() {
				// 获取用户信息
				var _this = this
				//判断是开启分销还是原始推广
				this.$api.shopConfig(res => {
					this.config = res;
					if (res.open_distribution) {
						this.order.invite.unshowItem = true
					} else {
						this.utilityMenus.distribution.unshowItem = true
						this.order.invite.unshowItem = false
					}
				})
				if (this.$db.get('userToken')) {
					this.hasLogin = true
					this.$api.userInfo({}, res => {
						if (res.status) {
							_this.userInfo = res.data
							// #ifdef MP-WEIXIN
							//微信小程序打开客服时，传递用户信息
							var kefupara = {}
							kefupara.nickName = res.data.nickname
							kefupara.tel = res.data.mobile
							_this.kefupara = JSON.stringify(kefupara)
							// #endif
							// 获取订单不同状态的数量
							let data = {
								ids: '1,2,3,4',
								isAfterSale: true
							}
							_this.$api.getOrderStatusSum(data, res => {
								if (res.status) {
									_this.orderItems.forEach((item, key) => {
										item.nums = res.data[key + 1]
									})
									_this.afterSaleNums = res.data.isAfterSale ?
										res.data.isAfterSale :
										0
								}
							})
							//判断是否是店员
							this.$api.isStoreUser({}, res => {
								this.isClerk = res.flag
							})
						}
					})
				} else {
					this.hasLogin = false
					// #ifdef MP-WEIXIN
					this.getWxCode()
					// #endif
				}
                
                this.userIsSubscription();
			},
			navigateToHandle(pageUrl) {
				if (!this.hasLogin) {
					return this.checkIsLogin()
				}
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
			//在线客服,只有手机号的，请自己替换为手机号
			showChat() {
				// #ifdef H5
				let _this = this
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
				})
				//传递客户信息
				window._AIHECONG('customer', {
					head: _this.userInfo.avatar,
					'名称': _this.userInfo.nickname,
					'手机': _this.userInfo.mobile
				})
				window._AIHECONG('showChat')
				// #endif

				// 打开客服页面
				// #ifdef APP-PLUS || APP-PLUS-NVUE
				this.$common.navigateTo('../customer_service/index');
				// #endif
				
				// 头条系客服
				// #ifdef MP-TOUTIAO
				if(this.shopMobile != 0){
					let _this = this;
					tt.makePhoneCall({
						phoneNumber: this.shopMobile.toString(),
						success(res) {},
						fail(res) {}
					});
				}else{
					_this.$common.errorToShow('暂无设置客服电话');
				}
				// #endif
			},
			// toPages(url) {
			// 	console.log(url)
			// 	let userToken = this.$db.get('userToken')
			// 	if (!userToken) {
			// 		common.jumpToLogin()
			// 		return false
			// 	} else {
			// 		this.$common.navigateTo(url)

			// 	}
			// },
            //查询用户订阅
            userIsSubscription() {
                let userToken = this.$db.get("userToken");
                if (userToken && userToken != '') {
                	this.$api.subscriptionIsTip(res => {
                        if (res.status) {
                            if (res.switch) {
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
		},
		computed: {
			// 获取店铺联系人手机号
			shopMobile(){
				return this.$store.state.config.shop_mobile || 0;
			},
			invoice_switch() {
				return this.$store.state.config.invoice_switch || 2;
			},
			store_switch() {
				return this.$store.state.config.store_switch || 0;
			},
            isTip() {
                return this.suTipStatus;
            }
		},
		watch: {}
	}
</script>

<style lang="scss" scoped>
	.member-top {
		position: relative;
		width: 100%;
		height: 340upx;
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
		margin: 0 auto 16upx;
	}

	.user-name {
		font-size: 30upx;
		color: #fff;
		margin-bottom: 16upx;
	}

	.grade {
		color: #FFF;
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
		width: 180upx;
		height: 50upx;
		line-height: 50upx;
		border-radius: 25upx;
		background: #ff7159;
		font-size: 12px;
	}
	
	.sale-block {
		padding: 0 20upx;
		margin-bottom: 20upx;

		.sale-title {
			height: 72rpx;
			line-height: 72rpx;
			border-bottom: 2rpx solid #f0f0f0;
			display: flex;
			align-items: center;
			.iconfont {
				margin-right: 12upx;
				color: $theme-color;
			}
		}

		.sale-list {
			justify-content: flex-start;
			flex-wrap: wrap;

			.item {
				width: 25%;
				padding: 20upx 0;

				.iconfont {
					font-size: 40upx;
					color: #5e5e5e;
				}

				.text {
					margin-top: 12upx;
					color: #4d4d4d;
					font-size: 24rpx;
				}
			}
			.tc {
			    text-align: center;
				.cell-hd-icon{
					float: none;
					width: 60rpx;
					height: 60rpx;
					margin: 0;
				}
			}
		}
	}
</style>
