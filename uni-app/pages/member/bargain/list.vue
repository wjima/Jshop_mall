<template>
	<view class="content">
		<view class="collection" v-if="bargainList.length">
			<view class="container_of_slide" v-for="item in bargainList" :key="item.id">
				<view class="slide_list">
					<view class="now-message-info" @click="bargainDetail(item)">
						<view class="icon-circle"><image class="goods-img" :src="item.image_url" mode="aspectFill"></image></view>
						<view class="list-right">
							<view class="list-title">{{ item.name }}</view>
							<view class="count-down" v-if="item.status == 1 && item.lasttime">
								<text class="count-down-text">倒计时</text>
								<uniCountdown :show-colon="false" splitor-color="#868686" color="#FF7159" :day="item.lasttime.day" :hour="item.lasttime.hour" :minute="item.lasttime.minute" :second="item.lasttime.second"/>
							</view>
							<view class="red-price">
								<text class="price-txt">已砍至</text>
								￥{{ item.price }}
							</view>
						</view>
						<view class="list-right-1"></view>
					</view>
				</view>
				<view class="footer-button">
					<text class="txt" v-if="item.status == 1">活动进行中</text>
					<text class="txt" v-if="item.status == 2">砍价成功</text>
					<text class="txt" v-if="item.status == 3">活动已生成订单</text>
					<text class="txt" v-if="item.status == 4">活动结束</text>
					<text class="txt" v-if="item.status == 5">活动已取消</text>
					<view class="btn-wrap">
						<view class="btn cancle" @click="cancleBargain(item)" v-if="item.status == 1">取消活动</view>
						<view class="btn submit" @click="bargainDetail(item)" v-if="item.status == 1">继续砍价</view>
						<view class="btn submit" @click="buyNow(item)" v-if="item.status == 2">立即下单</view>
					</view>
				</view>
			</view>
			<uni-load-more :status="loadStatus" :show-icon="true"></uni-load-more>
		</view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
import uniCountdown from '@/components/uni-countdown/uni-countdown.vue';
export default {
	data() {
		return {
			page: 1,
			limit: 10,
			bargainList: [], // 积分记录
			loadStatus: 'more'
		};
	},
	components: { uniLoadMore, uniCountdown },
	onLoad() {
		this.userbargainLog();
	},
	computed: {
		nowDate() {
			return this.$common.timeToDate(Math.round(new Date().getTime() / 1000));
		}
	},
	methods: {
		userbargainLog() {
			let _this = this;
			let data = {
				page: _this.page,
				limit: _this.limit
			};

			_this.loadStatus = 'loading';

			_this.$api.getUserBargainLog(data, function(res) {
				if (res.status) {
					_this.bargainList = [..._this.bargainList, ...res.data.list];
					// 判断数据是否加载完毕
					if (res.count > _this.bargainList.length) {
						_this.page++;
						_this.loadStatus = 'more';
					} else {
						_this.loadStatus = 'noMore';
					}
				} else {
					// 接口請求出錯
					_this.$common.errorToShow(res.msg);
					_this.loadStatus = 'more';
				}
			});
		},
		bargainDetail(item) {
			uni.navigateTo({
				url: '/pages/bargain/index?id=' + item.bargain_id + '&record_id=' + item.id
			});
		},
		//取消记录
		cancleBargain(item) {
			let data = {
				record_id: item.id
			};
			let _this = this;
			this.$api.cancleBargain(data, function(res) {
				if (res.status) {
					_this.page = 1;
					_this.bargainList = [];
					_this.userbargainLog(); //更新记录
					_this.$common.successToShow(res.msg);
				} else {
					_this.$common.errorToShow(res.msg);
				}
			});
		},
		// 立即购买
		buyNow(item) {
			let data = {
				product_id: item.product_id,
				nums: 1,
				type: 2,
				order_type: 6 //砍价
			};
			this.$api.addCart(
				data,
				res => {
					if (res.status) {
						let cartIds = res.data;
						this.$common.navigateTo(
							'/pages/goods/place-order/index?cart_ids=' + JSON.stringify(cartIds) + '&order_type=6&bargain_id=' + item.bargain_id + '&record_id=' + item.id
						);
					} else {
						this.$common.errorToShow(res.msg);
					}
				},
				res => {
					this.submitStatus = false;
				}
			);
		}
	},
	// 页面滚动到底部触发事件
	onReachBottom() {
		let _this = this;
		if (_this.loadStatus === 'more') {
			_this.userPointLog();
		}
	}
};
</script>

<style scoped>
.collection .goods-img {
	width: 150upx;
	height: 150upx;
}
.container_of_slide {
	width: 100%;
	overflow: hidden;
	margin-bottom: 10rpx;
}
.slide_list {
	transition: all 100ms;
	transition-timing-function: ease-out;
	min-width: 200%;
	overflow: hidden;
}
.now-message-info {
	box-sizing: border-box;
	display: flex;
	align-items: center;
	font-size: 16px;
	clear: both;
	padding: 20upx 26upx;
	margin-bottom: 2upx;
	background: #ffffff;
	width: 100%;
}
.now-message-info,
.group-btn {
	float: left;
}
.group-btn {
	display: flex;
	flex-direction: row;
	height: 190upx;
	min-width: 100upx;
	align-items: center;
}
.group-btn .btn-div {
	height: 190upx;
	color: #fff;
	text-align: center;
	padding: 0 50upx;
	font-size: 34upx;
	line-height: 190upx;
}
.group-btn .top {
	background-color: #ff7159;
}
.group-btn .removeM {
	background-color: #999;
}
.icon-circle {
	width: 150upx;
	height: 150upx;
	float: left;
}
.list-right {
	float: left;
	margin-left: 25upx;
	height: 150upx;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}
.list-right-1 {
	float: right;
	color: #a9a9a9;
}
.list-title {
	width: 490upx;
	/*line-height: 1.5;
	overflow: hidden;
	color: #333;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 2; */
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
	font-size: 26upx;
	color: #333;
	/* min-height: 80upx; */
}
.list-detail {
	width: 460upx;
	font-size: 24upx;
	color: #a9a9a9;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 1;
	overflow: hidden;
}
.history-none {
	text-align: center;
	padding: 200upx 0;
}
.history-none-img {
	width: 274upx;
	height: 274upx;
}
.footer-button {
	background: #fff;
	display: flex;
	justify-content: space-between;
	color: #000000;
	padding: 20rpx 28rpx;
	align-items: center;
}
.footer-button .txt {
	color: #f78513;
}
.footer-button .btn {
	padding: 10rpx 30rpx;
	border-radius: 4rpx;
	margin-left: 10rpx;
}
.footer-button .btn.cancle {
	color: #aaa;
	border: 1rpx solid #ddd;
}
.footer-button .btn.submit {
	background-color: #ff1f44;
	color: #fff;
}
.red-price .price-txt {
	font-size: 22rpx;
}
.count-down {
	font-size: 26rpx;
	color: #868686;
}
.count-down-text {
	margin-right: 10rpx;
}
</style>
