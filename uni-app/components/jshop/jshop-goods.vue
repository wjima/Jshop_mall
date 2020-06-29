<template>
	<view class="index-goods">
		<!-- 列表平铺两列三列 -->
		<view class='img-grids bottom-cell-group' 
		v-if="jdata.params.column == '2' && jdata.params.display == 'list' || jdata.params.column == '3' && jdata.params.display == 'list'"
		v-bind:class="'column'+jdata.params.column">
			<view class='cell-item right-img' v-if="jdata.params.title != ''">
				<view class='cell-item-hd'>
					<view class='cell-hd-title'>{{jdata.params.title}}</view>
				</view>
				<view class='cell-item-bd'>
				</view>
				<view class='cell-item-ft' v-if="jdata.params.lookMore == 'true'">
					
					<text class='cell-ft-text' @click="goodsList({cat_id: jdata.params.classifyId,brand_id:jdata.params.brandId})">查看更多</text>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<!-- <view class='img-grids'> -->
				<view class="" v-if="count">
					<view class="img-grids-item" v-for="item in jdata.params.list" :key="item.id" @click="goodsDetail(item.id)">
						<image
							class="img-grids-item-t have-none"
							:src="item.image_url"
							mode='aspectFill'
						></image>
						<view class="img-grids-item-b">
							<view class="goods-name grids-goods-name">
								{{item.name}}
							</view>
							<view class="goods-item-c">
								<view class="goods-price red-price">￥{{item.price}}</view>
							</view>
						</view>
					</view>
				</view>
		
				<view v-else-if="!count && !jdata.params.listAjax">
					<view class='img-grids-item'>
						<image class='img-grids-item-t have-none' src='' mode=''></image>
						<view class='img-grids-item-b'>
							<view class='goods-name grids-goods-name have-none'></view>
							<view class='goods-item-c'>
								<view class='goods-price red-price have-none'></view>
							</view>
						</view>
					</view>
					<view class='img-grids-item'>
						<image class='img-grids-item-t have-none' src='' mode=''></image>
						<view class='img-grids-item-b'>
							<view class='goods-name grids-goods-name have-none'></view>
							<view class='goods-item-c'>
								<view class='goods-price red-price have-none'></view>
							</view>
						</view>
					</view>
					<view class='img-grids-item'>
						<image class='img-grids-item-t have-none' src='' mode=''></image>
						<view class='img-grids-item-b'>
							<view class='goods-name grids-goods-name have-none'></view>
							<view class='goods-item-c'>
								<view class='goods-price red-price have-none'></view>
							</view>
						</view>
					</view>
				</view>
				<!-- <view v-else="">
					<scroll-view class='swiper-list' scroll-x="true"></scroll-view>
				</view> -->
			<!-- </view> -->
		</view>
		
		<!-- 列表平铺单列 -->
		<view class="img-list bottom-cell-group" 
		v-if="jdata.params.column == '1' && jdata.params.display == 'list'" >
			<view class='cell-item right-img' v-if="jdata.params.title != ''">
				<view class='cell-item-hd'>
					<view class='cell-hd-title'>{{jdata.params.title}}</view>
				</view>
				<view class='cell-item-bd'>
				</view>
				<view class='cell-item-ft' v-if="jdata.params.lookMore == 'true'">
					
					<text class='cell-ft-text' @click="goodsList({cat_id: jdata.params.classifyId,brand_id:jdata.params.brandId})">查看更多</text>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view v-if="count">
				<view class="img-list-item" v-for="(item, index) in jdata.params.list" :key="index" @click="goodsDetail(item.id)">
					<image class="img-list-item-l have-none" :src="item.image_url" mode='aspectFill'></image>
					<view class="img-list-item-r">
						<view class="goods-name list-goods-name">
							{{item.name}}
						</view>
						<view class="goods-item-c">
							<view class="goods-price red-price">￥{{item.price}}</view>
							<view class="goods-buy">
								<view class="goods-salesvolume" v-if="item.comments_count > 0">{{item.comments_count}}条评论</view>
								<view class="goods-salesvolume" v-else-if="item.comments_count <= 0">暂无评论</view>
								<image class="goods-cart" src="/static/image/ic-car.png"></image>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="order-none" v-else>
				<image class="order-none-img" src="/static/image/order.png" mode=""></image>
			</view>
		</view>
		
		<!-- 横向滚动 -->
		<view class='img-grids bottom-cell-group' 
		v-if="jdata.params.column == '2' && jdata.params.display == 'slide' || jdata.params.column == '3' && jdata.params.display == 'slide'"
		v-bind:class="'slide'+jdata.params.column">
			<view class='cell-item right-img' v-if="jdata.params.title != ''">
				<view class='cell-item-hd'>
					<view class='cell-hd-title'>{{jdata.params.title}}</view>
				</view>
				<view class='cell-item-bd'>
				</view>
				<view class='cell-item-ft' v-if="jdata.params.lookMore == 'true'">
					
					<text class='cell-ft-text' @click="goodsList({cat_id: jdata.params.classifyId,brand_id:jdata.params.brandId})">查看更多</text>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</view>
			<view class='swiper-grids'>
				<scroll-view class='swiper-list' scroll-x="true" v-if="count">
					<view class='img-grids-item' v-for="item in jdata.params.list" :key="item.id" @click="goodsDetail(item.id)">
						<image class='img-grids-item-t have-none' :src='item.image_url' mode='aspectFill'></image>
						<view class='img-grids-item-b'>
							<!-- <view class='goods-name grids-goods-name' >
								{{ item.name|substr }}
							</view> -->
							<view class='grids-goods-name' >
								{{ item.name|substr }}
							</view>
							<view class='goods-item-c'>
								<view class='goods-price red-price'>￥{{ item.price }}</view>
							</view>
						</view>
					</view>
				</scroll-view>
				<view v-else-if="!goodsListOfHotAjax && !goodsListOfHot.length">
					<scroll-view class='swiper-list' scroll-x="true">
						<view class='img-grids-item'>
							<image class='img-grids-item-t have-none' src='' mode='aspectFill'></image>
							<view class='img-grids-item-b'>
								<view class='goods-name grids-goods-name have-none'></view>
								<view class='goods-item-c'>
									<view class='goods-price red-price have-none'></view>
								</view>
							</view>
						</view>
						<view class='img-grids-item'>
							<image class='img-grids-item-t have-none' src='' mode='aspectFill'></image>
							<view class='img-grids-item-b'>
								<view class='goods-name grids-goods-name have-none'></view>
								<view class='goods-item-c'>
									<view class='goods-price red-price have-none'></view>
								</view>
							</view>
						</view>
						<view class='img-grids-item'>
							<image class='img-grids-item-t have-none' src='' mode=''></image>
							<view class='img-grids-item-b'>
								<view class='goods-name grids-goods-name have-none'></view>
								<view class='goods-item-c'>
									<view class='goods-price red-price have-none'></view>
								</view>
							</view>
						</view>
					</scroll-view>
				</view>
				<view v-else="">
					<scroll-view class='swiper-list' scroll-x="true"></scroll-view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
import {goods} from '@/config/mixins.js'
export default {
	filters:{
		substr(val) {
			if (val.length == 0 || val == undefined) {
				return false;
			} else if (val.length > 13) {
				return val.substring(0, 13) + "...";
			} else {
				return val;
			}
		}
	},
	mixins: [goods],
	name: "jshopgoods",
	props: {
		jdata:{
			// type: Array,
			required: true,
		}
	},
	computed: {
		count() {  
			return (this.jdata.params.list.length > 0)
		}
	},
	methods: {
		//跳转到商品详情页面
		goodsDetail: function(id) {
			let url = '/pages/goods/index/index?id=' + id;
			this.$common.navigateTo(url);
		},
		
	},
}
</script>

<style>
.cell-item {
	border: none;
	/* padding-bottom: 0; */
}
.cell-ft-text {
	font-size: 22upx;
	color: #999;
}
.img-grids,.img-list{
	/* margin-top: 20upx; */
	background-color: #fff;
}
.img-grids-item{
	display: inline-table;
	margin-top: 0;
	margin-bottom: 14upx;
}
.column3 .img-grids-item{
	width: 230upx;
	height: 364rpx !important;
	margin: 15upx;
	margin-right: 0;
	margin-top: 0;
	margin-bottom: 6upx;
}
.column3 .img-grids-item:nth-child(3n){
	margin-right: 15upx;
}
.column3 .img-grids-item-t{
	width: 230upx;
	height: 230upx;
}
.column3 .grids-goods-name{
	font-size: 24upx;
	/* height: 68upx; */
	/* min-height: 54rpx; */
}
.column3 .img-grids-item-b{
	padding: 0 8upx 8upx;
}
.column3 .goods-price{
	font-size: 26upx;
}
.slide3 .img-grids-item{
	width: 200upx;
}
.slide3 .img-grids-item-t{
	width: 200upx;
	height: 200upx;
}
.slide3 .grids-goods-name{
	font-size: 24rpx;
	/* height: 66rpx; */
	height: 50rpx;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}
.index-goods .img-grids-item{
	display: inline-block;
	margin-top: 0;
}
.index-goods .img-list-item{
	padding: 0upx 26upx;
	margin-bottom: 14upx;
}
.index-goods .img-list{
	padding-bottom: 10upx;
}
</style>
