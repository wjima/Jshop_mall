<template>
	<view>
		<image class="free-1" src="../../static/images/free/free-1.png" mode="widthFix"></image>
		<view class="free-2-wrap">
			<image class="free-2" src="../../static/images/free/free-2.png" mode="widthFix"></image>
			<view class="goods-list">
				<view class="goods-item" v-for="item in 12">
					<image class="goods-main-pic" src="../../static/image/1571297537282.jpg" mode="aspectFill"></image>
					<view class="title">10包装|薇垭推荐洗脸巾一次性薇垭推荐洗脸巾一次性</view>
					<view class="price-wrap"><text class="price">￥89.90</text> <image class="cart" src="../../static/images/free/free-cart.png" mode=""></image> </view>
				</view>
			</view>
		</view>
		<view class="bot-tips">
			<image class="free-3" src="../../static/images/free/free-3.png" mode="widthFix"></image>
			
			<view class="bot-content">
				<view class="title">活动规则</view>
				<view class="bot-item-wrap">
					<view class="item">活动期间购买三个商品，支付时最便宜的免单</view>
					<view class="item">三个商品的总值≥200元</view>
				</view>
			</view>
			
		</view>
	</view>
</template>

<script>
</script>

<style lang="scss" scoped>
	.free-1, .free-2,.free-3{
		width: 100%;
		font-size: 0;
		display: block;
		line-height: 0;
	}
	
	.free-2 {
		position: relative;
	}
	// .bot-content {
	// 	position: relative;
	// }
	.free-2-wrap {
		position: relative;
		.goods-list {
			position: absolute;
			top: 0;
			left: 0;
			z-index: 2;
			width: 100%;
			height: 100%;
			padding:130rpx 48rpx;
			display: flex;
			flex-wrap: wrap;
			.goods-item {
				border-radius: 8rpx;
				overflow: hidden;
				background: #FFFFFF;
				width: 208rpx;
				// height: 320rpx;
				margin-left: 14rpx;
				margin-bottom: 24rpx;
				&:nth-child(3n-2) {
					margin-left: 0;
				}
				
			}
			
			.goods-main-pic {
				width: 208rpx;
				height: 208rpx;
				background: #FFFFFF;
			}
			.title {
				font-size: 24rpx;
				color: #333333;
				padding: 4rpx 16rpx;
				@include ellNum();
			}
			.price-wrap {
				font-size: 24rpx;
				font-family: PingFang SC;
				font-weight: bold;
				line-height: 24px;
				color: #F44444;
				opacity: 1;
				padding: 0 16rpx;
				position: relative;
				.cart {
					position: absolute;
					top: 50%;
					right: 20rpx;
					transform: translateY(-50%);
					width: 28rpx;
					height: 28rpx;
				}
			}
		}
	}
</style>
