<template>
	<view class="content">
		<view class="collection" v-if="bargainList.length">
			<view class="container_of_slide" v-for="item in bargainList" :key="item.id">
				<view class="slide_list">
					<view class="now-message-info" @click="bargainDetail(item)">
						<view class="icon-circle"><image class="goods-img" :src="item.image_url" mode="aspectFill"></image></view>
						<view class="list-right">
							<view class="list-title">{{ item.name }}</view>
							<view class="red-price">￥{{ item.price }}</view>
							<view class="list-detail">{{ item.ctime }}</view>
						</view>
						<view class="list-right-1"><image class="cell-ft-next icon" src="/static/image/right.png"></image></view>
					</view>
				</view>
			</view>
			<uni-load-more :status="loadStatus" :show-icon="true"></uni-load-more>
		</view>
	</view>
</template>

<script>
import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
export default {
	data() {
		return {
			page: 1,
			limit: 10,
			bargainList: [], // 积分记录
			loadStatus: 'more'
		};
	},
	components: { uniLoadMore },
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
		bargainDetail(item){
			uni.navigateTo({
				url:'/pages/bargain/index?id='+item.bargain_id+'&record_id='+item.id
			})
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
}
.slide_list {
	transition: all 100ms;
	transition-timing-function: ease-out;
	min-width: 200%;
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
}
.list-right-1 {
	float: right;
	color: #a9a9a9;
}
.list-title {
	width: 490upx;
	line-height: 1.5;
	overflow: hidden;
	color: #333;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 2;
	overflow: hidden;
	font-size: 26upx;
	color: #333;
	min-height: 80upx;
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
</style>
