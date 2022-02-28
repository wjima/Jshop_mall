<template>
	<view class="content" v-if="cartData.list && cartData.list.length > 0">
		<view class="content-top">
			<view class="cell-group margin-cell-group">
				<view class="cell-item cell-item-mid right-img">
					<view class="cell-item-hd">
						<image class="cell-hd-icon" src="/static/image/homepage.png" style="width: 32upx;height: 32upx;"></image>
					</view>
					<view class="cell-item-bd">
						<text class="cell-bd-text">{{ shopName }}</text>
					</view>
					<view class="cell-item-ft">
						<text class="cell-bd-text" @click="editBtn" v-if="!editStatus">编辑</text>
						<text class="cell-bd-text" @click="editNoBtn" v-else>完成</text>
					</view>
				</view>
			</view>
			<view class="img-list cart-list">
				<checkbox-group class="cart-checkbox" v-for="(item, index) in cartData.list" :key="index" :val="item.id" @change="checkboxChange(item.id)">
					<view class="">
						<label class="uni-list-cell uni-list-cell-pd" v-show="item.type != 7">

							<view class="cart-checkbox-c">
								<checkbox color="#FF7159" :checked="item.is_select" :value="item.id" :disabled="item.stockNo" v-if="item.stockNo"
								 class="checkboxNo" />
								<checkbox color="#FF7159" :checked="item.is_select" :value="item.id" v-else />
							</view>
						</label>
						<view class="img-list-item">
							<image class="img-list-item-l little-img have-none" :src="item.products.image_path" mode="aspectFill"></image>
							<view class="img-list-item-r little-right">
								<view class="little-right-t">
									<view class="goods-name list-goods-name" @click="goodsDetail(item.products.goods_id)">
									<text class="gift" v-if="item.type == 7">[赠品]</text>{{ item.products.name }}
										</view>
									<view class="goods-price red-price" v-show="item.type != 7">￥{{ item.products.price || '0.00'}}</view>
								</view>
								<view class="romotion-tip" v-if="item.products.promotion_list">
									<!-- <view class="romotion-tip-item" v-for="(v, k) in item.products.promotion_list" :key="k" :class="v.type !== 2 ? 'bg-gray' : ''">
										{{ v.name }}
									</view> -->
									<view class="romotion-tip-item" v-for="(v, k) in item.products.promotion_list" :key="k">
										{{ v }}
									</view>
								</view>
								<view class="goods-item-c">
									<view class="goods-buy">
										<!-- 商品规格 -->
										<view class="goods-salesvolume" v-if="item.products.spes_desc">{{ item.products.spes_desc }}</view>
										<view class="goods-salesvolume" v-else></view>
										<view class="goods-numbox">
											<text v-if="item.stockNo && !editStatus" class="stockError">库存不足</text>
											<text v-else-if="item.stockTension && !editStatus" class="stockError stockTension">库存紧张</text>
											<uni-number-box v-on:change="bindChange($event, item)" :min="1" :max="item.maxStock" :value="item.nums"
											 :disabled="item.type == 7" v-if="!editStatus && item.type != 7"></uni-number-box>
											<view @click="del(index, item.id)" class="click-del" v-if="editStatus && item.type != 7">
												<image class="icon" src="/static/image/delete.png" mode=""></image>
											</view>
											<view v-if="item.type == 7" class="goods-num"> x {{item.nums}}</view>
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</checkbox-group>
			</view>
		</view>
		<view class="cart-bottom">
			<checkbox-group class="cart-checkbox" @change="checkboxAllButton">
				<label class="uni-list-cell uni-list-cell-pd">
					<view class="cart-checkbox-c">
						<checkbox :checked="checkboxAll" color="#FF7159" />
						全选
					</view>
				</label>
				<view class="cart-bottom-right">
					<view class="cart-bottom-right-t" v-if="!editStatus">
						合计：
						<view class="goods-price red-price">￥{{ cartData.amount }}</view>
					</view>
					<button class="btn btn-square btn-b" v-if="!editStatus" @click="settlement" hover-class="btn-hover2">去结算</button>
					<view v-else><button class="btn btn-square btn-b" @click="delList">删除</button></view>
				</view>
			</checkbox-group>
		</view>
	</view>
	<!-- 购物车为空 -->
	<view class="cart-none" v-else>
		<image class="cart-none-img" src="/static/image/car.png" mode=""></image>
		<view class="cart-none-t">购物车快饿瘪了 T.T</view>
		<view class="cart-none-m">快给我挑点宝贝吧</view>
		<navigator class="cart-none-b" url="/pages/index/index" hover-class="btn-hover" open-type="navigateTo">去逛逛</navigator>
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
	import uniNumberBox from '@/components/uni-number-box/uni-number-box.vue';
	import {
		goods
	} from '@/config/mixins.js';
	import {
		debounce
	} from '@/config/common.js'
	export default {
		mixins: [goods],
		data() {
			return {
				startX: 0, //开始坐标
				startY: 0,
				cartData: {}, //购物车数据
				cartIds: [], //选中ids
				checkboxAll: false, //全选按钮
				total: 0.0, //总价
				goSettlement: false, //去结算按钮
				cartId: '',
				cartNum: '',
				isLoad: false,
				cartNums: 0,
				editStatus: false
			};
		},
		components: {
			uniNumberBox
		},
		//页面加载
		onShow: function() {
			
			const _this = this
			
			let userToken = this.$db.get('userToken');
			if (userToken) {
				
				this.$api.GetcartidsFreePackage({}, res => {
					if(res.status) {
						_this.cartIds = res.data
						if(res.data.length) {
							_this.getCartData(res.data); //获取购物车数据
						} else {
							uni.showToast({
								title: '购物车还未添加商品~~~',
								icon: 'none'
							})
						}
					}
				})
			}
		},
		computed: {
			// 从vuex中获取店铺名称
			shopName() {
				return this.$store.state.config.shop_name;
			},
			goods_stocks_warn() {
				return this.$store.state.config.goods_stocks_warn;
			}
		},
		methods: {
			checkboxChange: function(e) {
				let _this = this;
				let id = e;
				let cartData = _this.cartData;
				for (let key in cartData.list) {
					if (cartData.list[key].id == id) {
						if (cartData.list[key].is_select == true) {
							cartData.list[key].is_select = false;
						} else {
							cartData.list[key].is_select = true;
						}
					}
				}
				_this.cartData = cartData;
				_this.setNumsData();
				_this.isAllCheckbox();
			},

			//数组转字符串
			arrayToStr: function(array) {
				return array.toString();
			},

			//获取购物车数据
			getCartData: function(cartIdsAll) {
				let _this = this;
				let cartIds = '';
				if(cartIdsAll){
					cartIds = _this.arrayToStr(cartIdsAll);
				}else{
					cartIds = _this.arrayToStr(_this.cartIds);
				}
			
				let data = {
					ids: cartIds,
					display: 'all'
				};
				data.order_type = 8
				this.$api.cartList(data, function(res) {
					if (res.status) {
						let data = res.data;
						_this.showHandle(data); //数量设置
					} else {
						uni.showToast({
							title: res.msg,
							icon: 'none'
						})
					}
				});
			},

			//渲染前配置数据
			showHandle: function(data, flag = true) {
				let _this = this;
				let goSettlement = false;
				for (let i in data.list) {
					//不可能购买0件
					if (data.list[i].nums < 1) {
						data.list[i].nums = 1;
					}
					//不能买大于库存的数量(库存不足)
					let stockNo = false;
					let maxStock = data.list[i].products.stock;
					if (data.list[i].nums > data.list[i].products.stock) {
						stockNo = true;
						maxStock = data.list[i].nums;
					}
					data.list[i].maxStock = maxStock;
					data.list[i].stockNo = stockNo;

					//库存紧张
					let stockTension = false;
					if (_this.goods_stocks_warn >= data.list[i].products.stock) {
						stockTension = true;
					}
					data.list[i].stockTension = stockTension;

					//设置样式
					data.list[i].minStatus = 'normal';
					data.list[i].maxStatus = 'normal';
					if (data.list[i].nums == 1) {
						data.list[i].minStatus = 'disabled';
					}
					if (data.list[i].nums == data.list[i].products.stock) {
						data.list[i].maxStatus = 'disabled';
					}

					//设置规格参数
					data.list[i].spes = [];
					if (data.list[i].products.spes_desc != null) {
						let spesArray = data.list[i].products.spes_desc.split(',');
						for (let key in spesArray) {
							let spesOne = spesArray[key].split(':');
							data.list[i].spes.push(spesOne[1]);
						}
					}
					//添加左滑效果
					data.list[i].isTouchMove = false;
					//是否可以去支付
					if (data.list[i].is_select) {
						goSettlement = true;
					}
					//id转换为字符串
					data.list[i].id = _this.arrayToStr(data.list[i].id);

					//选中状态
					if (flag) {
						if (data.list[i].is_select) {
							if (_this.cartIds.indexOf(data.list[i].id) < 0) {
								_this.cartIds.push(data.list[i].id);
							}
						}
					}
				}

				data.goods_pmt = _this.$common.formatMoney(data.goods_pmt, 2, '');
				data.order_pmt = _this.$common.formatMoney(data.order_pmt, 2, '');
				data.amount = _this.$common.formatMoney(data.amount, 2, '');
				let isLoad = false;
				if (data.list.length < 1) {
					isLoad = true;
				}
				let n = 0;
				for (let i in data.promotion_list) {
					n++;
				}
				// console.log(data.promotion_list);
				_this.goSettlement = goSettlement;
				_this.isLoad = isLoad;
				_this.cartNums = n;

				if (flag) {
					_this.cartData = data;
				} else {
					_this.getCartData();
				}

				_this.isAllCheckbox();
			},

			//是否全选
			isAllCheckbox: function() {
				let _this = this;
				let cartData = _this.cartData.list;
				let goSettlement = false;
				let flag = true;
				for (let key in cartData) {
					if (cartData[key].is_select == false && cartData[key].stockNo == false) {
						flag = false;
					}
					if (cartData[key].is_select == true) {
						goSettlement = true;
					}
				}
				if (cartData.length <= 0) {
					flag = false;
				}

				_this.checkboxAll = flag;
				_this.goSettlement = goSettlement;
			},

			//全选操作
			checkboxAllButton: function(e) {
				if (this.checkboxAll == true) {
					this.checkboxAll = false;
					this.setAllCheckbox(false);
				} else {
					this.checkboxAll = true;
					this.setAllCheckbox(true);
				}
			},

			//全选设置
			setAllCheckbox: function(e) {
				let _this = this;
				let cartData = _this.cartData;
				if (e) {
					//全选
					for (let key in cartData.list) {
						if (cartData.list[key].stockNo == false) {
							cartData.list[key].is_select = true;
						}
					}
				} else {
					//全不选
					for (let key in cartData.list) {
						cartData.list[key].is_select = false;
					}
				}

				_this.cartData = cartData;
				_this.setNumsData();
				_this.isAllCheckbox();
			},

			//设置刷新数据
			setNumsData: function() {
				let _this = this;
				let cartData = _this.cartData;
				let cartIds = [];
				for (let key in cartData.list) {
					if (cartData.list[key].is_select) {
						cartIds.push(cartData.list[key].id);
					}
				}
				_this.cartIds = cartIds;
				_this.cartData = cartData;
				if (cartIds.length == 0) {
					// console.log(111);
					let cartData = _this.cartData;
					// for (let k in cartData.promotion_list) {
					// 	cartData.promotion_list[k].type = 1;
					// }

					cartData.goods_pmt = '0.00';
					cartData.order_pmt = '0.00';
					cartData.amount = '0.00';
					_this.cartData = cartData;
				} else {
					_this.getCartData();
				}
			},

			//购物车数量调整
			bindChange(value, e) {
				delay(() => {
					let _this = this;
					let id = e.id;
					let num = value;
					let cartData = _this.cartData;
					let changeSelected = false;
					for (let key in cartData.list) {
						if (cartData.list[key].id == id) {
							if (num <= cartData.list[key].products.stock) {
								cartData.list[key].nums = num;
								changeSelected = true;
							}
						}
					}
					if (changeSelected) {
						_this.cartData = cartData;
						_this.cartId = id;
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
				// let _this = this;
				// let data = {
				// 	id: id,
				// 	nums: nums
				// };
				// _this.$api.setCartNum(data, function(res) {
				// 	if (_this.cartIds.indexOf(id) > -1) {
				// 		//_this.getCartData();
				// 		if (res.status) {
				// 			_this.$nextTick(function() {
				// 				_this.showHandle(res.data, false);
				// 			});
				// 		} else {
				// 			_this.$common.errorToShow(res.msg);
				// 		}
				// 	} else {
				// 		_this.$nextTick(function() {
				// 			_this.showHandle(res.data, false);
				// 		});
				// 	}
				// });
			},

			//删除事件
			del: function(index, ids) {

				let _this = this;
				// let cartid = id; //cart_id
				// console.log(cartid)
				// return
				//移除渲染
				// _this.cartData.list.splice(index, 1);
				// _this.cartData = _this.cartData;
				// _this.isLoad = true;

				//移除数据库
				let data = {
					ids,
					order_type : 8
				};
				_this.$api.removeCart(data, function(res) {
					if (res.status) {
						_this.$common.successToShow(res.msg);
					}
					// _this.setNumsData();
					// _this.isAllCheckbox();
					_this.getCartData()
				});
			},

			//收藏
			collection: function(e) {
				let _this = this;
				app.db.userToken(function(token) {
					let data = {
						goods_id: e.currentTarget.dataset.goodsid
					};
					app.api.goodsCollection(data, function(res) {
						for (let k in _this.cartData.list) {
							if (_this.cartData.list[k].products.goods_id == e.currentTarget.dataset.goodsid) {
								if (res.msg == '收藏成功') {
									_this.cartData.list[k].isCollection = true;
								} else {
									_this.cartData.list[k].isCollection = false;
								}
							}
						}
						wx.showToast({
							title: res.msg,
							complete: function() {
								setTimeout(function() {
									uni.hideToast();
								}, 1000);
							}
						});
					});
				});
			},

			//去结算
			settlement: function(e) {
				let _this = this;
				if (_this.goSettlement) {
					let cartData = _this.cartData.list;
					let newData = '';
					for (let key in cartData) {
						if (cartData[key].is_select == true && cartData[key].type != 7) {
							newData += ',' + cartData[key].id;
						}
					}
					if (newData.substr(0, 1) == ',') {
						newData = newData.substr(1);
					}
					if (newData.length > 0) {
						_this.$common.navigateTo('/pages/goods/place-order/index?order_type=8&cart_ids=' + JSON.stringify(newData));
						return true;
					} else {
						//没有选择不跳转
						this.$common.errorToShow("请先选择商品")
					}
				}else{
					this.$common.errorToShow("请先选择商品")
				}
			},

			//手指触摸动作开始 记录起点X坐标
			touchstart: function(e) {
				//开始触摸时 重置所有删除
				let _this = this;
				_this.cartData.list.forEach(function(v, i) {
					if (v.isTouchMove)
						//只操作为true的
						v.isTouchMove = false;
				});
				_this.setData({
					startX: e.changedTouches[0].clientX,
					startY: e.changedTouches[0].clientY,
					cartData: _this.cartData
				});
			},

			//滑动事件处理
			touchmove: function(e) {
				let _this = this;
				let index = e.currentTarget.dataset.index; //当前索引
				let startX = _this.startX; //开始X坐标
				let startY = _this.startY; //开始Y坐标
				let touchMoveX = e.changedTouches[0].clientX; //滑动变化坐标
				let touchMoveY = e.changedTouches[0].clientY; //滑动变化坐标
				let angle = _this.angle({
					X: startX,
					Y: startY
				}, {
					X: touchMoveX,
					Y: touchMoveY
				}); //获取滑动角度
				_this.cartData.list.forEach(function(v, i) {
					v.isTouchMove = false;
					//滑动超过30度角 return
					if (Math.abs(angle) > 30) return;
					if (i == index) {
						if (touchMoveX > startX)
							//右滑
							v.isTouchMove = false;
						//左滑
						else v.isTouchMove = true;
					}
				});
				//更新数据
				_this.setData({
					cartData: _this.cartData
				});
			},

			//计算滑动角度
			angle: function(start, end) {
				let _X = end.X - start.X,
					_Y = end.Y - start.Y;
				//返回角度 /Math.atan()返回数字的反正切值
				return (360 * Math.atan(_Y / _X)) / (2 * Math.PI);
			},
			//点击编辑
			editBtn: function() {
				this.editStatus = true;
			},
			//点击完成
			editNoBtn: function() {
				let _this = this;
				this.editStatus = false;
				let is_select = false;
				for (let i in _this.cartData.list) {
					if (_this.cartData.list[i].is_select) {
						is_select = true;
						break;
					}
				}
				if (is_select) {
					_this.getCartData();
				}
			},

			delList: function() {
				let _this = this;
				let ids = [];
				for (let k in _this.cartData.list) {
					if (_this.cartData.list[k].is_select) {
						ids += _this.cartData.list[k].id + ',';
					}
				}
				let data = {
					ids: ids
				};
				_this.$api.removeCart(data, function(res) {
					if (res.status) {
						_this.$common.successToShow(res.msg);
					}
					_this.setNumsData();
					_this.isAllCheckbox();
				});
			}
		}
	};
</script>
<style>
	.cell-item-hd {
		max-width: 40upx;
		min-width: 40upx;
	}

	.margin-cell-group {
		margin: 0 0 2upx 0;
	}

	.little-right .goods-salesvolume {
		float: none;
	}

	.cart-bottom {
		bottom: 0;
		z-index: 99;
		height: 90upx;
		width: 100%;
		background-color: #fff;
		position: fixed;

		overflow: hidden;
		box-shadow: 0 0 20upx #ccc;
	}

	.cart-bottom-right {
		height: 90upx;
		float: right;
		overflow: hidden;
	}

	.cart-bottom-right-t {
		display: inline-block;
		height: 100%;
		line-height: 90upx;
		margin-right: 20upx;
		font-size: 28upx;
		color: #666;
	}

	.cart-bottom-right-t .red-price {
		float: none;
	}

	.btn-square {
		float: right;
	}

	.cart-bottom .cart-checkbox-c {
		color: #333;
		font-size: 30upx;
	}

	.cart-none {
		text-align: center;
		padding: 200upx 0;
	}

	.cart-none-img {
		width: 252upx;
		height: 228upx;
		margin-bottom: 40upx;
	}

	.cart-none-t {
		color: #666;
		font-size: 28upx;
	}

	.cart-none-m {
		color: #666;
		font-size: 28upx;
		margin-bottom: 40upx;
	}

	.cart-none-b {
		display: inline-block;
		padding: 16upx 40upx;
		font-size: 30upx;
		color: #666;
		background-color: #e3e3e3;
	}

	.stockError {
		font-size: 12px;
		color: #ffffff;
		background-color: #ff7159;
		padding: 1px 3px;
		border-radius: 3px;
	}

	.stockTension {
		background-color: #ffc107;
	}

	/* #ifdef MP-ALIPAY */
	label {
		display: block;
	}

	/* #endif */
	.click-del {
		overflow: hidden;
		height: 52upx;
	}

	.click-del .icon {
		float: right;
	}

	.right-img .cell-item-ft {
		right: 26rpx;
	}

	/* /deep/ .uni-numbox--disabled {
	visibility: hidden !important;
	display: inline-block !important;
} */
</style>
