<template>
	<view class="content1">
		<view class="top">
			<view class="top-t">
				<view class="pro-img">
					<image :src="product.image" mode=""></image>
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
					<view class="spes-item" v-for="(item, index) in specList" :key="index">
						<view class="name">
							{{ index }}
						</view>
						<view class="list">
							<view :class="spes.cla" v-for="(spes, key) in item" :key="key" @click="specChangeSpes(index, key)">
								{{spes.name}}
							</view>
						</view>
					</view>
				</view>
				<view class="brief">
					<view class="name">
						产品描述
					</view>
					<view class="color-9">
						{{product.brief || ''}}
					</view>
				</view>
			</scroll-view>
		</view>
		<view class="bottom">
			<view class="bot-t">
				<view class="left">
					<view class="price">
						￥ {{ product.price || ''}}
					</view>
					<view class="spess">
						{{ product.default_spes_desc || ''}}
					</view>
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
				specList: {},
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
			spesData: function(val) {
				if (typeof val == 'object') {
					this.specList = val;
				} else {
					let d = JSON.parse(val);
					this.specList = d;
				}
			}
		},
		mounted() {
			// console.log("p",this.product);
		},
		methods: {
			specChangeSpes(v, k) {
				let newData = {
					v: v,
					k: k
				}
				this.$emit("changeSpes", newData);
			},
			// 加减数量
			change(val){
				this.nums = val;
			},
			addToCart() {
				this.submitStatus = true;
				if(this.mode == 2){
					let goodslist = this.$db.get("goodslist");
					// console.log("goodslist", goodslist);
					if(!goodslist){
						goodslist = []
					}
					let pro = JSON.parse(JSON.stringify(this.product));
					if(goodslist.length > 0){
						let status = goodslist.every(cart=>{
							return pro.id != cart.id
						})
						if(status){
							pro.buy_count = this.nums;
							goodslist.push(pro);
						}else{
							goodslist.forEach(item =>{
								if(pro.id == item.id){
									let nums = this.nums;
									item.buy_count = nums + item.buy_count;
								}
							})
						}
						
					}else{
						pro.buy_count = this.nums;
						goodslist.push(pro);
					}
					this.$db.set('goodslist', goodslist);
					this.close();
				}else{
					let cartlist = this.$db.get("cartlist");
					// console.log("cartlist", cartlist);
					if(!cartlist){
						cartlist = []
					}
					let pro = JSON.parse(JSON.stringify(this.product));
					if(cartlist.length > 0){
						let status = cartlist.every(cart=>{
							return pro.id != cart.id
						})
						if(status){
							pro.buy_count = this.nums;
							cartlist.push(pro);
						}else{
							cartlist.forEach(item =>{
								if(pro.id == item.id){
									let nums = this.nums;
									item.buy_count = nums + item.buy_count;
								}
							})
						}
						
					}else{
						pro.buy_count = this.nums;
						cartlist.push(pro);
					}
					this.$db.set('cartlist', cartlist);
					// 更新父页面购物车列表
					this.$emit('updateCart');
					this.close();
				}
				
			},
			// 关闭
			close() {
				this.$emit('toclose');
				this.nums = 1;
				this.submitStatus = false;
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
					color: #FEDD02;
					font-size: 28rpx;
					font-weight: 700;
				}
				.spess {
					color: #999;
					font-size: 24rpx;
				}
			}

			.btn {
				background-color: #FEDD02;
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
		background-color: #FEDD02 !important;
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
		border: 2rpx solid #FEDD02;
		border-radius: 100rpx;
	}
	/deep/ .uni-numbox__minus {
		background-color: #fff!important;
		width: 60rpx!important;
		border-radius: 100rpx!important;
		color: #FEDD02!important;
		font-size:	70rpx!important;
		line-height: 60rpx!important;
	}
	
	/deep/ .uni-numbox__plus {
		background-color: #fff!important;
		width: 60rpx!important;
		height: 60rpx!important;
		border-radius: 50%!important;
		color: #fff!important;
		background-color: #FEDD02!important;
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
</style>
