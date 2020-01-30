<template>
	<view class="classify">
		<!-- 二级小图 -->
		<view class="goods-box" v-if="cate_style == 3">
			<view class="goods-list">
				<scroll-view scroll-y="true">
					<view class="goods-li" :class="{ active: index == ins }" @click="active(index)" v-for="(tab, index) in beans" :key="index">
						<view class="shelectedZhu"></view>
						{{ tab.name }}
					</view>
				</scroll-view>
			</view>
			<view class="goods-grid">
				<scroll-view class="goods-content" scroll-y="true">
					<view class="goods-banner" v-if="advert.tpl1_class_banner1">
						<image mode="widthFix" v-for="item in advert.tpl1_class_banner1" :key="item.id" :src="item.img" @click="showSliderInfo(item.type, item.val)" />
					</view>
					<view class="goods-item">
						<view class="goods-item-box" v-if="isChild">
							<view class="goods-items" v-for="(item, index) in beans[ins].child" :key="index" @click="goClass(item.id)">
								<image class="goods-item-img" :src="item.image_url" alt="" mode="aspectFill" />
								<view class="goods-item-name">{{ item.name }}</view>
							</view>
						</view>
					</view>
				</scroll-view>
			</view>
		</view>

		<!-- 一级小图 -->
		<view class="goods-box level1-s" v-if="cate_style == 2">
			<view class="goods-grid">
				<scroll-view class="goods-content" scroll-y="true">
					<view class="goods-item">
						<view class="goods-item-box">
							<view class="goods-items" v-for="(item, index) in beans" :key="index" @click="goClass(item.id)">
								<image class="goods-item-img" :src="item.image_url" alt="" mode="aspectFill" />
								<view class="goods-item-name">{{ item.name }}</view>
							</view>
						</view>
					</view>
				</scroll-view>
			</view>
		</view>

		<!-- 一级大图 -->
		<view class="goods-box level1-b" v-if="cate_style == 1">
			<view class="goods-grid">
				<scroll-view class="goods-content" scroll-y="true">
					<view class="goods-item">
						<view class="goods-item-box">
							<view class="goods-items" v-for="(item, index) in beans" :key="index" @click="goClass(item.id)">
								<image class="goods-item-img" :src="item.image_url" alt="" mode="aspectFill" />
								<view class="goods-item-name">{{ item.name }}</view>
							</view>
						</view>
					</view>
				</scroll-view>
			</view>
		</view>
	</view>
</template>

<script>
var _this;
import { mapGetters } from 'vuex';
import { goods } from '@/config/mixins.js';
export default {
	mixins: [goods],
	data() {
		return {
			dataList: null,
			ins: 0,
			beans: [],
			advert: {},
			isChild: false
		};
	},
	computed: {
		cate_style() {
			return this.$store.state.config.cate_style ? this.$store.state.config.cate_style : 3;
		}
	},
	methods: {
		//切换样式 请求分类数据
		active(index) {
			this.ins = index;
			this.isChild = this.beans[index].hasOwnProperty('child');
		},
		categories() {
			this.$api.categories({}, res => {
				if (res.status) {
					for (var i = 0; i < res.data.length; i++) {
						if (i == 0) {
							res.data[i].active = true;
						}
					}
					this.beans = res.data;
					this.isChild = this.beans[0].hasOwnProperty('child');
				}
			});
		},
		goClass(cat_id) {
			uni.navigateTo({
				url: '/pages/classify/index?id=' + cat_id
			});
		},
		getBanner() {
			this.$api.advert(
				{
					codes: 'tpl1_class_banner1'
				},
				res => {
					this.advert = res.data.list;
				}
			);
		},
		// 广告点击查看详情
		showSliderInfo(type, val) {
			if (type == 1) {
				if (val.indexOf('http') != -1) {
					// #ifdef H5 
					window.location.href = val
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
				this.goodsDetail(val);
			} else if (type == 3) {
				// 文章详情
				this.$common.navigateTo('/pages/article/index?id=' + val + '&id_type=1');
			} else if (type == 4) {
				// 文章列表
				this.$common.navigateTo('/pages/article/list?cid=' + val);
			}
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
		}
	},
	onLoad() {
		this.categories();
		this.getBanner();
	}
};
</script>

<style>
.classify {
	/*  #ifdef  H5  */
	height: calc(100vh - 94px);
	/*  #endif  */
	/*  #ifndef  H5  */
	height: 100vh;
	/*  #endif  */
}
.goods-box {
	height: 100%;
	overflow: hidden;
}
.goods-list {
	overflow: auto;
	height: 100%;
	width: 160upx;
	float: left;
	display: inline-block;
	background-color: #f8f8f8;
}
.goods-li {
	font-size: 24upx;
	color: #666;
	height: 100upx;
	line-height: 100upx;
	text-align: center;
	position: relative;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
.goods-li.active {
	background-color: #fff;
}
.shelectedZhu {
	height: 56upx;
	width: 8upx;
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
}
.goods-li.active .shelectedZhu {
	background-color: #333;
}
.goods-content {
	width: 590upx;
	display: inline-block;
	float: left;
	padding: 20upx;
	box-sizing: border-box;
}
.goods-grid {
	height: 100%;
	overflow: auto;
	background-color: #fff;
}
.goods-banner {
	width: 100%;
	margin-bottom: 20upx;
}
.goods-banner image {
	width: 100%;
	height: 100%;
}
.goods-item {
}
.goods-item-box {
	overflow: hidden;
}
.goods-items {
	width: 170upx;
	margin-right: 20upx;
	margin-bottom: 20upx;
	display: inline-block;
}
.goods-items:nth-child(3n) {
	margin-right: 0;
}
.goods-item-img {
	width: 170upx;
	height: 170upx;
}
.goods-item-name {
	text-align: center;
	color: #666;
	font-size: 26upx;
	height: 1rem;
	overflow: hidden;
}
.level1-s .goods-content,
.level1-b .goods-content {
	width: 100%;
}
.level1-s .goods-items {
	width: 222upx;
}
.level1-s .goods-item-img {
	width: 222upx;
	height: 222upx;
}
.level1-b .goods-items {
	width: 100%;
}
.level1-b .goods-item-img {
	width: 100%;
	height: 222upx;
}
</style>
