<template>
	<view class="content">
		<view class="bg">
			<image class="bg-img" src="/static/image/1571297537282.jpg" mode="widthFix"></image>
		</view>
		<!-- 列表图片 -->
		<view class="img-list">
			<view v-if="goodsList.length > 0">
				<view class="img-list-item" v-for="(item, index) in goodsList" :key="index" @click="goodsDetail(item.goods.id)">
					<image class="img-list-item-l little-img have-none" :src="item.goods.image_url" mode="aspectFill"></image>
					<view class="img-list-item-r">
						<view class="goods-name list-goods-name">{{ item.goods.name||'' }}</view>
						<view class="goods-item-c-tip">
							<view class="people-num fsz24">{{ item.goods.pintuan_rule.people_number||'0' }}人团</view>
						</view>
						<view class="goods-item-c">
							<view class="goods-price fsz28">
								￥{{ item.goods.pintuanPrice||'0.00' }} <text class="fsz24 color-9">￥{{item.goods.price||'0.00'}}</text>
							</view>
							<button class="btn"><text>去拼团</text><image class="icon" src="/static/image/right-w.png" mode=""></image></button>
						</view>
						
					</view>
				</view>
			</view>
			<!-- <view class="order-none" v-else><image class="order-none-img" src="/static/image/order.png" mode=""></image></view> -->
		</view>
	</view>
</template>

<script>
import uniCountdown from '@/components/uni-countdown/uni-countdown.vue';
export default {
	components: { uniCountdown },
	data() {
		return {
			goodsList: {},
			pintuanPrice: 0,
			lasttime: {
				day: 0,
				hour: false,
				minute: 0,
				second: 0
			} //购买倒计时
		};
	},
	//加载执行
	onLoad() {
		console.log('s');
		this.getGoods();
	},
	methods: {
		//跳转到商品详情页面
		goodsDetail: function(id) {
			let url = '/pages/goods/index/pintuan?id=' + id;
			this.$common.navigateTo(url);
		},
		//取得商品数据
		getGoods: function() {
			var _this = this;
			let data = {};
			_this.$api.pintuanList(data, res => {
				if (res.status) {
					_this.goodsList = res.data.list;
					_this.goodsList.forEach(item => {
						if (item.goods.pintuan_price <= 0) {
							item.goods.pintuan_price = '0.00';
						} else {
							item.goods.pintuanPrice = this.$common.moneySub(item.goods.price, item.goods.pintuan_rule.discount_amount);
						}
						let timestamp = Date.parse(new Date()) / 1000;
						let lasttime = item.goods.pintuan_rule.etime - timestamp;
						item.lasttime = _this.$common.timeToDateObj(lasttime);
					});
				}
			});
		}
	}
};
</script>

<style>
.content{
	padding-top: 520rpx;
	height: 100vh;
	background-color: #49a4c1;
}
.bg{
	width: 100%;
	overflow: hidden;
	position: absolute;
	top: 0;
	z-index: 98;
}
.bg-img{
	width: 100%;
	float: left;
}
.img-list{
	position: relative;
	z-index: 99;
	padding: 0 20rpx;
	
}
.list-grid {
	width: 44upx;
	height: 44upx;
	float: left;
}

.img-grids {
	padding-bottom: 26upx;
}
.img-grids-item {
	
	margin-bottom: 0;
}
.img-grids > view,
.img-list > view {
	overflow: hidden;
}

.order-none {
	text-align: center;
	padding: 200upx 0;
}
.order-none-img {
	width: 274upx;
	height: 274upx;
}
.goods-price {
	/* margin-bottom: 10upx; */
	/* width: 100%; */
	overflow: hidden;
}
.goods-price .fsz24{
	text-decoration: line-through;
	margin-left: 10rpx;
}
.people-num {
	margin-right: 16upx;
	border: 2rpx solid #FF7159;
	display: inline-block;
	border-radius: 4rpx;
	padding: 0 6rpx;
	color: #FF7159;
	transform: scale(.9);
}
.img-list-item{
	border-radius: 10rpx;
	margin-bottom: 16rpx;
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
	padding: 0 0rpx 0 14rpx;
	line-height: 1.6;
	display: flex;
	justify-content: center;
	align-items: center;
	border-radius: 4rpx;
	height: 46rpx;
	transform: scale(.9);
}
.img-list-item .goods-item-c .btn .icon{
	width: 40rpx;
	height: 40rpx;
}
.img-list-item-r{
	width: 492rpx;
	min-height: auto;
	padding: 0;
}
.goods-name{
	-webkit-line-clamp: 1;
	margin-bottom: 10rpx;
}
.goods-item-c-tip{
	margin-bottom: 6rpx;
}
</style>
