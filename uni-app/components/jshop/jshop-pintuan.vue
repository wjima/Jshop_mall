<template>
	<!-- 拼团 -->
	<view class="img-list bottom-cell-group group-buying" v-if="jdata.params.list && count">
		<view class='cell-item right-img'>
			<view class='cell-item-hd group-title'>
				{{jdata.params.title}}
			</view>
		</view>
		<view class='swiper-grids' >
			<scroll-view class='swiper-list' scroll-x="true">
				<view class="img-list-item" v-if="item.goods_id !== 'undefined' && item.goods_id" v-for="(item, key) in jdata.params.list" :key="key">
					<image class="img-list-item-l medium-img have-none" :src="item.goods_image" mode='aspectFill' @click="pintuanDetail(item.goods_id)"></image>
					<view class="img-list-item-r medium-right">
						<view class="goods-name list-goods-name" @click="pintuanDetail(item.goods_id)">{{item.goods_name}}</view>
						<view class="goods-item-c">
							<view class="goods-price red-price">￥{{item.pintuan_price}}</view>
							<view class="goods-buy">
								<view class="goods-salesvolume red-price" v-if="(item.pintuan_start_status == 1) && item.lasttime">剩余：<uni-countdown :day="item.lasttime.day" :hour="item.lasttime.hour" :minute="item.lasttime.minute" :second="item.lasttime.second"></uni-countdown></view>
								<view class="goods-salesvolume red-price" v-if="item.pintuan_start_status == 3">已结束</view>
								<view class="goods-salesvolume red-price" v-if="item.pintuan_start_status == 2">即将开团</view>
								
								<image class="goods-cart" src="/static/image/ic-car.png" @click="pintuanDetail(item.goods_id)"></image>
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
import {goods} from '@/config/mixins.js'
export default {
	mixins: [goods],
	components:{uniCountdown},
	name: "jshoppintuan",
	props: {
		jdata:{
			// type: Array,
			required: false,
		}
	},
	computed: {  
		count() {  
			// console.log(this.jdata.params.list);
			return (this.jdata.params.list.length > 0)
		}
	},
	methods: {
	},
}
</script>

<style>
.img-list, .img-grids {
	background-color: #fff;
}
.cell-item{
	border: none;
}
.group-buying .img-list-item{
	min-height: 236upx;
	padding: 20upx;
	margin-left: 26upx;
	margin-bottom: 26upx;
	display: inline-table;
	background-color: #f9f9f9;
}
.swiper-grids .img-list-item:last-child{
	margin-right: 26upx;
}

/* .group-buying .goods-name{
	min-height: 74upx;
} */
.group-buying .group-title{
	width: 100%;
	overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
