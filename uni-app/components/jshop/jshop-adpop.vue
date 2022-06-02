<template>
	<!-- 单图 -->
	<view class="jshop-adpop" v-if="jdata.params.list && jdata.params.list.length > 0">
		<view class="adpop" v-if="closeAd">
			<view class="adpop-c">
				<view class="adpop-img">
					<!-- #ifdef MP-WEIXIN -->
					<view @click="showSliderInfo2" :data-type="jdata.params.list[0].linkType" :data-val="jdata.params.list[0].linkValue">
						<image class="ad-img" :src="jdata.params.list[0].image" mode="widthFix" ></image>
					</view>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<image class="ad-img" :src="jdata.params.list[0].image" mode="widthFix" @click="showSliderInfo(jdata.params.list[0].linkType, jdata.params.list[0].linkValue)"></image>
					<!-- #endif -->
				</view>
				<image class="close-btn" src="/static/image/close-pink.png" mode="" @click="closePop"></image>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "jshopadpop",
		props: {
			jdata: {
				// type: Object,
				required: true,
			}
		},
		data(){
			return{
				closeAd: true
			}
		},
		computed: {
			count() {  
				// console.log(jdata)
				return (this.jdata.params.list.length > 0)
			}
			
		},
		methods: {
			closePop(){
				this.closeAd = false
			},
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
.adpop{
	position: fixed;
	background: rgba(0,0,0,.5);
	width: 100%;
	height: 100vh;
	z-index: 999;
	top: 0;
	left: 0;
}
.adpop-c{
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%,-50%);
	width: 70%;
	text-align: center;
}
.adpop-img{
	width: 100%;
	max-height: 1000rpx;
	margin-bottom: 50rpx;
}
.ad-img{
	width: 100%;
	max-height: 1000rpx;
}
.close-btn{
	width: 60rpx;
	height: 60rpx;
}
</style>
