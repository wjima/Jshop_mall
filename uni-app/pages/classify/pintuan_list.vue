<template>
	<view class="content">
		<!-- 列表图片 -->
		<view class="img-list">
			<view v-if="goodsList.length > 0">
				<view class="img-list-item" v-for="(item, index) in goodsList" :key="index" @click="goodsDetail(item.id)">
					<image class="img-list-item-l" :src="item.image_url" mode="aspectFill"></image>
					<view class="img-list-item-r">
						<view class="goods-name list-goods-name">{{ item.name }}</view>
						<view class="goods-item-c">
							<view class="pintuan_time">
								<text class="fsz24 color-9">剩余：</text>
								<uni-countdown
									textColor="#999"
									color="#999"
									:day="item.lasttime.day"
									:hour="item.lasttime.hour"
									:minute="item.lasttime.minute"
									:second="item.lasttime.second"
								></uni-countdown>
							</view>
							<view class="goods-price red-price">
								￥{{ item.pintuanPrice }}
								<text class="people-num color-9 fsz24">{{ item.pintuan_rule.people_number }}人成团</text>
							</view>
							<view class="goods-buy">
								<view class="goods-salesvolume" v-if="item.comments_count > 0">{{ item.comments_count }}条评论</view>
								<view class="goods-salesvolume" v-else-if="item.comments_count <= 0">暂无评论</view>
								<image class="goods-cart" src="/static/image/more.png"></image>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="order-none" v-else><image class="order-none-img" src="/static/image/order.png" mode=""></image></view>
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
	onLoad: function() {
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
					_this.goodsList = res.data;
					_this.goodsList.forEach(item => {
						if (item.pintuan_price <= 0) {
							item.pintuan_price = '0.00';
						} else {
							item.pintuanPrice = this.$common.moneySub(item.price, item.pintuan_rule.discount_amount);
						}
						let timestamp = Date.parse(new Date()) / 1000;
						let lasttime = item.pintuan_rule.etime - timestamp;
						item.lasttime = _this.$common.timeToDateObj(lasttime);
					});
				}
			});
		}
	}
};
</script>

<style>
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
	margin-bottom: 10upx;
	width: 100%;
	overflow: hidden;
}
.people-num {
	margin-left: 16upx;
}
.img-list-item .goods-item-c {
	bottom: 0;
}
</style>
