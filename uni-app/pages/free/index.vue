<template>
	<view>
		<image class="free-1" src="../../static/images/free/free-1.png" mode="widthFix"></image>
		<view class="free-2-wrap">
			<image class="free-2" src="../../static/images/free/free-2.png" mode="widthFix"></image>
			<view class="goods-list">
				<view class="goods-item" v-for="item in goods" :key="item.id">
					<image class="goods-main-pic" :src="item.image_url" mode="aspectFill"></image>
					<view class="title">{{item.name|| ""}}</view>
					<view class="price-wrap"><text class="price">￥{{item.product.price ||' 0.00'}}</text> <image class="cart" src="../../static/images/free/free-cart.png" mode=""
					@click="cartAdd(item)"></image> </view>
				</view>
			</view>
		</view>
		<view class="bot-tips">
			<image class="free-3" src="../../static/images/free/free-3.png" mode="widthFix"></image>
			
			<view class="bot-content">
				<view class="title">
					<image class="title-icon" src="../../static/images/free/title-icon.png" mode=""></image>
					活动规则
					<image class="title-icon icon2" src="../../static/images/free/title-icon.png" mode=""></image>
					</view>
				<view class="bot-item-wrap">
					<view class="item"> <text>1</text> {{goodsConfig.combo_desc || ''}}</view>
					<!-- <view class="item"> <text>2</text> 三个商品的总值≥200元</view> -->
				</view>
			</view>
			
		</view>
		
		<view class="fast-cart" @click="toCart" v-if="nums">
			<image src="/static/images/free/cart.png" mode=""></image>
			<text class="big" >{{nums}}</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				form: {
					page: 1,
					limit: 12,
					where: JSON.stringify({
						is_combo: 1
					})
				},
				goods: [],
				goodsConfig: {},
				nums: 0
			}
		},
		mounted() {
			this.getGoodsList()
			this.listConfig()
			this.getCartNums()
		},
		methods: {
			listConfig() {
				this.$api.freePackage({}, res => {
					if(res.status) {
						this.goodsConfig = res.data
					}
				})
			},
			getGoodsList() {
				this.$api.goodsList(this.form, res =>{
					if(res.status) {
						this.goods = res.data.list
					}
				})
			},
			cartAdd(val) {
				this.$api.addCart({
					nums: 1,
					product_id: val.product.id,
					order_type: 8
				}, res => {
					this.getCartNums()
					uni.showToast({
						title: res.msg,
						icon: 'none'
					})
				})
			},
			getCartNums() {
				this.$api.GetcartidsFreePackage({}, res => {
					if(res.status) {
						if(res.data.length != 0) {
							this.nums = res.data.split(',').length
							if(this.nums >99) {
								this.nums = '99+'
							}
						}
					}
				})
			},
			toCart() {
				uni.setStorageSync('order_type', 8)
				uni.navigateTo({
					url:'../cart/index/free'
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.fast-cart {
		position: fixed;
		right: 0;
		top: 50%;
		transform: translateY(-50%);
		width: 102rpx;
		height: 88rpx;
		background: rgba($color: #ffffff, $alpha: .8);
		box-shadow: 0px 0px 6rpx rgba(0, 0, 0, 0.16);
		border-radius: 100rpx 0px 0px 100rpx;
		z-index: 10;
		display: flex;
		align-items: center;
		image {
			width: 58rpx;
			height: 58rpx;
			margin-left: 20rpx;
		}
		text {
			position: absolute;
			top: 12rpx;
			left: 50rpx;
			display: inline-block;
			background: #F44444;
			border-radius: 50%;
			color: #FFFFFF;
			text-align: center;
			line-height: 28rpx;
			padding: 4rpx;
			min-width: 30rpx;
			font-size: 24rpx;
			&.big {
				border-radius: 20rpx;
			}
		}
		
	}
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
				height: 340rpx;
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

	.bot{
		&-tips {
			width: 750rpx;
			height: 412rpx;
			position: relative;
		}
		&-content {
			position: absolute;
			width: 750rpx;
			height: 412rpx;
			top: 0;
			left: 0;
			padding:90rpx 84rpx;
			.title {
				text-align: center;
				color: #F44444;
				font-size: 32rpx;
				font-weight: bold;
				margin-bottom: 38rpx;
				&-icon {
					width: 46rpx;
					height: 16rpx;
					margin: 0 20rpx;
					&.icon2 {
						transform: rotateY(180deg);
					}
				}
			}
		}
		&-item-wrap{
			
			.item {
				font-size: 28rpx;
				line-height: 36rpx;
				color: #333333;
				display: flex;
				margin-bottom: 16rpx;
			}
			text {
				display: inline-block;
				width: 32rpx;
				height: 32rpx;
				flex: 0 0  32rpx;
				background: #F44444;
				border-radius: 50%;
				text-align: center;
				color: #FFFFFF; 
				font-size: 24rpx;
				margin-right: 16rpx;
			}
		}
	}
</style>
