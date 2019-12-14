<template>
	<view class="content" style="padding-top: 0upx;">
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
		<contact-button class="service icon" icon="/static/image/seller-content.png" size="80rpx*80rpx" tnt-inst-id="WKPKUZXG" scene="SCE00040186" hover-class="none" />
		<!-- #endif -->
		<!-- #ifdef MP-TOUTIAO -->
		<!-- 头条客服 -->
		<!-- #endif -->
		<red-bag v-if="redBagShow" @click="handleGet"></red-bag>
	</view>
</template>
<script>
	import jshop from '@/components/jshop/jshop.vue'
	import jihaiCopyright from '@/components/jihai-copyright/jihaiCopyright.vue'
	import uniCountdown from '@/components/uni-countdown/uni-countdown.vue'
	import redBag from '@/components/red-bag/index'
	import {
		goods
	} from '@/config/mixins.js'
	import {
		goBack
	} from '@/config/mixins.js'
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
				myShareCode: '', //分享Code
				imageUrl: '/static/image/share_image.png', //店铺分享图片
				pageData: [],
				pageCode: 'mobile_home', //页面布局编码
				pintuan: [], //拼团列表,
				redBagShow: false, //红包
				config:'',//配置信息
				userInfo: {}, // 用户信息
				kefupara: '', //客服传递资料
				copy: false
			}
		},
		updated() {
			this.copy = true;
		},
		computed: {
			appTitle() {
				return this.$store.state.config.shop_name
			},
			// 获取店铺联系人手机号
			shopMobile(){
				return this.$store.state.config.shop_mobile || 0;
			}
		},
		onLoad(e) {
			this.initData()
			if(this.$store.state.config.shop_name){
				uni.setNavigationBarTitle({
					title: this.$store.state.config.shop_name||''
				});
			}
		},
		methods: {
			//领取红包
			handleGet() {},
			destroyed() {
				window.removeEventListener('scroll', this.handleScroll)
			},
			goSearch() {
				uni.navigateTo({
					url: './search'
				})
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
				});
				
				this.getMyShareCode();
				
				var _this = this
				if (this.$db.get('userToken')) {
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
						}
					})
				}
			},
			getMyShareCode() {
				let userToken = this.$db.get("userToken");
				if (userToken && userToken != '') {
					// 获取我的分享码
					this.$api.shareCode({}, res => {
						if (res.status) {
							this.myShareCode = res.data ? res.data : '';
						}
					});
				}
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
			
				// 客服页面
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
			}
		},
		onPullDownRefresh() {
			this.initData()
			//this.$db.del('all_cat');
			uni.stopPullDownRefresh()
		},
		//分享
		onShareAppMessage() {
			let myInviteCode = this.myShareCode ? this.myShareCode : '';
			let ins = this.$common.shareParameterDecode('type=1&invite=' + myInviteCode);
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
	.search {
		/* position: fixed; */
		/*  #ifdef  H5  */
		/* top: 44px; */
		/*  #endif  */
		/*  #ifndef  H5  */
		/* top: 0; */
		/*  #endif  */
	}

	.cell-item {
		border: none;
	}

	.cell-ft-text {
		font-size: 22upx;
		color: #999;
	}
	
	.service{
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
		z-index: 999;
	}
	.service .icon{
		width: 60rpx;
		height: 60rpx;
	}
</style>
