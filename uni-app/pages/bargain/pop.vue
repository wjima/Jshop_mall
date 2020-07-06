<template>
	<view class="pop" v-show="popShow">
		<view class="content">
			<view class="close-wrap">
				<image class="close" @click="close" src="../../static/image/close-2.png" mode=""></image>
			</view>
			<view class="purse">
				<image class="purse" src="../../static/image/purse.png" mode=""></image>
			</view>
			<view class="title">恭喜您成功砍掉</view>
			<view class="money"> <text>{{price||'0.00'}}</text>元</view>
			<view class="button" @click="close">我知道啦</view>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			popShow: {
				default: false
			},
			price:{
				default:0
			}
		},
		methods: {
			close() {
				this.$emit('update:popShow', false)
			}
		}
	}
</script>

<style>
.pop {
	position: fixed;
	width: 100%;
	height: 100vh;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, .5);
	z-index: 9;
}
.pop .content {
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	width: 400rpx;
	height: 450rpx;
	display: flex;
	flex-direction: column;
	align-items: center;
	background-color: #ffffff;
	border-radius: 20rpx;
	padding: 20rpx 25rpx;
}
.pop .content .purse {
	width: 100rpx;
	height: 100rpx;
	/* margin-top: 32rpx; */
}
.close-wrap {
	width: 100%;
	display: flex;
	flex-direction: row-reverse;
}
.pop .close {
	width: 21rpx;
	height: 21rpx;
}
.pop .title {
	margin-top: 40rpx;
}
.pop .money {
	color: #fe1421;
	font-size:26rpx;
	margin-top: 28rpx;
	margin-bottom: 20rpx;
}
.pop .money text {
	font-size: 56rpx;
}
.pop .button {
	width: 248rpx;
	height: 60rpx;
	line-height: 60rpx;
	text-align: center;
	color: #fff;
	background-color: #FF7159;
	border-radius: 5rpx;
}
</style>
