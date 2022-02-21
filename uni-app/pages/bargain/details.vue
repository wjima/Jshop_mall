<template>
	<view class="content">

		<view class="nav-back">
			<view class="back-btn" @click="backBtn()">
				<image class="icon" src="/static/image/back-black.png" mode=""></image>
			</view>
		</view>


		<view class="content-top">

			<!-- 轮播图 -->
			<view class='swiper'>
				<swiper class="swiper-c" :indicator-dots="swiper.indicatorDots" :autoplay="swiper.autoplay" :interval="swiper.interval"
				 :duration="swiper.duration">
					<swiper-item class="have-none" v-for="(item, index) in goodsInfo.album" :key="index" @click="clickImg(item)">
						<image class='' :src='item' mode="aspectFill"></image>
					</swiper-item>
				</swiper>
			</view>
			<!-- 轮播图end -->
			<view class="time-wrap">
				剩余时间: 18天19时57分8秒
			</view>
			<view class='cell-group shop-info-wrap'>
				<view class="shop-info">
					<view class="title"> <text class="tag">砍价</text> <text>FENDI男款军绿色牛皮手拿包</text> </view>
					<view class="icon-wrap">
						规则
					</view>
				</view>
				<view class="money">原价 <text>￥70</text> </view>
				<view class="pep">已有34人参与砍价</view>
			</view>
			<view class='cell-group progress-wrap'>
				<view class="title">砍价流程</view>
				<view class="progress-step">
					<view :class="[ `step${idx + 1}`, 'step']"  v-for="(item, idx) in stepArr " :key="idx">
						<view class="step-top">
							<image src="/static/images/step1.png" style="width: 100%; height: 60rpx;" mode=""></image>
							<text>{{idx + 1}}</text>
							<image src="/static/images/step2.png" style="width: 100%; height: 60rpx;" mode=""></image>
						</view>
						<view class="step-desc">{{item}}</view>
					</view>
				</view>
			</view>

			<view class="goods-content">
				<uni-segmented-control :current="current" :values="items" @clickItem="onClickItem" style-type="text" active-color="#333"></uni-segmented-control>
				<view class="goods-content-c">
					<view class="goods-detail" v-show="current === 0">
						<jshopContent :content="goodsInfo.intro" v-if="goodsInfo.intro"></jshopContent>
						<view class="comment-none" v-else>
							<image class="comment-none-img" src="/static/image/order.png" mode=""></image>
						</view>
					</view>
					<view class="goods-parameter" v-show="current === 1">
						<view class='cell-group' v-if="goodsParams.length">
							<view class='cell-item' v-for="(item, index) in goodsParams" :key="index">
								<view class='cell-item-hd'>
									<view class='cell-hd-title'>{{ item.name || ''}}</view>
								</view>
								<view class='cell-item-bd'>
									<text class='cell-bd-text'>{{ item.value || ''}}</text>
								</view>
							</view>
						</view>
						<view class="comment-none" v-else>
							<image class="comment-none-img" src="/static/image/order.png" mode=""></image>
						</view>
					</view>
					<view class="goods-assess" v-show="current === 2">
						<view v-if="goodsComments.list.length">
							<view class="goods-assess-item" v-for="(item, index) in goodsComments.list" :key="index">
								<view class='cell-group'>
									<view class='cell-item goods-title-item cell-item-mid'>
										<view class='cell-item-hd'>
											<image class='user-head-img' :src='item.user.avatar' mode="aspectFill"></image>
										</view>
										<view class='cell-item-bd'>
											<view class="cell-bd-view">
												<text class="cell-bd-text">{{ (item.user.nickname && item.user.nickname != '')?item.user.nickname:item.user.mobile }}</text>
												<view class="cell-bd-text-right">
													<uni-rate size="16" disabled="true" :value="item.score"></uni-rate>
												</view>
											</view>
											<view class="cell-bd-view">
												<text class="cell-bd-text color-9" style="margin-right: 16upx;">{{ item.ctime || ''}}</text>
												<text class="cell-bd-text color-9">{{ item.addon || ''}}</text>
											</view>
										</view>
									</view>
								</view>
								<view class="gai-body">
									<view class="gai-body-text">
										{{ item.content || ''}}
									</view>
									<view class="gai-body-img" v-if="item.images_url.length">
										<image :src="img" mode="aspectFill" v-for="(img, key) in item.images_url" :key="key" @click="clickImg(img)"></image>
									</view>
									<view class="seller-content" v-if="item.seller_content">
										<view class="seller-content-top">
											<image class="seller-content-img" src="/static/image/seller-content.png"></image>掌柜回复
										</view>
										{{item.seller_content || ''}}
									</view>
								</view>
							</view>
							<uni-load-more :status="goodsComments.loadStatus"></uni-load-more>
						</view>
						<view class="comment-none" v-else>
							<image class="comment-none-img" src="/static/image/order.png" mode=""></image>
						</view>
					</view>
				</view>
			</view>
		</view>

		<lvv-popup position="bottom" ref="share">

			<!-- #ifdef H5 -->
			<shareByH5 :ifwx="ifwx" :goodsId="goodsInfo.id" :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name"
			 :shareContent="goodsInfo.brief" :shareHref="shareHref" @close="closeShare()"></shareByH5>
			<!-- #endif -->

			<!-- #ifdef MP-WEIXIN -->
			<shareByWx :goodsId="goodsInfo.id" :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief"
			 :shareHref="shareHref" @close="closeShare()"></shareByWx>
			<!-- #endif -->

			<!-- #ifdef MP-ALIPAY -->
			<shareByAli :goodsId="goodsInfo.id" :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief"
			 :shareHref="shareHref" @close="closeShare()"></shareByAli>
			<!-- #endif -->

			<!-- #ifdef MP-TOUTIAO -->
			<shareByTt :goodsId="goodsInfo.id" :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief"
			 :shareHref="shareHref" @close="closeShare()"></shareByTt>
			<!-- #endif -->

			<!-- #ifdef APP-PLUS || APP-PLUS-NVUE -->
			<shareByApp :goodsId="goodsInfo.id" :shareImg="goodsInfo.image_url" :shareTitle="goodsInfo.name" :shareContent="goodsInfo.brief"
			 :shareHref="shareHref" @close="closeShare()"></shareByApp>
			<!-- #endif -->

		</lvv-popup>

		<!-- 弹出层 -->
		<lvv-popup position="bottom" ref="lvvpopref">
			<view style="width: 100%;max-height: 804upx;background: #FFFFFF;position: absolute;left:0;bottom: 0;">
				<view class="pop-c">
					<view class="pop-t">
						<view class='goods-img'>
							<image :src='product.image_path' mode='aspectFill'></image>
						</view>
						<view class='goods-information'>
							<view class='pop-goods-name'>{{ product.name || ''}}</view>
							<view class='pop-goods-price red-price'>￥ {{ product.price || ''}}</view>
						</view>
						<view class='close-btn' @click="toclose()">
							<image src='/static/image/close.png'></image>
						</view>
					</view>
					<scroll-view class="pop-m" scroll-y="true" style="max-height: 560upx;">
						<spec :spesData="defaultSpesDesc" ref="spec" @changeSpes="changeSpes"></spec>
						<view class="goods-number">
							<text class="pop-m-title">数量</text>
							<view class="pop-m-bd-in">
								<uni-number-box :min="minNums" :max="product.stock" :value="buyNum" @change="bindChange"></uni-number-box>
							</view>
						</view>
					</scroll-view>
					<view class="pop-b">
						<button class='btn btn-square btn-b btn-all' hover-class="btn-hover2" @click="clickHandle()" :disabled='submitStatus'
						 :loading='submitStatus' v-if="product.stock">确定</button>
						<button class='btn btn-square btn-g btn-all' v-else>已售罄</button>
					</view>
				</view>
			</view>
		</lvv-popup>
		<!-- 弹出层end -->

		<div id="qrCode" ref="qrCodeDiv"></div>
		<!-- 底部按钮 -->
		<view class="goods-bottom">
			<!-- 客服按钮 -->
			<!-- #ifdef H5 || APP-PLUS-NVUE || APP-PLUS -->
			<view class="goods-bottom-ic" @click="showChat()">
				<image class="icon" src="/static/image/customerservice.png" mode=""></image>
				<view>客服</view>
			</view>
			<!-- #endif -->
			<!-- #ifdef MP-WEIXIN -->
			<button class="goods-bottom-ic weiContact" hover-class="none" open-type="contact" bindcontact="showChat"
			 :session-from="kefupara">
				<image class="icon" src="/static/image/customerservice.png" mode=""></image>
				<view>客服</view>
			</button>
			<!-- #endif -->
			<!-- #ifdef MP-ALIPAY -->
			<contact-button class="goods-bottom-ic icon" icon="/static/image/customerservice.png" size="80rpx*80rpx" tnt-inst-id="WKPKUZXG"
			 scene="SCE00040186" hover-class="none" />
			<!-- #endif -->
			<!-- #ifdef MP-TOUTIAO -->
			<view class="goods-bottom-ic" @click="showChat()">
				<image class="icon" src="/static/image/customerservice.png" mode=""></image>
				<view>客服</view>
			</view>
			<!-- #endif -->

			<view class="goods-bottom-ic" @click="redirectCart">
				<view class="badge color-f" v-if="cartNums">{{ cartNums || ''}}</view>
				<image class="icon" src="/static/image/ic-me-car.png" mode=""></image>
				<view>购物车</view>
			</view>

			<view class="goods-bottom-ic" @click="collection">
				<image class="icon" :src="isfav ? favLogo[1] : favLogo[0]" mode=""></image>
				<view v-if="!isfav">收藏</view>
				<view v-if="isfav">已收藏</view>
			</view>

			<button class='btn btn-square btn-g' @click="toshow(1)" hover-class="btn-hover2">直接购买</button>
			<button class='btn btn-square btn-b' @click="toshow(2)" hover-class="btn-hover2">立即砍价</button>
		</view>
		<!-- 底部按钮end -->

		<!-- 右边浮动球 -->
		<uni-fab :pattern="pattern" :content="content" :horizontal="horizontal" :vertical="vertical" :direction="direction"
		 @trigger="trigger"></uni-fab>

	</view>
</template>

<script>
	import uniSegmentedControl from "@/components/uni-segmented-control/uni-segmented-control.vue";
	import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
	import uniNumberBox from "@/components/uni-number-box/uni-number-box.vue";
	import uniRate from "@/components/uni-rate/uni-rate.vue";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	import uniFab from '@/components/uni-fab/uni-fab.vue';
	import {
		get
	} from '@/config/db.js';
	import {
		apiBaseUrl
	} from '@/config/config.js'
	import spec from '@/components/spec/spec.vue'
	// #ifdef H5
	import shareByH5 from '@/components/share/shareByh5.vue'
	// #endif
	// #ifdef MP-WEIXIN
	import shareByWx from '@/components/share/shareByWx.vue'
	// #endif
	// #ifdef MP-TOUTIAO
	import shareByTt from '@/components/share/shareByTt.vue'
	// #endif
	// #ifdef MP-ALIPAY
	import shareByAli from '@/components/share/shareByAli.vue'
	// #endif
	// #ifdef APP-PLUS || APP-PLUS-NVUE
	import shareByApp from '@/components/share/shareByApp.vue'
	// #endif
	import jshopContent from '@/components/jshop/jshop-content.vue' //视频和文本解析组件

	export default {
		components: {
			uniSegmentedControl,
			lvvPopup,
			uniNumberBox,
			uniRate,
			uniLoadMore,
			uniFab,
			spec,
			jshopContent,
			// #ifdef H5
			shareByH5,
			// #endif
			// #ifdef MP-WEIXIN
			shareByWx,
			// #endif
			// #ifdef MP-TOUTIAO
			shareByTt,
			// #endif
			// #ifdef MP-ALIPAY
			shareByAli,
			// #endif
			// #ifdef APP-PLUS || APP-PLUS-NVUE
			shareByApp,
			// #endif
		},
		data() {
			return {
				stepArr: ['选择心仪商品', '邀请好友砍价', '砍到低价后支付'],
				swiper: {
					indicatorDots: true,
					autoplay: true,
					interval: 3000,
					duration: 800,
				}, // 轮播图属性设置
				items: ['图文详情', '商品参数', '买家评论'],
				current: 0, // init tab位
				goodsId: 0, // 商品id
				goodsInfo: {}, // 商品详情
				cartNums: 0, // 购物车数量
				product: {}, // 规格详情
				goodsParams: [], // 商品参数信息
				goodsComments: {
					loadStatus: 'more',
					page: 1,
					limit: 5,
					list: []
				}, // 商品评论信息
				buyNum: 1, // 选定的购买数量
				minBuyNum: 1, // 最小可购买数量
				type: 2, // 1加入购物车 2购买
				isfav: false, // 商品是否收藏
				favLogo: [
					'/static/image/ic-me-collect.png',
					'/static/image/ic-me-collect2.png'
				],
				horizontal: 'right', //右下角弹出按钮
				vertical: 'bottom',
				direction: 'vertical',
				pattern: {
					color: '#7A7E83',
					backgroundColor: '#fff',
					selectedColor: '#007AFF',
					buttonColor: "#FF7159"
				},
				content: [{
						iconPath: '/static/image/tab-ic-hom-selected.png',
						selectedIconPath: '/static/image/tab-ic-hom-unselected.png',
						// text: '首页',
						active: false,
						url: '/pages/index/index'
					},
					{
						iconPath: '/static/image/tab-ic-me-selected.png',
						selectedIconPath: '/static/image/tab-ic-me-unselected.png',
						// text: '个人中心',
						active: false,
						url: '/pages/member/index/index'
					}
				],
				submitStatus: false,
				config: '', //配置信息
				goodsShowWord: [],
                shareUrl: '/pages/share/jump'
			}
		},
		onLoad(options) {
			//获取商品ID
			if (options.id != '') {
				this.goodsId = options.id;
			}

			if (this.goodsId) {
				this.getGoodsDetail();
				this.getGoodsParams();
				this.getGoodsComments();
			} else {
				this.$common.errorToShow('获取失败', () => {
					uni.navigateBack({
						delta: 1
					});
				});
			}

			// 获取购物车数量
			this.getCartNums();
			this.$api.shopConfig(res => {
				this.config = res;
				console.log(res)
				this.goodsShowWord = res.goods_show_word;
			});
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
			};
			this.ifwxl();
		},
		onShow() {
			this.submitStatus = false;
		},
		computed: {
			// 规格切换计算规格商品的 可购买数量
			minNums() {
				return this.product.stock > this.minBuyNum ? this.minBuyNum : this.product.stock;
			},
			// 判断商品是否是多规格商品  (为了兼容小程序 只能写在计算属性里面了)
			isSpes() {
				if (this.product.hasOwnProperty('default_spes_desc') && Object.keys(this.product.default_spes_desc).length) {
					return true;
				} else {
					return false;
				}
			},
			// 优惠信息重新组装
			promotion() {
				let arr = [];
				if (this.product.promotion_list) {
					for (let k in this.product.promotion_list) {
						arr.push(this.product.promotion_list[k]);
					}
				}
				return arr;
			},
			shareHref() {
				let pages = getCurrentPages()
				let page = pages[pages.length - 1]
				// #ifdef H5 || MP-WEIXIN || APP-PLUS || APP-PLUS-NVUE
				return apiBaseUrl + 'wap/' + page.route + '?id=' + this.goodsId;
				// #endif

				// #ifdef MP-ALIPAY
				return apiBaseUrl + 'wap/' + page.__proto__.route + '?id=' + this.goodsId;
				// #endif
			},
			// 获取店铺联系人手机号
			shopMobile() {
				return this.$store.state.config.shop_mobile || 0;
			},
            defaultSpesDesc() {
                return this.product.default_spes_desc;
            }
		},
		onReachBottom() {
			if (this.current === 2 && this.goodsComments.loadStatus === 'more') {
				this.getGoodsComments();
			}
		},
		methods: {
			// 判断是不是微信浏览器
			ifwxl() {
				this.ifwx = this.$common.isWeiXinBrowser()
			},
			// 返回上一页
			backBtn() {
				var pages = getCurrentPages();
				if (pages.length > 1) {
					uni.navigateBack({
						delta: 1
					});
				} else {
					uni.navigateTo({
						url: '/pages/index/index'
					});
				}
			},
			// 获取商品详情
			getGoodsDetail() {
				let data = {
					id: this.goodsId
				}

				// 如果用户已经登录 要传用户token
				let userToken = this.$db.get("userToken");

				if (userToken) {
					data['token'] = userToken;
				}

				this.$api.goodsDetail(data, res => {
					if (res.status == true) {
						let info = res.data;
						let products = res.data.product;

						//var htmlString = info.intro; //replace(/\\/g, "").replace(/<img/g, "<img style=\"display:none;\"")
						//info.intro = htmlParser(htmlString);
						this.goodsInfo = info;
						this.isfav = this.goodsInfo.isfav === 'true' ? true : false;
						this.product = this.spesClassHandle(products);



						// 判断如果登录用户添加商品浏览足迹
						if (userToken) {
							this.goodsBrowsing();
						}
					} else {
						this.$common.errorToShow(res.msg, () => {
							uni.navigateBack({
								delta: 1
							});
						})
					}
				})
			},
			// 获取购物车数量
			getCartNums() {
				let userToken = this.$db.get("userToken");
				if (userToken && userToken != '') {
					// 获取购物车数量
					this.$api.getCartNum({}, res => {
						if (res.status) {
							this.cartNums = res.data;
						}
					})
				}
			},
			// 显示modal弹出框
			toshow(type) {
				if(type == 2) {
					return
				}
				this.type = type;
				this.$refs.lvvpopref.show();
			},
			// 关闭modal弹出框
			toclose() {
				this.$refs.lvvpopref.close();
			},
			// 切换商品规格
			changeSpes(obj) {
				let index = obj.v;
				let key = obj.k;

                let userToken = this.$db.get('userToken');
                let tmp_default_spes_desc = JSON.parse(this.product.default_spes_desc);
                if (tmp_default_spes_desc[index][key].hasOwnProperty('product_id') && tmp_default_spes_desc[index][key].product_id) {
                    this.$refs.spec.changeSpecData();
                    this.$api.getProductInfo({id: tmp_default_spes_desc[index][key].product_id,token:userToken}, res => {
                        if (res.status == true) {
                            // 切换规格判断可购买数量
                            this.buyNum = res.data.stock > this.minBuyNum ? this.minBuyNum : res.data.stock;
                            this.product = this.spesClassHandle(res.data);
                        }
                    });
                    uni.showLoading({
                        title: '加载中'
                    });
                    setTimeout(function () {
                        uni.hideLoading();
                    }, 1000);
                }
			},
			// 多规格样式统一处理
			spesClassHandle(products) {
                // 判断是否是多规格 (是否有默认规格)
                if (products.hasOwnProperty('default_spes_desc')) {
                    let spes = products.default_spes_desc;
                    for (let key in spes) {
                        for (let i in spes[key]) {
                            if (spes[key][i].hasOwnProperty('is_default') && spes[key][i].is_default === true) {
                                this.$set(spes[key][i], 'cla', 'pop-m-item selected');
                            } else if (spes[key][i].hasOwnProperty('product_id') && spes[key][i].product_id) {
                                this.$set(spes[key][i], 'cla', 'pop-m-item not-selected');
                            } else {
                                this.$set(spes[key][i], 'cla', 'pop-m-item none');
                            }
                        }
                    }
                    spes = JSON.stringify(spes)
                    products.default_spes_desc = spes;
                }
                return products;
			},
			// 购买数量加减操作
			bindChange(val) {
				this.buyNum = val;
			},
			// 商品收藏/取消
			collection() {
				let data = {
					goods_id: this.goodsInfo.id
				}
				this.$api.goodsCollection(data, res => {
					if (res.status) {
						this.isfav = !this.isfav;
						this.$common.successToShow(res.msg);
					} else {
						this.$common.errorToShow(res.msg);
					}
				})
			},
			// tab点击切换
			onClickItem(index) {
				if (this.current !== index) {
					this.current = index;
				}
			},
			// 获取商品参数信息
			getGoodsParams() {
				this.$api.goodsParams({
					id: this.goodsId
				}, res => {
					if (res.status == true) {
						this.goodsParams = res.data;
					}
				})
			},
			// 获取商品评论信息
			getGoodsComments() {
				let data = {
					page: this.goodsComments.page,
					limit: this.goodsComments.limit,
					goods_id: this.goodsId
				}

				this.goodsComments.loadStatus = 'loading';

				this.$api.goodsComment(data, res => {
					if (res.status == true) {
						let _list = res.data.list;
						let count = res.data.count;
						this.items = ['图文详情', '商品参数', '买家评论(' + count + ')']
						// 如果评论没有图片 在这块作处理否则控制台报错
						_list.forEach(item => {
							item.ctime = this.$common.timeToDate(item.ctime, true);
							if (!item.hasOwnProperty('images_url')) {
								this.$set(item, 'images_url', [])
							}
						});

						this.goodsComments.list = [...this.goodsComments.list, ..._list];
						// 根据count数量判断是否还有数据
						if (res.data.count > this.goodsComments.list.length) {
							this.goodsComments.loadStatus = 'more';
							this.goodsComments.page++;
						} else {
							this.goodsComments.loadStatus = 'noMore';
						}
					} else {
						this.$common.errorToShow(res.msg);
					}
				})
			},
			// 添加商品浏览足迹
			goodsBrowsing() {
				let data = {
					goods_id: this.goodsInfo.id
				}

				this.$api.addGoodsBrowsing(data, res => {});
			},
			// 加入购物车
			addToCart() {
				if (this.buyNum > 0) {
					let data = {
						product_id: this.product.id,
						nums: this.buyNum
					}
					this.$api.addCart(data, res => {
						if (res.status) {
							this.toclose(); // 关闭弹出层
							this.getCartNums(); // 获取购物车数量
							this.$common.successToShow(res.msg);
						} else {
							this.$common.errorToShow(res.msg);
						}
					}, res => {
						this.submitStatus = false;
					})
				}
			},
			// 立即购买
			buyNow() {
				if (this.buyNum > 0) {
					let data = {
						product_id: this.product.id,
						nums: this.buyNum,
						type: 2 // 区分加入购物车和购买
					}

					this.$api.addCart(data, res => {
						if (res.status) {
							this.toclose();
							let cartIds = res.data;
							this.$common.navigateTo('/pages/goods/place-order/index?cart_ids=' + JSON.stringify(cartIds));
						} else {
							this.$common.errorToShow(res.msg);
						}
					}, res => {
						this.submitStatus = false;
					})
				}
			},
			// 购物车页面跳转
			redirectCart() {
				uni.navigateTo({
					url: '/pages/cart/index/index'
				});
			},
			// 点击弹出框确定按钮事件处理
			clickHandle() {
				this.submitStatus = true;
				this.type === 1 ? this.addToCart() : this.buyNow();
			},
			trigger(e) {
				this.content[e.index].active = !e.item.active;
				uni.navigateTo({
					url: e.item.url
				})
			},
			// 跳转到h5分享页面
			goShare() {
				this.$refs.share.show();
			},
			closeShare() {
				this.$refs.share.close();
			},
			// 图片点击放大
			clickImg(imgs) {
				// 预览图片
				uni.previewImage({
					urls: imgs.split()
				});
			},
			//在线客服,只有手机号的，请自己替换为手机号
			showChat() {
				let _this = this;
				// #ifdef H5
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
            //获取分享URL
            getShareUrl() {
                let data = {
                    client: 2,
                    url: "/pages/share/jump",
                    type: 1,
                    page: 2,
                    params: {
                        goods_id: this.goodsInfo.id,
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
            goodsInfo: {
                handler () {
                    this.getShareUrl();
                },
                deep: true
            }
        },
        //分享
		onShareAppMessage() {
			return {
				title: this.goodsInfo.name,
				// #ifdef MP-ALIPAY
				desc: this.goodsInfo.brief,
				// #endif
				imageUrl: this.goodsInfo.album[0],
				path: this.shareUrl
			}
		}
	}
</script>

<style>
	.time-wrap {
		text-align: center;
		height: 50rpx;
		line-height: 50rpx;
		font-size: 20rpx;
		color: #FFFFFF;
		background: linear-gradient(left, #ff7058, #fb3642);
		/* background: ; */
	}
	.shop-info-wrap {
		padding: 20rpx;
		
	}
	.shop-info-wrap .shop-info {
		display: flex;
		justify-content: space-between;
		font-size: 22rpx;
		margin-bottom: 30rpx;
	}
	.shop-info-wrap .shop-info .title {
		display: flex;
		align-items: center;
	}
	.shop-info-wrap .shop-info .title .tag {
		background: #fb3642;
		padding: 4rpx 15rpx;
		color: #fff;
		font-size: 16rpx;
		margin-right: 5rpx;
	}
	.shop-info-wrap .money {
		color: #fb3642;
		font-size: 18rpx;
		margin-bottom: 16rpx;
	}
	.shop-info-wrap .money text {
		font-size: 28rpx;
	}
	.shop-info-wrap .pep {
		font-size: 20rpx;
	}
	.progress-wrap {
		margin-top: 20rpx;
		padding: 20rpx;
	}
	.progress-wrap .title{
		font-size: 20rpx;
		padding-bottom: 20rpx;
		border-bottom: 1rpx solid #dfdfdf;
	}
	.swiper {
		height: 750upx;
	}

	.goods-top {
		border-bottom: 0;
	}

	.goods-top .goods-price {
		font-size: 38upx;
	}

	.cost-price {
		font-size: 28upx !important;
		bottom: -10upx;
		color: #999;
		text-decoration: line-through;
	}

	.goods-top .cell-item-ft {
		font-size: 20upx;
		color: #666;
	}

	.goods-details {
		padding-top: 0;
	}

	.goods-details .cell-hd-title {
		width: 620upx;
	}

	.goods-details .cell-hd-title .cell-hd-title-view {
		width: 100%;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
		overflow: hidden;
	}

	.goods-details .cell-hd-title .cell-hd-title-view:last-child {
		margin-top: 10upx;
	}

	.goods-details .cell-item-ft {
		top: 24upx;
	}

	.goods-title-item .cell-item-hd {
		min-width: 60upx;
		color: #666;
		font-size: 24upx;
	}

	.goods-title-item .cell-item-bd {
		color: #333;
		font-size: 24upx;
		display: block;
	}

	.goods-title-item .cell-bd-text {
		bottom: 0;
	}

	.cell-bd-view {
		position: relative;
		overflow: hidden;
		margin-bottom: 8upx;
	}

	.cell-bd-view:last-child {
		margin-bottom: 0;
	}

	.goods-title-item-ic {
		width: 22upx;
		height: 22upx;
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		/* #ifdef MP-ALIPAY */
		background-size: 100% 100%;
		/* #endif */
	}

	.cell-bd-view .cell-bd-text {
		margin-left: 30upx;
	}

	.goods-content {
		margin-top: 26upx;
		background-color: #fff;
		padding: 26upx 0;
	}

	.goods-content-c {
		margin-top: 20upx;
	}

	.goods-parameter {
		padding: 10upx 26upx;
		min-height: 600rpx;
	}

	.goods-bottom,
	.pop-b {
		background-color: #fff;
		position: fixed;
		bottom: 0;
		height: 90upx;
		width: 100%;
		overflow: hidden;
		box-shadow: 0 0 20upx #ccc;

	}

	.goods-bottom .btn {
		height: 100%;
		width: 29%;
		float: left;
	}

	.goods-bottom-ic {
		display: inline-block;
		position: relative;
		text-align: center;
		height: 100%;
		width: 14%;
		float: left;
		font-size: 22upx;
		color: #666;
	}

	.goods-bottom-ic .icon {
		position: relative;
		top: 6upx;
		/* #ifdef MP-ALIPAY */
		background-size: 100% 100%;
		/* #endif */
	}

	.goods-bottom .btn-g {
		color: #333;
		background-color: #D9D9D9;
	}

	.goods-parameter .cell-item {
		border-bottom: none;
		margin-left: 0;
	}

	.goods-parameter .cell-item-hd {
		color: #333;
		font-size: 24upx;
	}

	.goods-parameter .cell-item-bd {
		color: #999;
	}

	.goods-parameter .cell-item-bd .cell-bd-text {
		bottom: 0;
	}

	.goods-parameter .cell-bd-text {
		margin-left: 0;
	}

	.pop-t {
		position: relative;
		padding: 30upx 26upx;
		border-bottom: 2upx solid #f3f3f3;
	}

	.goods-img {
		width: 160upx;
		height: 160upx;
		position: absolute;
		top: -20upx;
		background-color: #fff;
		border-radius: 6upx;
		border: 2upx solid #fff;

	}

	.goods-img image {
		height: 100%;
		width: 100%;
	}

	.goods-information {
		width: 420upx;
		display: inline-block;
		margin-left: 180upx;
	}

	.pop-goods-name {
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		display: block;
		font-size: 24upx;
		margin-bottom: 20upx;
	}

	.pop-goods-price {
		font-size: 30upx;
	}

	.close-btn {
		width: 40upx;
		height: 40upx;
		border-radius: 50%;
		display: inline-block;
		position: absolute;
		right: 30upx;
	}

	.close-btn image {
		width: 100%;
		height: 100%;
	}

	.pop-m {
		font-size: 28upx;
		margin-bottom: 90upx;
	}

	.goods-specs,
	.goods-number {
		padding: 26upx;
		border-top: 1px solid #f3f3f3;
	}

	.goods-specs:first-child {
		border: none;
	}

	.pop-m-title {
		margin-right: 10upx;
		color: #666;
	}

	.pop-m-bd {
		overflow: hidden;
		margin-top: 10upx;
	}

	.pop-m-item {
		display: inline-block;
		float: left;
		padding: 6upx 16upx;
		background-color: #fff;
		color: #333;
		margin-right: 16upx;
		margin-bottom: 10upx;
	}

	.selected {
		border: 2upx solid #333;
		background-color: #333;
		color: #fff;
	}

	.not-selected {
		border: 2upx solid #ccc;
	}

	.none {
		border: 2upx dashed #ccc;
		color: #888;
	}

	.pop-m-bd-in {
		display: inline-block;
	}

	.badge {
		top: 2upx;
		left: 62upx;
	}

	.goods-assess .user-head-img {
		width: 80upx;
		height: 80upx;
		border-radius: 50%;
	}

	.goods-assess .cell-item-bd {
		padding-right: 0;
	}

	.goods-assess .cell-bd-text {
		margin: 0;
	}

	.goods-assess .cell-bd-text.color-9 {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		max-width: 440upx;
	}

	.gai-body {}

	.gai-body-text {
		font-size: 26upx;
		color: #333;
		padding: 0 26upx;
		word-wrap: break-word;
	}

	.gai-body-img {
		overflow: hidden;
		padding: 20upx 26upx;
	}

	.gai-body-img image {
		width: 220upx;
		height: 220upx;
		float: left;
		margin-right: 19upx;
		margin-bottom: 18upx;
	}

	.gai-body-img image:nth-child(3n) {
		margin-right: 0;
	}

	.redstar {
		width: 24rpx;
		height: 24rpx;
		padding: 2rpx;
	}

	.mask-share-wechat {
		display: inline-block;
		background-color: #fff;
		padding: 0;
	}

	.mask-share-wechat:after {
		border: none;
	}

	.right-ball {
		position: fixed;
		right: 30upx;
		bottom: 300upx;
		z-index: 999;
		text-align: center;
		padding: 14upx 0;
		width: 80upx;
		height: 80upx;
		font-size: 24upx;
		color: #fff;
		background-color: rgba(0, 0, 0, .5);
		border-radius: 50%;
	}

	.comment-none {
		text-align: center;
		padding: 160upx 0;
	}

	.comment-none-img {
		width: 274upx;
		height: 274upx;
	}


	.price-salesvolume {
		width: 100%;
		padding: 0 0 0 26upx;
		overflow: hidden;
		color: #A5A5A5;
		background-color: rgb(252, 226, 80);
		position: relative;
	}

	.commodity-price {
		width: 224upx;
		display: inline-block;
		float: left;
	}

	.current-price {
		font-size: 40upx;
		color: #FF7159;
		display: block;
		line-height: 1.5;
	}

	.cost-price {
		font-size: 26upx;
		text-decoration: line-through;
		display: block;
	}

	.commodity-salesvolume {
		width: 240upx;
		display: inline-block;
		font-size: 22upx;
		float: left;
		padding: 16upx 0;
	}

	.commodity-salesvolume>text {
		display: block;
	}

	.commodity-time-img {
		display: block;
		width: 0;
		height: 0;
		border-width: 48upx 28upx 50upx 0;
		border-style: solid;
		border-color: transparent #FF7159 transparent transparent;
		/*透明 黄 透明 透明 */
		position: absolute;
		top: 0px;
		left: 462upx;
	}

	.commodity-time {
		display: inline-block;
		width: 260upx;
		text-align: center;
		font-size: 24upx;
		background-color: #FF7159;
		padding: 16upx 0 18upx;
		color: rgb(250, 233, 0);
	}

	.commodity-time>text {
		display: block;
	}

	.commodity-day {
		font-size: 22upx;
	}

	.commodity-day>text {
		display: inline-block;
		background-color: rgb(255, 212, 176);
		color: rgb(255, 115, 0);
		padding: 0 6upx;
		border-radius: 6upx;
	}

	.nav-back {
		width: 100%;
		height: 44px;
		/* #ifndef MP-WEIXIN */
		padding: 12px 12px 0;
		/* #endif */
		/* #ifdef MP-WEIXIN */
		padding: 26px 12px 0;
		/* #endif */

		position: fixed;
		top: 0;
		background-color: rgba(255, 255, 255, 0);
		z-index: 98;
	}

	.back-btn {
		height: 32px;
		width: 32px;
		border-radius: 50%;
		background-color: rgba(255, 255, 255, 0.8);
	}

	.back-btn .icon {
		height: 20px;
		width: 20px;
		position: relative;
		top: 50%;
		left: 46%;
		transform: translate(-50%, -50%);
	}

	.seller-content {
		background-color: #f8f8f8;
		margin: 0 13px 15px;
		padding: 10px;
		color: #6e6e6e;
		border-radius: 4px;
	}

	.seller-content-top {
		font-weight: bold;
		margin-bottom: 6px;
	}

	.seller-content-img {
		width: 20px;
		height: 20px;
		vertical-align: middle;
		margin-right: 4px;
	}

	.service {
		width: 80rpx;
		height: 80rpx;
		background-color: #fff;
		border-radius: 50%;
		position: fixed;
		left: 30rpx;
		bottom: 120rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		box-shadow: 0 0 10rpx #ccc;
		padding: 0;
	}

	.service .icon {
		width: 60rpx;
		height: 60rpx;
	}

	/* #ifdef MP-WEIXIN */
	.weiContact {
		background-color: #fff;
		border: none;
	}

	.weiContact::after {
		border: none;
	}

	.weiContact>view {
		position: absolute;
		top: 45rpx;
		left: 50%;
		transform: translateX(-50%);
	}

	/* #endif */
	
	.progress-step {
		display: flex;
		padding: 10rpx 0;
	}
	.progress-step .step {
		flex: 1;
		text-align: center;
		font-size: 20rpx;
	}
	.progress-step .step-top {
		display: flex;
		align-items: center;
	}
	.progress-step .step-top text {
		display: inline-block;
		width: 32rpx;
		height: 32rpx;
		border-radius: 50%;
		background: #999;
		flex: 0 0 32rpx;
		text-align: center;
		line-height: 32rpx;
		color: #fff;
		font-size: 24rpx;
	}
	.step1 .step-top image:first-child{
		visibility: hidden;
	}
	.step3 .step-top image:last-child {
		visibility: hidden;
	}
	.progress-step .step-desc {
		color: #bdbdbd;
	}
	.progress-step .step1.step,.progress-step .step1.step .step-desc {
		color: #e66650;
	}
	.progress-step .step1.step text{
		background: #e66650;
	}
</style>
