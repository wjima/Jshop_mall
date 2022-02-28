<template>
	<view class="imgnavbar">
		<view class="imgnavbar-list" v-if="jdata.params.limit == '3' ||jdata.params.limit == '4' ||jdata.params.limit == '5'"
		 v-bind:class="'row'+jdata.params.limit">
			<view class="imgnavbar-item" ref="imgwitem" v-for="(item, index) in jdata.params.list" :key="index">
				<image class="imgnavbar-item-img" :src="item.image" mode="aspectFill" @click="showSliderInfo(item.linkType, item.linkValue)"></image>
				<view class="imgnavbar-item-text">{{item.text}}</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "jshopnavbar",
		props: {
			jdata: {
				// type: Object,
				required: true,
			}
		},
		data() {
			return {
				height: '',
				height1: ''
			}
		},
		onLoad() {

		},
		mounted() {

		},
		methods: {
			showSliderInfo(type, val) {
				if (!val) {
					return;
				}
				if (type == 1) {
					if (val.indexOf('http') != -1) {
						this.$common.navigateTo('/pages/webview/index?src=' + encodeURIComponent(val));
						return;
						/* uni.navigateTo({
							url: '/pages/webview/index.vue?src=' + val
						}) */
					} else {
						// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE || MP
						if (val == '/pages/index/index' || val == '/pages/classify/classify' || val == '/pages/cart/index/index' || val == '/pages/member/index/index') {
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
					this.$common.navigateTo('/pages/article/index?id=' + val + '&id_type=1')
				} else if (type == 4) {
					// console.log("11")
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
		}
	}
</script>

<style>
	.imgnavbar {
		width: 100%;
		background-color: #fff;
		/* margin-bottom: 20rpx; */
	}

	.imgnavbar-list {
		overflow: hidden;
		padding: 24upx 0 0;
	}

	/* 堆积两列 */
	.imgnavbar-list .imgnavbar-item {
		height: auto;
		float: left;
		padding: 0upx 10upx;
		margin-bottom: 20upx;
		text-align: center;
	}

	.imgnavbar-list .imgnavbar-item image {
		width: 90upx;
		height: 90upx;
		margin-bottom: 6upx;
	}

	.imgnavbar-item-text {
		font-size: 26upx;
		color: #666;
		width: 100%;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.imgnavbar-list.row3 .imgnavbar-item {
		width: 33.3%;
	}

	.imgnavbar-list.row4 .imgnavbar-item {
		width: 25%;
	}

	.imgnavbar-list.row5 .imgnavbar-item {
		width: 20%;
	}

	.imgnavbar-list.row5 .imgnavbar-item .imgnavbar-item-text {
		font-size: 24upx;
	}
</style>
