<template>
	<view class="bargain">
		<view class="shop-wrap">
			<view class="shop-title">
				<image src="../../static/image/avatar-demo.png" mode="" class="avatar"></image>小胖纸
			</view>
			<view class="shop-info">
				<image src="../../static/image/shop-demo.png" mode="" class="shop-pic"></image>
				<view class="shop-detail">
					<view class="info-title">抱枕长条枕床头靠垫靠枕大靠背靠垫靠枕大靠背垫沙发枕头夹腿床上...</view>
					<view class="sub-desc">
						<text class="red">柔软舒适</text>
						<text class="red">性价比高</text>
						<text>库存:50</text>
						<text>|</text>
						<text>已售:30</text>
					</view>
					<view class="time">
						<text class="block hour">20</text> :
						<text class="block minute">20</text>:
						<text class="block seconds">20</text>
						<text class="desc">后砍价过期</text>
					</view>
				</view>
			</view>
			<view class="pro-text">
				原价 <text class="red"> 200元</text> 已砍 <text class="red"> 156.20 </text>元
			</view>
			<progress percent="60" active
			 border-radius="10" stroke-width="5" 
			 activeColor="#fc3742" backgroundColor="#e5e5e5"/>
			<view class="share">分享好友，代您砍价</view>
		</view>
		
		<view class="tab-nav">
			<view class="tab-item"  v-for="(item, idx) in tabList" :key="idx"
			@click="tabClick"
			:data-tabindex="idx"
			:class="{active: idx== current}">{{item}}</view>
		</view>
		<view class="tab tab-list-wrap" v-show="current == 0">
			<view v-for="(item,idx) in 10" :key="idx" class="tab-list">
				<view class="user">
					<image src="../../static/image/avatar-demo.png" mode="" class="user-avatar"></image>
					<view class="user-info">
						<view class="user-name">爱你哦***</view>
						<view class="user-date">03/16  10:24</view>
					</view>
				</view>
				<view class="money"> <text class="red">-28.6</text> 元</view>
			</view>
		</view>
		<view class="tab tab-list-wrap" v-show="current == 3">
			<view v-for="(item,idx) in 4" :key="idx" class="tab-list">
				<view class="user">
					<image src="../../static/image/avatar-demo.png" mode="" class="user-avatar"></image>
					<view class="user-info">
						<view class="user-name">爱你哦***</view>
						<view class="user-date">03/16  10:24</view>
					</view>
				</view>
				<view class="money"> <text class="red">-28.6</text> 元</view>
			</view>
		</view>
		<pop :popShow.sync="popShow"/>
		<view class="flot-btn">我也要砍</view>
	</view>
</template>

<script>
	import pop from '@/pages/bargain/pop.vue';
	export default { 
		data() {
			return {
				tabList: ['亲友团', '商品详情','活动规则', '参与榜'],
				current: 0,
				popShow: false
			}
		},
		components: {
			pop
		},
		methods: {
			tabClick(e) {
				this.current = e.currentTarget.dataset.tabindex
			}
		}
	}
</script>

<style>
.flot-btn {
	position: fixed;
	left: 20rpx;
	bottom: 0;
	width: 708rpx;
	height: 80rpx;
	text-align: center;
	line-height: 80rpx;
	background-color: #e02e24;
	font-size: 26rpx;
	color: #ffffff;
}
page {
	/* background: url('~@/static/image/bg-bargain.png') no-repeat center center; */
	/* background: url('https://demo.jihainet.com/static/uploads/images/2020/03/20/15846972345e748f92c0a17.png') no-repeat center center; */
}
.red {
	color: #fe4949;
}
.bargain {
	padding: 0 20rpx;
	padding-bottom: 100rpx;
}
.shop-wrap {
	padding: 0 20rpx;
	background: #fff;
	padding: 15rpx 20rpx 30rpx 20rpx;
	margin-top: 20rpx;
}
.shop-wrap .shop-title {
	display: flex;
	align-items: center;
	margin-bottom: 15rpx;
}
.shop-wrap .shop-title .avatar {
	width: 47rpx;
	height: 50rpx;
	border-radius: 50%;
	margin-right: 20rpx;
}
.shop-wrap .shop-info {
	padding: 20rpx;
	background: #f4f4f4;
	display: flex;
}
.shop-wrap .shop-info .shop-pic {
	width: 113rpx;
	height: 120rpx;
	margin-right: 30rpx;
}
.shop-info .shop-detail .info-title {
	width: 485rpx;
	overflow: hidden;
	white-space: nowrap;
	word-break: break-all;
	text-overflow: ellipsis;
	font-size: 20rpx;
}
.shop-info .shop-detail .sub-desc {
	font-size: 24rpx;
	color: #cacaca;
	margin-top: 6rpx;
}
.shop-info .shop-detail .sub-desc  text.red {
	font-size: 26rpx;
}
.shop-info .shop-detail .sub-desc  text {
	margin-right: 15rpx;
}
.shop-info .shop-detail .time {
	display: flex;
	align-items: center;
	margin-top: 15rpx;
}
.shop-info .shop-detail .time text.block {
	display: inline-block;
	width: 37rpx;
	height: 37rpx;
	text-align: center;
	line-height: 37rpx;
	background-color: #4f1701;
	font-size: 20rpx;
	color: #fff;
	margin:0 10rpx;
}
.shop-info .shop-detail .time text.block:first-child {
	margin-left: 0;
}
.shop-info .shop-detail .time text.desc {
	font-size: 18rpx;
	color: #5f5f5f;
}
.pro-text {
	margin-top: 20rpx;
	margin-bottom: 14rpx;
	text-align: center;
	font-size: 16rpx;
}
.pro-text text {
	font-size: 22rpx;
}
.share {
	width: 668rpx;
	height: 60rpx;
	border-radius: 30rpx;
	text-align: center;
	line-height: 60rpx;
	color: #fff;
	margin-top: 44rpx;
	background: linear-gradient(left, #ff7058, #fb3642);
}
.tab-nav {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	margin-top: 20rpx;
	background: #fff;
}
.tab-nav .tab-item {
	flex: 1;
	text-align: center;
	color: #e02e24;
	padding:28rpx 0;
	border-bottom: 1rpx solid #707070;
}
.tab-nav .tab-item.active {
	border-bottom: 1rpx solid #e02e24;
}

.tab-list {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 24rpx 20rpx;
}
.tab-list-wrap {
	background: #ffff;
}
.tab-list-wrap .user-avatar{
	width: 50rpx;
	height: 50rpx;
	border-radius: 50%;
	margin-right: 30rpx;
}
.tab-list-wrap .user {
	display: flex;
}
.tab-list-wrap .user .user-name {
	font-size: 24rpx;
	color: #434343;
}
.tab-list-wrap .user .user-date {
	color: #ababab;
	font-size: 16rpx;
	margin-top: 10rpx;
}
.tab-list-wrap .money {
	font-size: 16rpx;
	color: #ababab;
}
.tab-list-wrap .money text {
	font-size: 22rpx;
	color: #f91111;
}
</style>
