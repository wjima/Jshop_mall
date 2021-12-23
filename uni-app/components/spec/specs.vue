<template>
	<view class="content1">
		<view class="top">
			<view class="top-t">
				<view class="pro-img">
					<image  :src="product.image_path || ''" mode="" ></image>
				</view>
				<view class="close" @click="close">
					<image src="/static/image/close.png" mode=""></image>
				</view>
			</view>
			<view class="title">
				{{ product.name || ''}}
			</view>
			<scroll-view scroll-y="true" style="max-height: 450upx;">
				<view class="spes-list">
					<view class="spes-item" v-for="(item, index) in specList()" :key="index">
						<view class="name">
							{{ index }}
						</view>
						<view class="list">
							<view :class="spes.cla" v-for="(spes, key) in item" :key="key" @click="specChangeSpes(index, key)">
								{{ spes.name.replace(/====/g,'.') }}
							</view>
						</view>
					</view>
				</view>
			</scroll-view>
		</view>
		<view class="bottom">
			<view class="bot-t">
				<view class="left">
				</view>
				<view class="cartNum">
					<uni-number-box :min="1" :value="nums" @change.self="change($event,product)"
					 :step="1" :has-border="true" :width="62" :size="100" class="cartNum"></uni-number-box>
				</view>
			</view>
			<button class="btn" @click="addToCart" :disabled='submitStatus' :loading='submitStatus'>
					加入购物车
			</button>
		</view>
	</view>
</template>

<script>
	import uniNumberBox from "@/components/uni-number-box/uni-number-box.vue";
	export default {
		name: "specs",
		components: {
			uniNumberBox
		},
		data() {
			return {
				// specList: {},
				submitStatus: false,
				cartlist:[],
				nums:1
			}
		},
		props: {
			// 默认picker选中项索引
			spesData: {
				required: true
			},
			product: {
				required: true
			},
			mode: {
				type: [String, Number],
				default: 1
			},
		},
		watch: {
			// spesData: function(val) {
			// 	if (typeof val == 'object') {
			// 		this.specList = val;
			// 	} else {
			// 		if(val) {
			// 			let d = JSON.parse(val);
			// 			this.specList = d;
			// 		}
					
			// 	}
			// }
		},
		mounted() {
			// console.log("p",this.product);
		},
		methods: {
			specList() {
				if (typeof this.spesData == 'object') {
					return this.spesData;
				} else {
					if( this.spesData) {
						return JSON.parse(this.spesData);
					}
					
				}
			},
			specChangeSpes(v, k) {
				let newData = {
					v: v,
					k: k
				}
				this.$emit("changeSpes", newData);
			},
			// 加减数量
			change(val){
				console.log(val);
				this.nums = val;
			},
			addToCart() {
				this.$emit('clickHandle', {id: this.product.id, nums: this.nums})
			},
			// 关闭
			close() {
				this.$emit('toclose');
			},
		}
	}
</script>

<style lang="scss">
	.content1 {

		.top {
			border-radius: 20rpx 20rpx 0 0;
			background-color: #fff;
			padding: 30rpx;
			.top-t{
				display: flex;
				justify-content: center;
			}
			.pro-img{
				width: 200rpx;
				height: 200rpx;
				image {
					width: 100%;
					height: 100%;
					border-radius: 16rpx;
				}
			}

			.title {
				text-align: center;
				font-size: 32rpx;
				color: #1A1600;
				width: 95%;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				margin-bottom: 20rpx;
				font-weight: 700;
				margin-top: 20rpx;
			}

			.close {
				position: absolute;
				top: 30rpx;
				right: 30rpx;
				width: 44rpx;
				height: 44rpx;

				image {
					width: 100%;
					height: 100%;
				}
			}

			.spes-list {
				.spes-item {
					margin-bottom: 20rpx;

					.name {
						color: #666;
					}

					.list {
						display: flex;
						justify-content: flex-start;
						flex-wrap: wrap;

						.item {
							padding: 20rpx;
							color: #9A9A9A;
							padding: 15rpx 30rpx;
							border-radius: 10rpx;
							margin: 15rpx 15rpx 15rpx 0;
						}
					}
				}
			}
		}

		.bottom {
			background-color: #fff;
			padding: 15rpx 30rpx;
			border-radius: 0 0 20rpx 20rpx;
			box-shadow: 0px 0px 12rpx rgba(0, 0, 0, 0.16);
			.bot-t{
				display: flex;
				justify-content: space-between;
			}
			.left {
				color: #333;
				width: 54%;
				
				.price{
					color: #da3324;
					font-size: 28rpx;
					font-weight: 700;
				}
				.spess {
					color: #999;
					font-size: 24rpx;
				}
			}

			.btn {
				background-color: #da3324;
				color: #fff;
				padding: 0 20rpx;
				border-radius: 15rpx;
				height: 80rpx;
				line-height: 80rpx;
				width: 100%;
				margin-top: 12rpx;
			}
		}
	}

	.selected {
		color: #fff !important;
		background-color: #da3324 !important;
	}

	.not-selected {
		color: #1A1600 !important;
		background-color: #F5F5F5 !important;
	}

	.none {
		color: #1A1600 !important;
		background-color: #F5F5F5 !important;
	}
	.brief{
		.name{
			color: #666;
			margin-bottom: 20rpx;
		}
	}
	.cartNum {
		/* #ifndef MP-WEIXIN */
		width: 160rpx;
		/* #endif */
		/* #ifdef MP-WEIXIN */
		width: 170rpx;
		/* #endif */
		height: 65rpx;
	}
	/deep/ .uni-numbox{
		height: 60rpx!important;
		border: 2rpx solid #da3324;
		border-radius: 100rpx;
	}
	/deep/ .uni-numbox__minus {
		background-color: #fff!important;
		width: 60rpx!important;
		border-radius: 100rpx!important;
		color: #da3324!important;
		font-size:	70rpx!important;
		line-height: 60rpx!important;
	}
	
	/deep/ .uni-numbox__plus {
		background-color: #fff!important;
		width: 60rpx!important;
		height: 60rpx!important;
		border-radius: 50%!important;
		color: #fff!important;
		background-color: #da3324!important;
		font-size: 60rpx!important;
		line-height: 60rpx!important;
	}
	
	/deep/ .uni-numbox__value {
		background-color: #fff!important;
		width: 40rpx!important;
		color: #1A1600!important;
		font-size: 32rpx!important;
	}
	
	/deep/ .uni-numbox--disabled {
		color: #C0C0C0;
	}
	
	.pop-m-item{
		padding: 20rpx;
		padding: 14rpx 30rpx;
		border-radius: 10rpx;
		margin: 14rpx 14rpx 14rpx 0;
		color: #1a1600!important;
		background-color: #f5f5f5!important;
		&.selected{
			background-color: #da3324!important;
			color: #fff!important;
		}
	}
	
	
	/deep/ .uni-numbox__minus {
		line-height: 44rpx !important;
	}
	/deep/ .uni-numbox__plus {
		line-height: 25px !important;
	    margin-top: -1px;
	}
</style>
