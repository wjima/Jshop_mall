<template>
	<view>
		<image class="free-1" :src="`${baseUrl}static/images/free/free-1.png`" mode="widthFix"></image>
		<view class="free-2-wrap">
			<image class="free-2" :src="`${baseUrl}static/images/free/free-2.png`" mode="widthFix"></image>
			<view class="goods-list">
				<view class="goods-item" v-for="item in goods" :key="item.id">
					<image class="goods-main-pic" :src="item.image_url" mode="aspectFill"></image>
					<view class="title">{{item.name|| ""}}</view>
					<view class="price-wrap"><text class="price">￥{{item.product.price ||' 0.00'}}</text> <image class="cart" :src="`${baseUrl}static/images/free/free-cart.png`" mode=""
					@click="cartAdd(item)"></image> </view>
				</view>
			</view>
		</view>
		<view class="bot-tips" :class="{'bot-tips-height' : numsCart > 0}">
			<image class="free-3" :src="`${baseUrl}static/images/free/free-3.png`" mode="widthFix"></image>
			
			<view class="bot-content">
				<view class="title">
					<image class="title-icon" :src="`${baseUrl}static/images/free/title-icon.png`" mode=""></image>
					活动规则
					<image class="title-icon icon2" :src="`${baseUrl}static/images/free/title-icon.png`" mode=""></image>
					</view>
				<view class="bot-item-wrap">
					<view class="item"> {{goodsConfig.combo_desc || ''}}</view>
					<!-- <view class="item"> <text>2</text> 三个商品的总值≥200元</view> -->
				</view>
			</view>
			
		</view>
		
		<!-- <view class="fast-cart" @click="toCart" v-if="nums">
			<image src="`${baseUrl}static/images/free/cart.png`" mode=""></image>
			<text class="big" >{{nums}}</text>
		</view> -->
		
		<!-- 弹出层 -->
		<lvv-popup position="bottom" ref="lvvpopref">
			<view style="width: 100%;max-height: 804upx;background: #FFFFFF;position: absolute;left:0;bottom: 0;">
				<view class="pop-c">
					<view class="pop-t" style="padding: 26rpx;">
						<view class='goods-img'>
							<image :src='product.image_path' mode='aspectFill'></image>
						</view>
						<view class='goods-information'>
							<view class='pop-goods-name' style="margin-bottom: 6rpx;">{{ product.name || ''}}</view>
							<view class='pop-goods-price red-price' style="margin-bottom: 6rpx;">￥ {{ product.price || ''}}</view>
							<view class="fsz24 color-9">
								库存{{ product.stock || ''}}
							</view>
						</view>
						<view class='close-btn' @click="toclose()">
							<image :src="`${baseUrl}static/images/free/close.png`"></image>
						</view>
					</view>
					<!-- <scroll-view class="pop-m" scroll-y="true" style="max-height: 560upx;">
						<spec :spesData="defaultSpesDesc" ref="spec" @changeSpes="changeSpes"></spec>
						<view class="goods-number">
							<text class="pop-m-title">数量</text>
							<view class="pop-m-bd-in">
								<uni-number-box :min="minNums" :max="product.stock" 
								:value="buyNum" @change="bindChange"></uni-number-box>
							</view>
						</view>
					</scroll-view> -->
					<view class="pop-b">
						<button class='btn btn-square btn-b btn-all' hover-class="btn-hover2" @click="clickHandle()"
						 :disabled='submitStatus'
						 :loading='submitStatus' v-if="product.stock">确定</button>
						<button class='btn btn-square btn-g btn-all' v-else>已售罄</button>
					</view>
				</view>
			</view>
		</lvv-popup>
		<!-- 弹出层end -->
		
		<lvv-popup position="center" ref="spes" class="spes-content" >
			<view class="content" >
				<specs  v-if="product.id " :spesData="defaultSpesDesc" :product="product" ref="spec"
				 @changeSpes="changeSpes" @clickHandle="clickHandle" @toclose="toclose"></specs>
			</view>
		</lvv-popup>
		
		
		<lvv-popup ref="cart" position="bottom">
			<view class="cart-lvv">
				<scroll-view scroll-y="true" style="max-height: 560upx;">
				<view class="goodsList" v-for="(goods,i) in cartLists" :key="goods.id">
					<view class="goods-l">
						<view class="name two-line">
							<text v-if="goods.products && goods.products.spes_desc">({{goods.products.spes_desc || ''}})</text> {{goods.products.name}}
						</view>
						<view class="spec" v-if="goods.default_spes_desc">
							- 规格({{goods.default_spes_desc}})
						</view>
					</view>
					<view class="goods-r column-c">
						<view class="price">
							￥{{goods.products.price}}
						</view>
						<uni-number-box :value="goods.nums" @change.self="bindChange($event,goods)"
						 :step="1" :has-border="true"
						 :width="62" :size="100" class="nums"></uni-number-box>
		
					</view>
				</view>
				</scroll-view>
			</view>
		</lvv-popup>
		<!-- 底部 -->
		<view class="cart-bottom"  v-if="numsCart">
			<view class="cart" id="cart" @click="toCart">
				<image :src="`${baseUrl}static/images/free/cart.png`" mode=""></image>
				 <text  v-if="ids">  {{numsCart}}  </text>
			</view>
			<view class="submit" @click="goCart" > 去支付</view>
		</view>
	</view>
</template>

<script>
	
	const delay = (function() {
		let timer = 0
		return function(callback, ms) {
			clearTimeout(timer)
			timer = setTimeout(callback, ms)
		}
	})()
	
	import {apiBaseUrl} from '@/config/config.js';
	import specs from '@/components/spec/specs.vue';
	export default {
		components: {
			specs
		},
		computed: {
			minNums() {
				if(this.product.stock == 0) {
					this.buyNum = 0
					return 0
				} else {
					return this.product.stock > this.minBuyNum ? this.minBuyNum : this.product.stock;
				}
			},
			defaultSpesDesc() {
				return this.product.default_spes_desc;
			}
		},
		data() {
			return {
				submitStatus: false,
				buyNum: 1, // 选定的购买数量
				minBuyNum: 1, // 最小可购买数量
				form: {
					page: 1,
					limit: 12,
					where: JSON.stringify({
						is_combo: 1
					})
				},
				goods: [],
				goodsConfig: {},
				nums: 0,
				product: {},
				cartLists: [],
				ids: '',
				cartData: {},
				numsCart:0,
				cartId: 0,
				cartNum: 0,
				baseUrl:apiBaseUrl
			}
		},
		onShow() {
			this.getGoodsList()
			this.listConfig()
			this.getCartNums()
			this.getCartList()
		},
		methods: {
			toclose( ) {
				this.$refs.spes.close()
				setTimeout(() => {
					this.product = {}
				},50)
			},
			goCart() {
				const _this = this
				_this.$common.navigateTo('/pages/goods/place-order/index?order_type=8&cart_ids=' + JSON.stringify(this.ids));
			},
			getCartList() {
				const _this = this
				let data = {
					ids: this.ids,
					display: 'all',
					order_type: 8
				};
				this.$api.cartList(data, function(res) {
					_this.cartLists = res.data.list
					
					if( res.data.list.length ) {
						let nums = 0
						for(let i of res.data.list) {
							nums += i.nums
						}
						_this.numsCart = nums
					} else {
						_this.numsCart = 0
						
						_this.$refs.cart.close()
					}
					
					if (res.status) {
						
						
					} else {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})
					}
				});
			},
			// 删除购物车
			delList(ids) {
				let _this = this;
				let data = {
					ids,
					order_type: 8
				};
				_this.$api.removeCart(data, function(res) {
					if (res.status) {
						_this.getCartList()
						_this.$common.successToShow(res.msg);
					}
					// _this.setNumsData();
				});
			},
			
			
			//购物车数量调整
			bindChange(value, e) {
				delay(() => {
					let _this = this;
					let id = e.products.id;
					let num = value;
					console.log('num', num);
					if(num == 0) {
						this.delList(e.id)
						return
					}
					let cartData = _this.cartLists;
					let changeSelected = false;
					for (let key in cartData) {
						if (cartData[key].product_id == id) {
							if (num <= cartData[key].products.stock) {
								cartData[key].nums = num;
								changeSelected = true;
							}
							break;
						}
					}
					if (changeSelected) {
						_this.cartData = cartData;
						
						_this.cartId = e.id;
						_this.cartNum = num;
						
						
						_this.$common.throttle(_this.bindCartNumberOperation, _this, 350);
					}
					return false;
				}, 500)
			
			},
			
			//数量减一操作
			bindCartNumberOperation: function() {
				let _this = this;
				_this.setCartNum(_this.cartId, _this.cartNum);
			},
			
			
			//设置购物车数量
			setCartNum: function(id, nums) {
				let _this = this;
				let data = {
					id: id,
					nums: nums,
					ids: this.ids,
					order_type: 8
				};
				_this.$api.setCartNum(data, function(res) {
						if (res.status) {
							_this.cartData = res.data
							_this.cartLists = res.data.list
							
							let nums = 0
							for(let i of res.data.list) {
								nums += i.nums
							}
							_this.numsCart = nums
						} else {
							_this.$common.errorToShow(res.msg);
						}
				});
			},
			// 切换商品规格
			changeSpes(obj) {
				let index = obj.v;
				let key = obj.k;
			
				let userToken = this.$db.get('userToken');
				let tmp_default_spes_desc = JSON.parse(this.product.default_spes_desc);
				if (tmp_default_spes_desc[index][key].hasOwnProperty('product_id') && tmp_default_spes_desc[index][key].product_id) {
					// this.$refs.spec.changeSpecData();
					this.$api.getProductInfo({
						id: tmp_default_spes_desc[index][key].product_id,
						token: userToken
					}, res => {
						if (res.status == true) {
							// 切换规格判断可购买数量
							this.buyNum = res.data.stock > this.minBuyNum ? this.minBuyNum : res.data.stock;
							this.product = this.spesClassHandle(res.data);
						}
					});
					uni.showLoading({
						title: '加载中'
					});
					setTimeout(function() {
						uni.hideLoading();
					}, 1000);
				}
			},
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
			
			// 多规格样式统一处理
			spesClassHandle(products) {
				// 判断是否是多规格 (是否有默认规格)
				if (products.hasOwnProperty('default_spes_desc') && typeof(products.default_spes_desc) != 'string') {
					let spes = products.default_spes_desc;
					for (let key in spes) {
						for (let i in spes[key]) {
							if (spes[key][i].hasOwnProperty('is_default') && spes[key][i].is_default === true) {
								this.$set(spes[key][i], 'cla', 'pop-m-item selected');
							} else if (spes[key][i].hasOwnProperty('product_id') && spes[key][i].product_id) {
								this.$set(spes[key][i], 'cla', 'pop-m-item not-selected');
							} else {
								this.$set(spes[key][i], 'cla', 'pop-m-item none');
							}
						}
					}
					spes = JSON.stringify(spes).replace(/\./g,'====');
					products.default_spes_desc = spes;
				}
				return products;
			},
			
			clickHandle(obj){
				let {nums, id: product_id} = obj
				this.$api.addCart({
					nums,
					product_id,
					order_type: 8
				}, res => {
					this.$refs.spes.close()
					this.getCartNums()
					uni.showToast({
						title: res.msg,
						icon: 'none'
					})
				})
			},
			cartAdd(val) {
				
				
				console.log('val.product', val.product);
				this.product = this.spesClassHandle(val.product);
				this.$refs.spes.show()
			},
			getCartNums() {
				this.$api.GetcartidsFreePackage({}, res => {
					if(res.status) {
						if(res.data.length != 0) {
							this.ids = res.data
							this.nums = res.data.split(',').length
							this.getCartList()
							if(this.nums >99) {
								this.nums = '99+'
							}
						}
					}
				})
			},
			toCart() {
				this.$refs.cart.show()
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
			padding:108rpx 48rpx;
			.goods-item {
				float: left;
				border-radius: 8rpx;
				overflow: hidden;
				background: #FFFFFF;
				width: 208rpx;
				height: 330rpx;
				margin-left: 14rpx;
				margin-bottom: 16rpx;
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
	
	
	.pop-t {
		position: relative;
		padding: 30upx 26upx;
		border-bottom: 2upx solid #f3f3f3;
	}
	
	.goods-img {
		width: 160upx;
		height: 160upx;
		position: absolute;
		top: -20upx;
		background-color: #fff;
		border-radius: 6upx;
		border: 2upx solid #fff;
	
	}
	
	.goods-img image {
		height: 100%;
		width: 100%;
	}
	
	.goods-information {
		width: 420upx;
		display: inline-block;
		margin-left: 180upx;
	}
	
	.pop-goods-name {
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		display: block;
		font-size: 24upx;
		margin-bottom: 20upx;
	}
	
	.pop-goods-price {
		font-size: 30upx;
	}
	
	.close-btn {
		width: 40upx;
		height: 40upx;
		border-radius: 50%;
		display: inline-block;
		position: absolute;
		right: 30upx;
	}
	
	.close-btn image {
		width: 100%;
		height: 100%;
	}
	
	.pop-m {
		font-size: 28upx;
		margin-bottom: 90upx;
	}
	
	.goods-specs,
	.goods-number {
		padding: 26upx;
		border-top: 1px solid #f3f3f3;
	}
	
	.bot-tips-height{
		height: 255px;
	}
	
	.goods-specs:first-child {
		border: none;
	}
	
	.pop-m-title {
		margin-right: 10upx;
		color: #666;
	}
	
	.pop-m-bd {
		overflow: hidden;
		margin-top: 10upx;
	}
	
	.pop-m-item {
		display: inline-block;
		float: left;
		padding: 6upx 16upx;
		background-color: #fff;
		color: #333;
		margin-right: 16upx;
		margin-bottom: 10upx;
	}
	
	.selected {
		border: 2upx solid #333;
		background-color: #333;
		color: #fff;
	}
	
	.not-selected {
		border: 2upx solid #ccc;
	}
	
	.none {
		border: 2upx dashed #ccc;
		color: #888;
	}
	
	.pop-m-bd-in {
		display: inline-block;
	}
	
	
	.spes-content {
		z-index: 100;
		.lvv-popup {
			z-index: 100;
		}
		.content {
			width: 80%;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
	}
	
	
	.cart-lvv {
		width: 100%;
		bottom: 0;
		/* #ifdef MP-ALIPAY */
		bottom: 0;
		/* #endif */
		height: 300rpx;
		background: #ffffff;
		position: absolute;
		left: 0;
		padding-bottom: 158rpx;
		border-radius: 16rpx 16rpx 0 0;
		min-height: 850rpx;
	
		.top {
			display: flex;
			justify-content: space-between;
			padding: 30rpx;
			border-bottom: 2rpx solid #f2f2f2;
	
			.top-l {
				image {
					width: 36rpx;
					height: 36rpx;
					vertical-align: bottom;
					margin-right: 4rpx;
				}
	
				text {
					color: #1A1600;
					font-size: 32rpx;
				}
			}
		}
	
		.goodsList {
			padding: 30rpx;
			display: flex;
			justify-content: space-between;
			border-bottom: 2rpx solid #fafafa;
	
			&:last-child {
				border: none;
			}
	
			.goods-l {
				width: 50%;
				display: flex;
				align-items: center;
				.name {
					font-size: 28rpx;
					color: #1A1600;
					font-weight: 700;
				}
	
				.spec {
					font-size: 24rpx;
					color: #1A1600;
					margin-top: 24rpx;
				}
			}
	
			.goods-r {
				width: 45%;
				display: flex;
				justify-content: space-between;
	
				.price {
					font-size: 32rpx;
					color: #1A1600;
					font-weight: 700;
					line-height: 60rpx;
				}
	
				.nums {
					width: 160rpx;
					height: 60rpx;
					border-radius: 100rpx;
				}
			}
		}
	}
	
	.bot-buy {
		position: absolute;
		bottom: 0;
		left: 0;
	}
	
	
	.cart-bottom {
		bottom: 0;
		z-index: 99;
		height: 120rpx;
		width: 100%;
		position: fixed;
		overflow: hidden;
		background: #FFFFFF;
		display: flex;
		align-items: center;
		justify-content: space-between;
		
		.sleep {
			width: 100%;
			height: 100%;
			background: #888888;
			text-align: center;
			line-height: 120rpx;
			color: #FFFFFF;
		}
		.cart {
			position: relative;
			margin-left: 84rpx;
			image {
				width: 60rpx;
				height: 60rpx;
			}
			text {
				display: inline-block;
				position: absolute;
				right: -10rpx;
				min-width: 30rpx;
				height: 30rpx;
				line-height: 30rpx;
				text-align: center;
				background: #FE4E00;
				border-radius: 50%;
				color: #FFFFFF;
				&.big {
					padding: 4rpx 8rpx;
					border-radius: 20rpx;
					right: -20rpx;
				}
				
			}
		}
		.cart-bgi {
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
		}
	
		.cart-icon {
			width: 100rpx;
			height: 100rpx;
			position: absolute;
			top: 20rpx;
			left: 54rpx;
		}
	
		.cart-nums {
			width: 48rpx;
			height: 48rpx;
			background-color: #4712AD;
			border-radius: 50%;
			position: absolute;
			top: 0;
			left: 124rpx;
			text-align: center;
			line-height: 48rpx;
			font-size: 32rpx;
			color: #fff;
			&.more {
				width: initial;
				padding: 0 10rpx;
				border-radius: 12rpx;
			}
		}
	
		.cart-price {
			position: absolute;
			top: 50%;
			left: 170rpx;
			font-size: 28rpx;
			color: #fff;
		}
		.submit {
			margin-right: 40rpx;
			font-size: 24rpx;
			font-weight: bold;
			color: #FFFFFF;
			width: 510rpx;
			height: 84rpx;
			text-align: center;
			line-height: 84rpx;
			background: #FE4E00;
			border-radius: 100rpx;
			
		}
	}
	
	
  </style>
