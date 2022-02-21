<template>
	<view class="content my-store">
		<view class="" ref="myStore">
			<view class="my-store-t">
				<view class="mst-top">
					<image :src="store_banner_src" mode="widthFix"></image>
				</view>
				<view class="mst-bot">
					<view class='member-grid'>
						<view class='member-item'>
							<image class='member-item-img' :src='store_logo_src'></image>
						</view>
						<view class='member-item'>
							<view class="color-o fsz36">{{total_goods}}</view>
							<text class='member-item-text'>全部宝贝</text>
						</view>
						<!-- #ifdef MP-TOUTIAO -->
						<view class='member-item'>
							<button class='share btn' @click="createPoster()">
								<image class='member-item-icon' src='/static/image/ic-me-collect.png'></image>
								<text class='member-item-text'>分享店铺</text>
							</button>
						</view>
						<!-- #endif -->
						<!-- #ifndef MP-TOUTIAO -->
						<view class='member-item' @click="openPopup()">
							<image class='member-item-icon' src='/static/image/ic-me-collect.png'></image>
							<text class='member-item-text'>收藏本店</text>
						</view>
						<!-- #endif -->
						<view class='member-item'>
							<!-- #ifdef MP-WEIXIN -->
							<button class='share btn' open-type="share">
								<image class='member-item-icon' src='/static/image/qr_code.png'></image>
								<text class='member-item-text'>二维码</text>
							</button>
							<!-- #endif -->
							<!-- #ifndef MP-WEIXIN -->
							<button class='share btn' @click="createPoster()">
								<image class='member-item-icon' src='/static/image/qr_code.png'></image>
								<text class='member-item-text'>二维码</text>
							</button>
							<!-- #endif -->
						</view>
					</view>
				</view>
			</view>
			<view class="my-store-m">
				<view class="search">
					<view class="search-c" @click="goSearch">
						<view class="search-input search-input-p">
							<view class="search-input-p-c">
								{{ searchKey }}
							</view>
						</view>
						<image class="icon search-icon" src="/static/image/zoom.png"></image>
					</view>
				</view>
			</view>
			<!-- 收藏弹出窗 -->
			<lvv-popup position="bottom" ref="lvvpopref" @click="closePopup()">
				<view class="collect-pop" @click="closePopup()">
					<image v-if="isWeixinBrowser" src="/static/image/wxh5.png" mode="widthFix"></image>
					<!-- #ifdef MP-WEIXIN -->
					<image src="/static/image/wxxcx.png" mode="widthFix"></image>
					<!-- #endif -->
					<!-- #ifdef H5 -->
					<view class="h5-tip color-f fsz38">
						<view>请将此页面添加浏览器书签</view>
						<view>方便下次浏览</view>
					</view>
					<!-- #endif -->
				</view>
			</lvv-popup>
		</view>
		<!-- 商品列表 -->
		<view class="img-grids">
			<view v-if="goodsList.length>0">
				<view class="img-grids-item" v-for="(item, index) in goodsList" :key="index" @click="goodsDetail(item.id)">
					<image class="img-grids-item-t have-none" :src="item.image_url" mode='aspectFill'></image>
					<view class="img-grids-item-b">
						<view class="goods-name grids-goods-name">
							{{item.name}}
						</view>
						<view class="goods-item-c">
							<view class="goods-price red-price">￥{{item.price}}</view>
							<image class="goods-cart" src="/static/image/ic-car.png"></image>
						</view>
					</view>
				</view>

			</view>
			<!-- 无数据时默认显示 -->
			<view class="order-none" v-else>
				<image class="order-none-img" src="/static/image/order.png" mode=""></image>
			</view>

		</view>
		<uni-load-more :status="loadStatus"></uni-load-more>

	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue'
    import { h5Url } from '@/config/config.js'
	import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
	import {
		apiBaseUrl
	} from '@/config/config.js'
	export default {
		components: {
			lvvPopup,
			uniLoadMore
		},
		data() {
			return {
				goodsList: [],
				loadStatus: 'more',
				orderItems: [{
						name: '全部宝贝',
						nums: '115'
					},
					{
						name: '收藏本店',
						icon: '/static/image/ic-me-collect.png',
					},
					{
						name: '二维码',
						icon: '/static/image/qr_code.png',
					}
				],
				storeCode: '',
				store_name: '', //店铺名称
				store_logo: '',
				store_logo_src: '',
				store_banner: '',
				store_desc: '', //店铺介绍
				store_banner_src: '',
				isWeixinBrowser: this.$common.isWeiXinBrowser(), //判断是否是微信浏览器
				total_goods: 0,
				page: 1, //默认页码
				searchKey: '请输入关键字搜索',
                shareUrl: '/pages/share/jump'
			}
		},
		onShow: function() {
			if (this.$store.state.config.distribution_store == '2') {
				//跳转到首页
				uni.navigateTo({
					url: '/pages/index/index'
				});
			}
		},
		//加载执行
		onLoad: function(options) {
			let store = options.store;
			this.storeCode = store;
			this.getDistribution(store);
			this.getGoods();
			if(!options.self){
				uni.hideHomeButton();
			}
		},
		mounted() {
			// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE
			window.addEventListener('scroll', this.handleScroll)
			// #endif
		},
		updated() {
			// #ifndef MP-WEIXIN || MP-TOUTIAO || MP-ALIPAY
			// 获取上半部分的整体高度
			this.$nextTick(() => {
				let h = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight; //浏览器高度
				this.top_height = this.$refs.myStore.$el.clientHeight;
			})
			// #endif
		},
		methods: {
			// 显示modal弹出框
			openPopup() {
				this.$refs.lvvpopref.show();
			},
			// 关闭modal弹出框
			closePopup() {
				this.$refs.lvvpopref.close();
			},
			//去搜索
			goSearch() {
				let pages = getCurrentPages();
				let prevPage = pages[pages.length - 2];
				// #ifdef H5 || MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE || MP-TOUTIAO
				if (prevPage && prevPage.route) {
					let search_flag = prevPage.route;
					if (search_flag == 'pages/index/search') {
						uni.navigateBack({
							delta: 1
						});
					} else {
						this.$common.navigateTo('/pages/index/search');
					}
				} else {
					this.$common.navigateTo('/pages/index/search');
				}
				// #endif

				// #ifdef MP-ALIPAY
				if (prevPage && prevPage.__proto__.route) {
					let search_flag = prevPage.__proto__.route;
					if (search_flag == 'pages/index/search') {
						uni.navigateBack({
							delta: 1
						});
					} else {
						this.$common.navigateTo('/pages/index/search');
					}
				} else {
					this.$common.navigateTo('/pages/index/search');
				}
				// #endif
			},
			//跳转到商品详情页面
			goodsDetail: function(id) {
				let url = '/pages/goods/index/index?id=' + id;
				this.$common.navigateTo(url);
			},
			//取得商品数据
			getGoods: function() {
				let data = {
					page: this.page,
					limit: 10
				}
				this.loadStatus = 'loading'
				this.$api.goodsList(data, res => {
					if (res.status) {
						if (this.page >= res.data.total_page) {
							// 没有数据了
							this.loadStatus = 'noMore'
						} else {
							// 未加载完毕
							this.loadStatus = 'more'
							this.page++
						}
						this.goodsList = [...this.goodsList, ...res.data.list]
					} else {
						this.$common.errorToShow(res.msg)
					}
				});
			},
			//获取分销商信息
			getDistribution: function(store) {
				let _this = this;
				_this.$api.getStoreInfo({
					store: store
				}, function(res) {
					if (res.status) {
						_this.store_name = res.data.store_name;
						_this.store_desc = res.data.store_desc;
						_this.store_logo_src = res.data.store_logo_src;
						_this.store_logo = res.data.store_logo;
						_this.store_banner_src = res.data.store_banner_src;
						_this.total_goods = res.data.total_goods;
						uni.setNavigationBarTitle({
							title: _this.store_name
						});
					} else {
						//报错了
						_this.$common.errorToShow(res.msg);
					}
				});
			},
            // 生成邀请海报
            createPoster() {
                let data = {
                    page: 4,
                    params: {
                        store: this.storeCode
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
            //获取分享URL
            getShareUrl() {
                let data = {
                    client: 2,
                    url: "/pages/share/jump",
                    type: 1,
                    page: 4,
                    params: {
                        store: this.storeCode
                    }
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
        watch:{
            storeCode: {
                handler () {
                    this.getShareUrl();
                },
                deep: true
            }
        },
		//上拉加载
		onReachBottom() {
			if (this.loadStatus === 'more') {
				this.getGoods();
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
	.mst-top {
		width: 100%;
	}
	.mst-top image {
		width: 100%;
	}
	.member-grid {
		border-top: 2upx solid #ddd;
		background-color: #fff;
		margin-bottom: 20upx;
	}
	.member-item {
		border-right: 2upx solid #eee;
		height: 90upx;
	}
	.member-item:last-child {
		border-right: none;
	}
	.member-item-img {
		width: 150upx;
		height: 150upx;
		top: -70upx;
		position: absolute;
		left: 42%;
		transform: translateX(-50%);
		border-radius: 10upx;
		background-color: #fff;
		border-radius: 6upx;
		box-shadow: 0 0 10upx #ccc;
	}
	.img-grids {
		padding-bottom: 26upx;
	}
	.img-grids-item {
		margin-bottom: 0;
	}
	.scroll-Y {
		/*  #ifdef  H5  */
		height: calc(100vh - 44px - 0upx);
		/*  #endif  */
		/*  #ifndef H5 */
		height: calc(100vh - 0upx);
		/*  #endif  */
		position: position;
	}
	.collect-pop {
		width: 100%;
		height: 100%;
		position: absolute;
		left: 0;
		bottom: 0;
	}
	.collect-pop image {
		width: 100%;
	}
	.h5-tip {
		text-align: center;
		margin-top: 300upx;
	}
	.member-item .share {
		background: none !important;
		line-height: normal;
	}
</style>