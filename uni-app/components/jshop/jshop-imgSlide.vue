<template>
	<view class='swiper' v-if="jdata.params.list && count">
		<swiper class="swiper-c" :indicator-dots="swiper.indicatorDots" :autoplay="swiper.autoplay" :interval="jdata.params.duration"
		 :duration="swiper.duration">
			<swiper-item class="have-none" v-for="(item, index) in jdata.params.list" :key="index">
				<image class='' :src="item.image" @click="showSliderInfo(item.linkType, item.linkValue)" mode="aspectFill"></image>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	export default {
		name: "jshopimgSlide",
		props: {
			jdata: {
				// type: Object,
				required: true,
			}
		},
		data() {
			return {
				swiper: {
					indicatorDots: true,
					autoplay: true,
					// interval: 2000,
					duration: 500,
				},
			};
		},
		computed: {
			count() {  
				return (this.jdata.params.list.length > 0)
			}
		},
		created() {},
		watch: {},
		methods: {
			// 广告点击查看详情
			showSliderInfo(type, val) {
				if (!val) {
					return;
				}
				if (type == 1) {
					if (val.indexOf('http') != -1) {
						// #ifdef H5 
						window.location.href = val
						// #endif
						// #ifndef H5
						uni.navigateTo({
							url: '/pages/webview/index.vue?src=' + val
						})
						// #endif
					} else {
						// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE || MP
						if (val == '/pages/index/index' || val == '/pages/classify/classify' || val == '/pages/cart/index/index' || val == '/pages/member/index/index') {
							uni.switchTab({
								url: val
							});
							return;
						} else if (val.indexOf('/pages/coupon/coupon') > -1) {
							var id = val.replace('/pages/coupon/coupon?id=', "");
							this.receiveCoupon(id)
						} else {
							this.$common.navigateTo(val);
							return;
						}
						// #endif
					}
				} else if (type == 2) {
					// 商品详情
					this.goodsDetail(val)
				} else if (type == 3) {
					// 文章详情
					this.$common.navigateTo('/pages/article/index?id=' + val + '&id_type=1')
				} else if (type == 4) {
					// 文章列表
					this.$common.navigateTo('/pages/article/list?cid=' + val)
				} else if (type == 5) {
					//智能表单 
					this.$common.navigateTo('/pages/form/detail/form?id=' + val)
				}
			},
			//跳转到商品详情页面
			goodsDetail: function(id) {
				let url = '/pages/goods/index/index?id=' + id;
				this.$common.navigateTo(url);
			},
			// 用户领取优惠券
			receiveCoupon(couponId) {
				let data = {
					promotion_id: couponId
				}
				this.$api.getCoupon(data, res => {
					if (res.status) {
						this.$common.successToShow(res.msg)
					} else {
						this.$common.errorToShow(res.msg)
					}
				})
			},
		}
	}
</script>

<style>
	.swiper {
		height: 340upx;
	}
</style>
