<template>
	<!-- 单图 -->
	<view class="ad jshop-imgsingle" v-if="jdata.params.list && jdata.params.list.length > 0">
		<view class="" v-for="item in jdata.params.list" :key="item.id">
			<!-- #ifdef MP-WEIXIN -->
			<view @click="showSliderInfo2" :data-type="item.linkType" :data-val="item.linkValue">
				<image class="ad-img" :src="item.image" mode="widthFix" ></image>
			</view>
			<!-- #endif -->
			<!-- #ifndef MP-WEIXIN -->
			<image class="ad-img" :src="item.image" mode="widthFix" @click="showSliderInfo(item.linkType, item.linkValue)"></image>
			<!-- #endif -->
			<view class="imgup-btn" v-if="item.buttonText != ''" @click="showSliderInfo(item.linkType, item.linkValue)">
				<button class="btn btn-fillet" :style="{background:item.buttonColor,color:item.textColor}">{{item.buttonText}}</button>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "jshopimgsingle",
		props: {
			jdata: {
				// type: Object,
				required: true,
			}
		},
		computed: {
			count() {  
				// console.log(jdata)
				return (this.jdata.params.list.length > 0)
			}
			
		},
		methods: {
			showSliderInfo(type, val) {
				console.log(type);
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
						} else if(val.indexOf('/pages/coupon/coupon')>-1){
							var id = val.replace('/pages/coupon/coupon?id=',"");
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
				// let ins = encodeURIComponent('id='+id);
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
			// #ifdef MP-WEIXIN
			showSliderInfo2:function(e){
				let type = e.currentTarget.dataset.type;
				let val = e.currentTarget.dataset.val;
				console.log(type);
				console.log(val)
				console.log(type);
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
						} else if(val.indexOf('/pages/coupon/coupon')>-1){
							var id = val.replace('/pages/coupon/coupon?id=',"");
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
			}
			// #endif
		},
	}
</script>

<style>
	/* .ad {
	width: 100%;
	overflow: hidden;
}
.ad-img{
	width: 100%;
	float: left;
	margin-bottom: 20upx;
}
.ad-img:last-child{
	margin-bottom: 0;
} */
	.jshop-imgsingle.ad {
		width: 100%;
		overflow: hidden;
		position: relative;
	}

	.jshop-imgsingle .ad-img {
		width: 100%;
		float: left;
		position: relative;
		z-index: 667;
		/* margin-bottom: 20upx; */
	}

	.jshop-imgsingle .ad-img:last-child {
		margin-bottom: 0;
	}

	.jshop-imgsingle .imgup-btn {
		position: absolute;
		z-index: 668;
		bottom: 80upx;
		left: 40upx;
	}

	.jshop-imgsingle .imgup-btn .btn {
		line-height: 2;
		font-size: 28upx;
		padding: 0 50upx;
	}
</style>
