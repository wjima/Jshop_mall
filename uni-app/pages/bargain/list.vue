<template>
	<view class="bargain-list">
		<view class="img-wrap">
			<image src="../../static/image/banner.png" mode="" style="width:100%; height: 100%"></image>
		</view>
		<view class="list-wrap">
			<view class="list-item" v-for="(item, idx) in 5" :key="idx">
				<view class="pic">
					<image src="../../static/image/car.png" mode="" style="width: 100%; height: 100%;"></image>
				</view>
				<view class="cont">
					<text class="title">海晨酱-2019新款外套女黑色宽松短款工装夹克外套女秋冬百搭</text>
					<view class="desc">
						<text class="num">已售: <text>50</text> 件</text>
						<navigator url="/pages/bargain/index">
							<text class="btn">点击砍价</text>
						</navigator>
					</view>
				</view>
			</view>
		</view>
		<lvv-popup position="bottom" ref="lvvpopref">
			<view class="pop-wrap">
				<view class="pop-title">
					<text>选择收货地址</text>
					<image @click="toclose" src="../../static/image/close-2.png" class="close-btn" mode=""></image>
				</view>
				<view class="address">
					<image class="icon1" src="../../static/image/address.png" mode=""></image>
					<view class="add-info">
						<view class="add-user">
							<text>小胖纸</text> 15539466899
						</view>
						<view>河南省 郑州市 高新技术开发区 河南省郑州市高新技术开发区 科学大道金梭路西悦城 11楼 870</view>
					</view>
					<image class="icon2" src="../../static/image/check.png" mode=""></image>
				</view>
				<view class="subtitle">添加其他收货地址</view>
				<view class="btn-wrap">
					<view class="btn">已选定该商品</view>
				</view>
			</view>
		</lvv-popup>
	</view>
</template>

<script>
	import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
	export default {
		data() {
			return {
			}
		},
		components: {
			lvvPopup
		},
		mounted() {
		},
		methods: {
			// 显示modal弹出框
			toshow(type) {
				this.$refs.lvvpopref.show();
			},
			// 关闭modal弹出框
			toclose() {
				this.$refs.lvvpopref.close();
			},
		}
	}
</script>

<style>
	.pop-wrap {
		width: 100%;
		max-height: 804upx;
		background: #FFFFFF;
		position: absolute;
		left:0;
		bottom: 0;
	}
	.bargain-list {
		
	}
	.bargain-list .img-wrap {
		width: 100%;
		height: 300rpx;
	}
	.bargain-list .list-item {
		border-bottom: 1rpx solid #dfdfdf;
		padding: 20rpx;
		display: flex;
	}
	.pic {
		width: 141rpx;
		flex: 0 0 141rpx;
		height: 141rpx;
		margin-right: 25rpx;
	}
	.cont {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}
	.cont .title {
		font-size: 20rpx;
	}
	.cont .desc {
		display: flex;
		justify-content: space-between;
	}
	.cont .desc .num {
		color: #ff3535;
		font-size: 20rpx;
	}
	.cont .desc .num text {
		font-size: 24rpx;
	}
	.cont .desc .btn {
		width: 124rpx;
		height: 36rpx;
		text-align: center;
		line-height: 36rpx;
		background-color: #fc3742;
		border-radius: 5rpx;
		font-size: 20rpx;
		color: #ffffff;
	}
	.pop-wrap {
		width: 100%;
	}
	.pop-title {
		position: relative;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		border-bottom: 1rpx solid #a0a0a0;
	}
	.pop-wrap .close-btn {
		width: 19rpx;
		height: 19rpx;
		position: absolute;
		top: 50%;
		right: 20rpx;
		transform: translateY(-50%);
	}
	.pop-wrap .address {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 25rpx 0;
		border-bottom: 1rpx solid #a0a0a0;
	}
	.pop-wrap .address .add-info {
		font-size: 20rpx;
	}
	.pop-wrap .address .add-user {
		margin-bottom: 18rpx;
	}
	.pop-wrap .address .add-user text {
		font-size: 26rpx;
		margin-right: 18rpx;
	}
	.pop-wrap .address .icon1 {
		width: 71rpx;
		flex: 0 0 71rpx;
		height: 65rpx;
		margin-left: 30rpx;
		margin-right: 50rpx;
	}
	.pop-wrap .address .icon2 {
		width: 36rpx;
		flex: 0 0 36rpx;
		height: 34rpx;
		margin-left: 50rpx;
		margin-right: 40rpx;
	}
	.pop-wrap .address .add-info {
		flex: 1;
	}
</style>
