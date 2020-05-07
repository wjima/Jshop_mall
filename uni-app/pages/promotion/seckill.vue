<template>
	<view class="content">
		<view class="banner">
			<image class="banner-img" src="/static/image/1222.png" mode="widthFix"></image>
		</view>
		<view class="">
			<view class="img-list">
				<view v-if="list.length > 0">
					<view class="img-list-item" v-for="(item, index) in list" :key="index" @click="groupDetail(item.goods_id, item.id)">
						<view class="img-list-item-t">
							
							<view class="">
								<text class="fsz26 color-6">还剩：</text><uni-countdown
									:backgroundColor="'#ff7159'" :color="'#fff'" :day="item.goods.lasttime.day" :hour="item.goods.lasttime.hour" :minute="item.goods.lasttime.minute" :second="item.goods.lasttime.second"></uni-countdown>
							</view>
							
						</view>
						<view class="img-list-item-b">
							<image class="img-list-item-l little-img have-none" :src="item.goods.image_url" mode="aspectFill"></image>
							<view class="img-list-item-r little-right">
								<view class="goods-name list-goods-name">{{ item.goods.name||'' }}</view>
								<view class="">
									<view class="goods-price red-price fsz34"><text class="fsz24 color-3">限时价</text>￥{{ item.goods.price||'0.00' }}</view>
								</view>
								<view class="goods-item-c">
									<view class="goods-item-c-tip">
										仅剩{{item.goods.product.stock||'0'}}件
									</view>
									<button class="btn" v-if="item.goods.product.stock > 0">马上抢</button>
									<button class="btn btn-g" v-else>已售罄</button>
								</view>
							</view>
						</view>
					</view>
				</view>
				<uni-load-more :status="loadStatus"></uni-load-more>
				<!-- <view class="order-none" v-else><image class="order-none-img" src="/static/image/order.png" mode=""></image></view> -->
			</view>
		</view>
	</view>
</template>

<script>
import uniCountdown from "@/components/uni-countdown/uni-countdown.vue"
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
import {
	goods
} from '@/config/mixins.js'
export default {
	components: {
		uniCountdown,uniLoadMore
	},
	mixins: [goods],
	data() {
		return {
			page: 1,
			limit: 10,
			loadStatus: 'more',
			list:[]
		}
	},
	onReachBottom() {
		if (this.loadStatus === 'more') {
			this.getList();
		}
	},
	onLoad() {
		this.getList()
	},
	methods:{
		getList(){
			let data = {
				page: this.page,
				limit: this.limit,
				type:4
			};
			this.loadStatus = 'loading';
			
			this.$api.getGroup(data,res=>{
				if (res.status) {
					const _list = res.data.list;
					console.log(_list);
					this.list = [...this.list, ..._list];
					if (res.data.count > this.list.length) {
						this.loadStatus = 'more';
						this.page++;
					} else {
						this.loadStatus = 'noMore';
					}
				} else {
					// 接口请求出错了
					this.$common.errorToShow(res.msg);
				}
			})
		}
	}
}
</script>

<style>
.banner{
	width: 100%;
	overflow: hidden;
}
.banner-img{
	width: 100%;
	float: left;
}
.little-right .list-goods-name{
	width: 100%;
	-webkit-line-clamp: 1;
}
.little-right .goods-price{
	float: none;
	max-width: 300rpx;
}
.img-list-item .goods-item-c {
	bottom: 0;
	display: flex;
	justify-content: space-between;
}
.img-list-item .goods-item-c .btn{
	font-size: 24rpx;
	background-color: #FF7159;
	color: #fff;
	margin: 0;
	/* padding: 0 14rpx; */
	line-height: 1.6;
	display: flex;
	justify-content: center;
	align-items: center;
	border-radius: 4rpx;
	height: 46rpx;
	transform: scale(.9);
}
.img-list-item .goods-item-c .btn.btn-g{
	font-size: 24rpx;
	background-color: #eee;
	border-color: #eee;
	color: #fff;
	margin: 0;
	/* padding: 0 14rpx; */
	line-height: 1.6;
	display: flex;
	justify-content: center;
	align-items: center;
	border-radius: 4rpx;
	height: 46rpx;
	transform: scale(.9);
}
.goods-item-c-tip{
	border-radius: 50rpx;
	border: 2rpx solid #ff7159;
	font-size: 24rpx;
	color: #FF7159;
	/* padding: 0 20rpx; */
	width: 200rpx;
	text-align: center;
	height: 40rpx;
	line-height: 36rpx;
}
.img-list-item{
	padding-top: 14rpx;
}
.img-list-item-t{
	margin-bottom: 10rpx;
	display: flex;
	justify-content: space-between;
}

.img-list-item-b{
	overflow: hidden;
}
.img-list-item-t-tip{
	border-radius: 6rpx;
	border: 2rpx solid #ff7159;
	font-size: 24rpx;
	color: #FF7159;
	padding: 0 20rpx;
	/* width: 200rpx; */
	text-align: center;
	height: 40rpx;
	line-height: 36rpx;
}
.little-right{
	width: 532rpx;
}
</style>
