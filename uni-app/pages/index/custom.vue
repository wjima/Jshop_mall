<template>
	<view class="content" style="padding-top: 0upx;">
		<jshop :jdata="pageData"></jshop>
		<jihaiCopyright></jihaiCopyright>
	</view>
</template>
<script>
	import jshop from "@/components/jshop/jshop.vue"
	import jihaiCopyright from "@/components/jihai-copyright/jihaiCopyright.vue"
	import {
		goods
	} from '@/config/mixins.js'
	export default {
		mixins: [goods],
		components: {
			jihaiCopyright,
			jshop
		},
		data() {
			return {
				myShareCode: '', //分享Code
				imageUrl: '/static/image/share_image.png', //店铺分享图片
				pageData: [],
				pageCode: 'mobile_home', //页面布局编码
				statusBarHeight: '0',
				customBarOpacity: false,
				scrollTop: 0,
				showLoad: false, //是否显示loading
				share_name: ''
			}
		},
		computed: {
			appTitle() {
				return this.$store.state.config.shop_name;
			}
		},
		onLoad(e) {
			//增加页面编码，可自定义编码
			if (e.page_code) {
				this.pageCode = e.page_code
			}
			this.initData()
		},
		// 小程序沉浸式状态栏变色
		onPageScroll(e) {
			// console.log(e);
			e.scrollTop > 50 ? this.customBarOpacity = true : this.customBarOpacity = false
		},
		mounted() {
			// #ifdef H5 
			window.addEventListener('scroll', this.handleScroll)
			// #endif
		},
		methods: {
			// 搜索框滑动变色
			handleScroll() {
				var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop
				scrollTop > 50 ? this.searchBarOpacity = true : this.searchBarOpacity = false
			},
			destroyed() {
				window.removeEventListener('scroll', this.handleScroll)
			},
			goSearch() {
				uni.navigateTo({
					url: './search'
				});
			},
			// 首页初始化获取数据
			initData() {
				this.showLoad = true;
				//获取首页配置
				this.$api.getPageConfig({
					code: this.pageCode,
				}, res => {
					if (res.status == true) {
						this.pageData = res.data.items;
						this.share_name = res.data.name;
						uni.setNavigationBarTitle({
							title: res.data.name
						});
						//隐藏loading
						setTimeout(() => {
							this.showLoad = false;
						}, 600);
					}
				});
				
				this.getMyShareCode();
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
			}
		},
		onPullDownRefresh() {
			this.initData();
			uni.stopPullDownRefresh();
		},
		//分享
		onShareAppMessage() {
			let myInviteCode = this.myShareCode ? this.myShareCode : '';
			let ins = this.$common.shareParameterDecode('type=8&page_code=' + this.pageCode + '&invite=' + myInviteCode);
			let path = '/pages/share/jump?scene=' + ins;
			return {
				title: this.share_name,
				// #ifdef MP-ALIPAY
				//desc: this.$store.state.config.share_desc,
				// #endif
				//imageUrl: this.$store.state.config.share_image,
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

	.status_bar {
		height: var(--status-bar-height);
		width: 100%;
		position: fixed;
		top: 0;
		z-index: 999;
		background: rgba(0, 0, 0, 0);
		transition: all .5s;
	}

	.custom-navbar {
		height: 40px;
		line-height: 34px;
		position: fixed;
		width: 100%;
		padding-left: 26upx;
		top: var(--status-bar-height);
		z-index: 999;
		background: rgba(0, 0, 0, 0);
		transition: all .5s;
	}

	.index-logo {
		width: 140upx;
		height: 70upx;
		/* margin: 0 auto; */
	}

	.index-logo-img {
		width: 100%;
		height: 100%;
	}

	.isOpacity {
		background: rgba(255, 255, 255, 1);
		transition: all .5s;
	}

	/* iPhone X in portrait & landscape */
	@media only screen and (min-device-width : 375px) and (max-device-width : 812px) and (-webkit-device-pixel-ratio : 3) {
		.status_bar {
			height: 50px;
		}

		.custom-navbar {
			top: 50px;
		}
	}

	/* iPhone X in landscape */
	@media only screen and (min-device-width : 375px) and (max-device-width : 812px) and (-webkit-device-pixel-ratio : 3) and (orientation : landscape) {
		.status_bar {
			height: 50px;
		}

		.custom-navbar {
			top: 50px;
		}
	}

	/* iPhone X in portrait */
	@media only screen and (min-device-width : 375px) and (max-device-width : 812px) and (-webkit-device-pixel-ratio : 3) and (orientation : portrait) {
		.status_bar {
			height: 50px;
		}

		.custom-navbar {
			top: 50px;
		}
	}
</style>
