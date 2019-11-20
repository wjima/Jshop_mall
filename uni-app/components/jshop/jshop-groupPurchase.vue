<template>
	<!-- 团购秒杀 -->
	<view class="img-list bottom-cell-group group-buying" v-if="jdata.params.list && count">
		<view class='cell-item right-img'>
			<view class='cell-item-hd group-title'>
				{{jdata.params.title}}
				<!-- <view class='cell-hd-title'></view> -->
			</view>
		</view>
		<view class='swiper-grids'>
			<scroll-view class='swiper-list' scroll-x="true">
				<view class="img-list-item" v-if="item.goods !== 'undefined' && item.goods" v-for="(item, key) in jdata.params.list"
				 :key="key">
					<image class="img-list-item-l medium-img have-none" :src="item.goods.image_url" mode='aspectFill' @click="groupDetail(item.goods.id, item.goods.group_id)"></image>
					<view class="img-list-item-r medium-right">
						<view class="goods-name list-goods-name" @click="groupDetail(item.goods.id, item.goods.group_id)">{{item.goods.name}}</view>
						<view class="goods-item-c">
							<view class="goods-price red-price">￥{{item.goods.product.price}}</view>
							<view class="goods-buy">
								<view class="goods-salesvolume red-price" v-if="(item.goods.lasttime != '已经结束' || item.goods.lasttime != '即将开始') && item.goods.lasttime">剩余：<uni-countdown
									 :show-day="false" :hour="item.goods.lasttime.hour" :minute="item.goods.lasttime.minute" :second="item.goods.lasttime.second"></uni-countdown>
								</view>
								<view class="goods-salesvolume red-price" v-if="item.goods.lasttime == '已经结束'">已结束</view>
								<view class="goods-salesvolume red-price" v-if="item.goods.lasttime == '即将开始'">即将开始</view>
								<image class="goods-cart" src="/static/image/ic-car.png" @click="groupDetail(item.goods.id, item.goods.group_id)"></image>
							</view>
						</view>
					</view>
				</view>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import uniCountdown from "@/components/uni-countdown/uni-countdown.vue"
	import {
		goods
	} from '@/config/mixins.js'
	export default {
		mixins: [goods],
		components: {
			uniCountdown
		},
		name: "jshopgrouppurchase",
		props: {
			jdata: {
				// type: Array,
				required: false,
			}
		},
		computed: {
			count() {  
				return (this.jdata.params.list.length > 0)
			}
		},
		methods: {
			showSliderInfo(type, val) {
				if (type == 1) {
					if (val.indexOf('http') != -1) {
						// #ifdef H5 
						window.location.href = val
						// #endif
					} else {
						// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE || MP
						if (val == '/pages/classify/classify' || val == '/pages/cart/index/index' || val == '/pages/member/index/index') {
							uni.switchTab({
								url: val
							});
							return;
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
					this.$common.navigateTo('/pages/article/index?id=' + val +'&id_type=1')
				} else if (type == 4) {
					// 文章列表
					this.$common.navigateTo('/pages/article/list?cid=' + val)
				} else if (type == 5) {
					//智能表单 
					this.$common.navigateTo('/pages/form/detail/form?id=' + val)
				}
			},
			//跳转到商品详情页面
			// goodsDetail: function(id) {
			// 	let url = '/pages/goods/index/index?id=' + id;
			// 	this.$common.navigateTo(url);
			// },
		},
	}
</script>

<style>
	.img-list,
	.img-grids {
		background-color: #fff;
	}

	.cell-item {
		border: none;
	}

	.group-buying .img-list-item {
		min-height: 236upx;
		padding: 20upx;
		margin-left: 26upx;
		margin-bottom: 26upx;
		display: inline-table;
		background-color: #f9f9f9;
	}

	.swiper-grids .img-list-item:last-child {
		margin-right: 26upx;
	}

	/* .group-buying .goods-name{
	min-height: 74upx;
} */
	.group-buying .group-title {
		width: 100%;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
</style>
