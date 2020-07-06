<template>
	<form class="content" @submit="toPay" report-submit="true">
		<view class="content-top">
			<uni-segmented-control :current="type_current" :values="type_items" @clickItem="onTypeItem" style-type="text"
			 active-color="#333" v-if="storeSwitch == 1"></uni-segmented-control>
			<view class="content">
				<view v-show="type_current === 0">
					<!-- 收货地址信息 -->
					<view class="cell-group margin-cell-group" v-if="userShip && userShip.id" @click="showAddressList">
						<view class="cell-item add-title-item right-img cell-item-mid">
							<view class="cell-item-hd">
								<image class="cell-hd-icon" src="/static/image/location.png"></image>
							</view>
							<view class="cell-item-bd cell-item-bd-block">
								<view class="cell-bd-view">
									<text class="cell-bd-text">收货人：{{ userShip.name || '' }}</text>
									<text class="cell-bd-text-right">{{ userShip.mobile || '' }}</text>
								</view>
								<view class="cell-bd-view">
									<text class="cell-bd-text address">{{ userShip.area_name || '' }} {{ userShip.address || '' }}</text>
								</view>
							</view>
							<view class="cell-item-ft">
								<image class="cell-ft-next icon" src="/static/image/right.png"></image>
							</view>
						</view>
					</view>
					<view class="cell-group margin-cell-group" v-else>
						<view class="cell-item add-title-items"><button class="btn btn-b" @click="goAddress()" hover-class="btn-hover2">添加收货地址</button></view>
					</view>
				</view>
				<view v-show="type_current === 1">
					<!-- 门店信息 -->
					<view v-if="store && store.id && store.id != 0" class="cell-group margin-cell-group" @click="goStorelist()">
						<view class="cell-item add-title-item right-img cell-item-mid">
							<view class="cell-item-hd">
								<image class="cell-hd-icon" src="/static/image/homepage.png"></image>
							</view>
							<view class="cell-item-bd cell-item-bd-block">
								<view class="cell-bd-view">
									<text class="cell-bd-text">{{ store.name || '' }}</text>
									<text class="cell-bd-text-right">{{ store.mobile || '' }}</text>
								</view>
								<view class="cell-bd-view">
									<text class="cell-bd-text address">{{ store.address || '' }}</text>
								</view>
							</view>
							<view class="cell-item-ft">
								<image class="cell-ft-next icon" src="/static/image/right.png"></image>
							</view>
						</view>
					</view>
					<view v-else class="cell-group margin-cell-group">
						<view class="cell-item add-title-item right-img no-store cell-item-bd-block">暂无门店</view>
					</view>
				</view>
			</view>

			<view class="cell-group margin-cell-group" v-if="storeSwitch == 1 && type_current === 1">
				<view class="cell-item user-head cell-item-mid">
					<view class="cell-item-hd">
						<view class="cell-hd-title">姓名</view>
					</view>
					<view class="cell-item-bd" style="width:88%;">
						<input class="cell-bd-input" style="width: 100%;" placeholder="请输入提货人姓名" v-model="store_pick.name" />
					</view>
				</view>
				<view class="cell-item cell-item-mid">
					<view class="cell-item-hd">
						<view class="cell-hd-title">电话</view>
					</view>
					<view class="cell-item-bd" style="width: 88%;">
						<input class="cell-bd-input" placeholder="请输入提货人电话" v-model="store_pick.mobile" style="width:100%;" />
					</view>
				</view>
			</view>

			<!-- 商品列表信息 -->
			<view class="img-list">
				<view class="img-list-item" v-if="item.is_select == true" v-for="(item, index) in products" :key="index">
					<image class="img-list-item-l little-img have-none" :src="item.products.image_path" mode="aspectFill"></image>
					<view class="img-list-item-r little-right">
						<view class="little-right-t">
							<view class="goods-name list-goods-name" @click="goodsDetail(item.products.goods_id)">{{ item.products.name || '' }} <text class="gift" v-if="item.type == 7">[赠品]</text></view>
							<view class="goods-price" v-if="item.type != 7">￥{{ item.products.price || '' }}</view>
						</view>
						<view class="romotion-tip" v-if="item.products.promotion_list">
							<!-- <view class="romotion-tip-item" :class="v.type !== 2 ? 'bg-gray' : ''" v-for="(v, k) in item.products.promotion_list"
							 :key="k">{{ v.name || '' }}
							</view> -->
							<view class="romotion-tip-item" v-for="(v, k) in item.products.promotion_list" :key="k">{{ v || '' }}
							</view>
						</view>
						<view class="goods-item-c">
							<view class="goods-buy">
								<view class="goods-salesvolume" v-if="item.products.spes_desc !== null">{{ item.products.spes_desc || '' }}</view>
								<view class="goods-num">× {{ item.nums || '' }}</view>
							</view>
						</view>
					</view>
				</view>
			</view>

			<view class="cell-group">
				<!-- 订单促销信息 -->
					<view class="cell-item" v-if="cartData.promotion_list && cartData.promotion_list.length > 0">
						<view class="cell-item-hd">
							订单优惠
						</view>
						<view class="cell-item-ft">
							<view class="promotion-item" v-for="(v, k) in cartData.promotion_list" :key="k">
								{{ v || '' }}
							</view>
						</view>
					</view>
				<view class="cell-item">
					<view class="cell-item-hd">
						<view class="cell-hd-title" style="min-width: 100rpx;">优惠券</view>
					</view>
					<view class="cell-item-ft">
						<text class="cell-ft-p" @click="toshow()">{{ usedCouponsCompute || '' }}</text>
					</view>
				</view>

				<!-- 商户开启积分 并且用户有积分情况下 -->
				<view class="cell-item add-title-item right-img" v-if="isOpenPoint === 1 && userPointNums > 0">
					<view class="cell-item-bd" style="margin-left:0 ;">
						<view class="cell-bd-view">积分抵扣</view>
						<view class="cell-bd-view" style="margin-bottom: 4rpx;">
							<text class="cell-bd-text address color-9">
								可用 {{ canUsePoint || '' }} 积分，可抵扣 {{ pointMoney || '' }} 元，共有 {{ userPointNums || '' }} 积分。
							</text>
						</view>
					</view>
					<view class="cell-item-ft" @click="changePointHandle">
						<label class="radio">
							<radio value="1" :checked="isUsePoint" color="#FF7159" />
							<!-- <radio class="radioNo" disabled="true"/> -->
						</label>
					</view>
				</view>

				<view class="cell-item invoice right-img" v-if="invoiceSwitch == 1">
					<view class="cell-item-hd">
						<view class="cell-hd-title">发票</view>
					</view>
					<view class="cell-item-ft" @click="goInvoice()"> 
						<text class="cell-bd-input">{{ invoice.name || '' }}</text>
						<image class="cell-ft-next icon" src="/static/image/right.png"></image>
					</view>
				</view>

				<view class="cell-item">
					<view class="cell-item-hd cell-item-bd-block">
						<view class="cell-bd-view">商品价格</view>
						<view class="cell-bd-view" v-if="cartData.goods_pmt_old > 0">商品优惠</view>
						<view class="cell-hd-view" v-if="cartData.order_pmt_old > 0">订单优惠</view>
						<!-- <view class="cell-hd-view" v-if="!couponIsUsed">优惠券抵扣</view> -->
						<view class="cell-hd-view" v-if="cartData.point > 0">积分抵扣</view>
						<view class="cell-hd-view">运费</view>
					</view>
					<view class="cell-item-ft cell-item-bd-block">
						<view class="cell-ft-view red-price">{{ cartData.goods_amount || '' }}</view>
						<view class="cell-ft-view" v-if="cartData.goods_pmt_old > 0">-{{ cartData.goods_pmt || '' }}</view>
						<view class="cell-ft-view" v-if="cartData.order_pmt_old > 0">-{{ cartData.order_pmt || '' }}</view>
						<!-- <view class="cell-ft-view" v-if="!couponIsUsed">-{{ cartData.coupon_pmt || '' }}</view> -->
						<view class="cell-ft-view" v-if="cartData.point > 0">-{{ cartData.point_money || '' }}</view>
						<view class="cell-ft-view">{{ cartData.cost_freight || '' }}</view>
					</view>
				</view>
			</view>

			<view class="cell-group leave-message">
				<view class="cell-item right-img">
					<view class="cell-item-hd">
						<view class="cell-hd-title">买家留言</view>
					</view>
				</view>
				<view class="cell-textarea ">
					<!-- #ifdef MP-WEIXIN -->
					<input v-model="memo" placeholder="50字以内(选填)" />
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<textarea v-model="memo" placeholder="50字以内(选填)" maxlength="50" />
					<!-- #endif -->
				</view>
			</view>
		</view>

		<!-- 优惠券信息 -->
		<lvv-popup position="bottom" ref="lvvpopref">
			<view style="width: 100%;height: 700upx;background: #F8F8F8;;position: absolute;left:0;bottom: 0;">
				<view class="pop-c">
					<!-- <view class="pop-t">
                        <view class='cell-item invoice'>
                            <view class='cell-item-hd'>
                                <view class='cell-hd-title'>优惠券</view>
                            </view>
                            <view class='cell-item-ft'
                            @click="toclose()">
                                <image class='cell-ft-next icon' src='/static/image/close.png'></image>
                            </view>
                        </view>
                    </view> -->

					<view class="pop-b">
						<view class="pop-b-t">
							<uni-segmented-control :current="current" :values="items" @clickItem="onClickItem" style-type="text" active-color="#333"></uni-segmented-control>
						</view>
						<view class="" v-show="current === 0">
							<scroll-view class="coupon-c" scroll-y="true" style="" v-if="userCoupons.length">
								<view class="coupon-c-item" v-for="(item, index) in userCoupons" :key="index">
									<view :class="item.cla"><view class="cci-l-c color-f">coupon</view></view>

									<view class="cci-r">
										<!-- <image class="cci-r-img" src="" mode=""></image> -->
										<view class="cci-r-c">
											<view class="ccirc-t color-9">{{ item.name || '' }}</view>
											<view class="ccirc-b">
												<view class="ccirc-b-l">
													<view class="ccirc-b-tip">{{ item.expression1 + item.expression2 }}</view>
													<view class="ccirc-b-time color-9">有效期：{{ item.stime + ' - ' + item.etime }}</view>
												</view>
												<view class="ccirc-b-r color-f" @click="couponHandle(index)" v-if="!item.checked && !item.disabled">立即使用</view>
												<view class="ccirc-b-r color-f" @click="couponHandle(index)" v-else-if="item.checked && !item.disabled">取消使用</view>
												<!-- <view class="ccirc-b-r color-f bg-c">
													不可用
												</view> -->
											</view>
										</view>
									</view>
								</view>
							</scroll-view>
							<view class="coupon-none" v-else><image class="coupon-none-img" src="/static/image/order.png" mode=""></image></view>
						</view>

						<view class="coupon-c" v-show="current === 1">
							<scroll-view class="coupon-c" scroll-y="true">
								<view class="coupon-enter">
									<view class="coupon-input"><input type="text" v-model="inputCouponCode" placeholder="请输入优惠券码" /></view>

									<!-- #ifdef MP-WEIXIN -->
									<!-- <view class="coupon-code">
									<image src="/static/image/ewm.png" class="icon" mode="" @click="scanCode"></image>
								</view> -->
									<!-- #endif -->

									<view class="coupon-enter-btn" @click="useInputCouponCode"><button class="btn btn-b">确认</button></view>
								</view>
								<view class="coupon-c-item" v-for="(item, index) in inputCouArr" :key="index">
									<view class="cci-l"><view class="cci-l-c color-f">coupon</view></view>

									<view class="cci-r">
										<view class="cci-r-c">
											<view class="ccirc-b" style="padding: 20rpx 10rpx;">
												<view class="ccirc-b-l">
													<view class="ccirc-b-tip" style="margin-bottom: 40rpx;">{{ item.name || '' }}</view>
													<view class="ccirc-b-time color-9">{{ item.code }}</view>
												</view>
												<view class="ccirc-b-r color-f" @click="removeCouponCode1(item.code)">取消使用</view>
											</view>
										</view>
									</view>
								</view>
								<!-- <view class="coupon-list" v-for="(item,index) in inputCouArr" :key="index">
									<view class="cci-l-c color-f">
										coupon
									</view>
									<view class="coupon-item">
										<view class="color-6 fsz28 coupon-name">
											{{item.name}}
										</view>
										<view class="color-9 fsz24 coupon-code1">
											{{item.code}}
										</view>
										<view class="delete" @click="removeCouponCode(item.code)">
											<image src="/static/image/del.png" mode=""></image>
										</view>
									</view>
								</view> -->
							</scroll-view>
						</view>
					</view>
					<view class="button-bottom">
						<button class="btn btn-square" @click="notUseCoupon()">不使用优惠卷</button>
						<button class="btn btn-square btn-b" @click="toclose()">确定</button>
					</view>
				</view>
			</view>
		</lvv-popup>

		<view class="button-bottom">
			<view class="button-bottom-c">
				<view class="button-bottom-c-t">共 {{ productNums || '' }} 件商品</view>
				<view class="button-bottom-c-b">
					合计
					<text class="red-price">{{ cartData.amount || '' }}</text>
				</view>
			</view>
			<button class="btn btn-square btn-b" hover-class="btn-hover2" form-type="submit" :disabled="submitStatus" :loading="submitStatus">立即支付</button>
		</view>
	</form>
</template>

<script>
import lvvPopup from '@/components/lvv-popup/lvv-popup.vue';
import uniSegmentedControl from '@/components/uni-segmented-control/uni-segmented-control.vue';
import { goods } from '@/config/mixins.js';
export default {
	mixins: [goods],
	data() {
		return {
			type_items: ['快递配送', '门店自提'], //门店自提切换
			type_current: 0,
			cartData: {}, // 购物车商品详情
			products: [], // 货品信息
			promotions: [], // 促销信息
			userShip: {}, // 用户收货地址
			receiptType: 1, // 订单类型 1是发货订单 2是门店自提订单
			params: {
				ids: 0, // 传递过来的购物车id
				area_id: 0, // 收货地址id
				coupon_code: '', // 优惠券码列表(string)多张逗号分隔
				point: 0, // 抵扣积分额
				type: 1, //购物车类型
				params:''//购物车扩展信息
			}, // 监听params参数信息 以重新请求接口
			// 发票信息
			invoice: {
				type: '1', // 类型 1不开发票 2个人发票 3公司发票
				name: '不开发票', // 发票抬头
				code: '' // 发票税号
			},
			memo: '', // 买家留言
			items: ['选择优惠券', '输入券码'],
			orderType: 1, // 商品订单类型 1
			current: 0,
			isUsePoint: false, // 是否勾选使用积分
			userPointNums: 0, // 用户的总积分
			canUsePoint: 0, // 可以使用的积分
			pointMoney: '', // 积分抵扣的金额
			userCoupons: [], // 用户的可用优惠券列表
			usedCoupons: {}, // 已经选择使用的优惠券
			inputCouponCode: '', // 输入的优惠券码
			optCoupon: '', // 当前选择使用的优惠券(暂存使用 如果接口返回不可用则剔除优惠券状态)
			store: {
				id: 0,
				name: '',
				mobile: '',
				address: ''
			},
			store_pick: {
				name: '',
				mobile: ''
			},
			team_id: 0, //拼团id
			submitStatus: false,
			inputCouArr: [], //输入的优惠券码的列表
			clickIndex: Number ,//当前点击的优惠券
			bargain_id: 0, //砍价活动id
			record_id: 0 ,//砍价活动id
			group_id:0,//团购活动id
		};
	},
	components: { lvvPopup, uniSegmentedControl },
	onLoad(options) {
		let cartIds = options.cart_ids;
		if (options.order_type) {
			this.params.order_type = options.order_type;
		}
		if (options.team_id) {
			this.team_id = options.team_id;
		}
		//砍价活动id
		if (options.bargain_id) {
			this.bargain_id = options.bargain_id;
		}
		//发起人砍价活动id
		if (options.record_id) {
			this.record_id = options.record_id;
		}
		//团购活动id
		if(options.group_id){
			this.group_id = options.group_id
			this.params.params = JSON.stringify({ group_id: options.group_id})
		}
		this.params.ids = JSON.parse(cartIds);
		if (!this.params.ids) {
			this.$common.successToShow('获取失败', function() {
				uni.navigateBack({
					delta: 1
				});
			});
		}
		// 获取用户的默认收货地址信息
		this.userDefaultShip();

		// 获取用户的可用优惠券信息
		this.getUserCounpons();

		//获取默认门店信息
		this.getDefaultStore();
	},
	onShow() {
		// // #ifdef MP-ALIPAY || MP-TOUTIAO
		// let user_ship = this.$db.get('address_user_ship', true);
		// if (user_ship) {
		// 	this.userShip = user_ship;
		// 	this.params.area_id = user_ship.area_id;
		// 	this.$db.del('address_user_ship', true);
		// }
		// let user_invoice = this.$db.get('user_invoice', true);
		// if (user_invoice) {
		// 	this.invoice = user_invoice;
		// 	this.$db.del('user_invoice', true);
		// }
		// let user_store = this.$db.get('user_store', true);
		// if (user_store) {
		// 	this.store = user_store;
		// 	this.$db.del('user_store', true);
		// }
		// // #endif
		// // #ifdef H5 || APP-PLUS || APP-PLUS-NVUE
		// let user_ship = this.$store.state.userShip;
		// // console.log(user_ship);
		// if (user_ship) {
		// 	this.userShip = user_ship;
		// 	this.params.area_id = user_ship.area_id;
		// }
		// let user_invoice = this.$store.state.invoice;
		// if (user_invoice) {
		// 	this.invoice = user_invoice;
		// }
		// // #endif
		let user_ship = this.$db.get('address_user_ship', true);
		if (user_ship) {
			this.userShip = user_ship;
			this.params.area_id = user_ship.area_id;
			this.$db.del('address_user_ship', true);
		}
		let user_invoice = this.$db.get('user_invoice', true);
		if (user_invoice) {
			this.invoice = user_invoice;
			this.$db.del('user_invoice', true);
		}
		let user_store = this.$db.get('user_store', true);
		if (user_store) {
			this.store = user_store;
			this.$db.del('user_store', true);
		}
		if(this.$store.state.shopAddress.id) {
			this.store = this.$store.state.shopAddress
		}
	},
	methods: {
		// 切换门店
		onTypeItem(index) {
			if (this.type_current !== index) {
				this.type_current = index;
			}
			let receiptType = 1;
			if (this.type_current != 0) {
				receiptType = 2;
			}
			this.receiptType = receiptType;

			this.getCartList();
		},
		// 跳转到门店列表
		goStorelist() {
			uni.navigateTo({
				url: './storelist'
			});
		},
		// 没有收货地址时跳转
		goAddress() {
			uni.navigateTo({
				url: '/pages/member/address/list?type=order'
			});
		},
		// 获取用户的默认收货地址
		userDefaultShip() {
			this.$api.userDefaultShip({}, res => {
				if (res.status && Object.keys(res.data).length) {
					this.userShip = res.data;
					this.params.area_id = this.userShip.area_id;
				}
			});
		},
		// 获取购物车商品详情
		getCartList() {
			let data = this.params;
			data['receipt_type'] = this.receiptType; // 区分订单类型  1快递订单 2门店自提订单

			this.$api.cartList(data, res => {
				if (res.status) {
					let data = res.data;
					// 判断是否开启积分抵扣 并且 没有勾选积分使用
					if (this.isOpenPoint === 1 && !this.isUsePoint) {
						let money = {
							order_money: data.amount
						};
						this.$api.usablePoint(money, res => {
							if (res.status) {
								this.userPointNums = res.data; // 用户总积分
								this.canUsePoint = res.available_point; // 可以使用的积分
								this.pointMoney = res.point_rmb; // 积分抵扣的总金额
							}
						});
					}

					// 所有价格转换
					data.amount = this.$common.formatMoney(data.amount);
					//商品金额=商品优惠后金额+商品优惠金额
					let goods_amount = this.$common.moneySum(data.goods_amount,data.goods_pmt);

					data.goods_amount = this.$common.formatMoney(goods_amount);
					data.goods_pmt_old = data.goods_pmt;
					data.goods_pmt = this.$common.formatMoney(data.goods_pmt);
					data.coupon_pmt = this.$common.formatMoney(data.coupon_pmt);
					data.order_pmt_old = data.order_pmt;
					data.order_pmt = this.$common.formatMoney(data.order_pmt);
					data.point_money = this.$common.formatMoney(data.point_money);
					data.cost_freight = this.$common.formatMoney(data.cost_freight);

					// 购物车详情
					this.cartData = data;
					// 商品详情
					this.products = data.list;
					//判断是否有库存
					let noStock = true;
					for (let i = 0; i < data.list.length; i++) {
						if (data.list[i].is_select) {
							noStock = false;
						}
					}
					if (noStock) {
						this.$common.errorToShow('您所挑选的商品已售罄，请重新添加哦');
					}
					this.userCoupons.forEach(item => {
						if (item.checked1) {
							item.checked = item.checked1;
						}
					});
					// 优惠信息
					this.promotions = data.promotion_list;
					// 使用的优惠券信息
					this.usedCoupons = data.coupon;
					// 优惠券码
					if (this.inputCouponCode) {
						let item = {};
						for (let key in this.usedCoupons) {
							item.code = key;
							item.name = this.usedCoupons[key];
						}
						this.inputCouArr.push(item);
					}
					// 手动输入的优惠券使用成功后关闭弹窗并清除输入的优惠券码
					// this.current === 1 && this.$refs.lvvpopref.popshow && this.inputCouponCode ? this.toclose() : ''
					this.current === 1 && this.$refs.lvvpopref.popshow;

					this.inputCouponCode = '';

					this.optCoupon = '';
				} else {
					this.$common.errorToShow(res.msg, () => {
						// this.userCoupons[this.clickIndex].checked1=false
						// this.userCoupons.forEach(item=>{
						// 	if(item.coupon_code==this.optCoupon){
						// 		item.checked1=false;
						// 		item.checked=item.checked1;
						// 	}
						// })
						// 优惠券不可用状态判断
						// 优惠券号码不存在 			15009
						// 优惠券未开始				15010
						// 优惠券已使用				15013
						// 优惠券不符合使用规则		15014
						// 优惠券不可使用多张			15015
						//同一类优惠券只能用一张  11255
						// let errStatus = [15009, 15010, 15013, 15014, 15015,11255]
						// if (errStatus.indexOf(res.data) !== -1) {
						// 删除使用的优惠券号码
						if (this.current === 1) {
							this.removeCouponCode(this.inputCouponCode, this.current);
						} else {
							// 取消选择使用的状态
							if (this.optCoupon) {
								this.userCoupons.forEach(item => {
									if (item.coupon_code === this.optCoupon) {
										item.checked1 = false;
									}
								});
							}
							this.removeCouponCode(this.optCoupon, this.current);
						}
						// }
					});
				}
			});
		},
		// 获取用户可用的优惠券信息
		getUserCounpons() {
			let data = {
				display: 'no_used'
			};
			this.$api.userCoupon(data, res => {
				if (res.status) {
					let _list = res.data.list;
					let nowTime = Math.round(new Date().getTime() / 1000).toString();
					_list.forEach(item => {
						this.$set(item, 'checked', false);
						// 判断优惠券是否有效(开始时间)
						this.$set(item, 'disabled', item.start_time > nowTime ? true : false);
						this.$set(item, 'cla', item.disabled ? 'cci-l bg-c' : 'cci-l'); // 绑定相应的class样式
					});
					this.userCoupons = _list;
				}
			});
		},
		// 点击使用/取消优惠券操作
		couponHandle(index) {
			this.clickIndex = index;
			// 更改使用/取消状态
			// this.userCoupons[index].checked = !this.userCoupons[index].checked
			this.userCoupons[index].checked1 = !this.userCoupons[index].checked;
			// 暂存当次选中使用的优惠券key
			this.optCoupon = this.userCoupons[index].coupon_code;
			let arr = [];
			this.userCoupons.forEach(item => {
				if (item.checked1) {
					arr.push(item.coupon_code);
				}
			});
			if (this.userCoupons[index].checked1) {
				// this.params.coupon_code = arr.join()
				if (this.params.coupon_code) {
					// 正在使用的优惠
					// console.log(this.params.coupon_code);
					// console.log(this.optCoupon);
					this.params.coupon_code += ',' + this.optCoupon;
					// console.log(this.params.coupon_code);
					// this.params.coupon_code += ','+arr.join()
					// let all= (this.params.coupon_code + ','+arr.join()).split(",")
					// console.log(all);
					// let arr1=[]
					// for(let i=0;i<all.length;i++){
					// 	if(arr1.indexOf(all[i]) == -1){
					// 		// console.log(all[i]);
					// 		arr1.push(all[i])
					// 	}
					// }
					// console.log(arr1);
					// this.params.coupon_code=arr1.join()
				} else {
					this.params.coupon_code = arr.join();
				}
			} else {
				// 取消使用
				let paramsCodes = this.params.coupon_code.split(',');
				let usedIndex = paramsCodes.indexOf(this.userCoupons[index].coupon_code);
				if (usedIndex !== -1) {
					paramsCodes.splice(usedIndex, 1);
					this.params.coupon_code = paramsCodes.join();
				}
				this.userCoupons[index].checked = false;
			}
		},
		// 手输的优惠券码使用
		useInputCouponCode() {
			if (!this.inputCouponCode) {
				this.$common.errorToShow('请输入优惠券码');
			} else {
				// 判断是否有使用的优惠券
				if (this.params.coupon_code.length > 0) {
					this.params.coupon_code += ',' + this.inputCouponCode;
				} else {
					this.params.coupon_code = this.inputCouponCode;
				}
			}
		},
		// 不使用优惠券
		notUseCoupon() {
			this.toclose();
			this.inputCouponCode = ''; // 清空手输的优惠券码
			this.userCoupons.forEach(item => {
				item.checked = false;
				item.checked1 = false;
			}); // 取消所有选中的使用状态
			this.params.coupon_code = ''; // 清空params优惠券码
			this.inputCouArr = []; //清空优惠券码列表
		},
		// 移除/取消使用中的指定优惠券
		removeCouponCode(code, current) {
			let arr = this.params.coupon_code.split(',');
			arr.splice(arr.indexOf(code), 1);
			current === 0 ? (this.optCoupon = '') : (this.inputCouponCode = '');
			this.params.coupon_code = arr.join();

			// let inputCouArr=[]
			// this.inputCouArr.forEach(item=>{
			// 	if(item.code!=code){
			// 		inputCouArr.push(item)
			// 	}
			// })
			// this.inputCouArr=inputCouArr
		},
		// 取消使用中的优惠券码
		removeCouponCode1(code) {
			console.log(code);
			let arr = this.params.coupon_code.split(',');
			arr.splice(arr.indexOf(code), 1);
			this.params.coupon_code = arr.join();
			let inputCouArr = [];
			this.inputCouArr.forEach(item => {
				if (item.code != code) {
					inputCouArr.push(item);
				}
			});
			this.inputCouArr = inputCouArr;
		},
		// 是否使用积分
		changePointHandle() {
			if (this.userPointNums > 0) {
				this.isUsePoint = !this.isUsePoint;
				this.params.point = this.isUsePoint ? this.canUsePoint : 0;
			}
		},
		// 显示modal弹出框
		toshow() {
			this.$refs.lvvpopref.show();
		},
		// 关闭modal弹出框
		toclose() {
			this.$refs.lvvpopref.close();
		},
		// 去支付
		toPay(e) {
			if (this.submitStatus) {
				return false;
			}
			this.submitStatus = true;
			let receiptType = 1;
			if (this.type_current != 0) {
				receiptType = 2;
			}
			this.receiptType = receiptType;

			let data = {
				cart_ids: this.params.ids,
				memo: this.memo,
				coupon_code: this.params.coupon_code,
				point: this.params.point,
				receipt_type: this.receiptType
			};
			data['order_type'] = this.params.order_type; //订单类型
			if (this.team_id != 0) {
				data['params'] = JSON.stringify({ team_id: this.team_id }); //团id
			}
			//砍价
			if (this.bargain_id != 0) {
				data['params'] = JSON.stringify({ bargain_id: this.bargain_id, record_id: this.record_id }); //砍价信息
			}
			//团购秒杀
			if(this.group_id!=0){
				data['params'] = JSON.stringify({ group_id: this.group_id}); //团购秒杀信息
			}
			let delivery = {};
			// 判断是快递配送还是门店自提
			if (this.receiptType == 1) {
				if (!this.userShip.id || !this.params.area_id) {
					this.$common.errorToShow('请选择收货地址');
					this.submitStatus = false;
					return false;
				}
				// 快递配送
				delivery = {
					uship_id: this.userShip.id,
					area_id: this.params.area_id
				};
			}
			if (this.receiptType == 2) {
				if (!this.store.id) {
					this.$common.errorToShow('请选择自提门店');
					this.submitStatus = false;
					return false;
				}
				if (!this.store_pick.name) {
					this.$common.errorToShow('请输入提货人姓名');
					this.submitStatus = false;
					return false;
				}
				if (!this.store_pick.mobile) {
					this.$common.errorToShow('请输入提货人电话');
					this.submitStatus = false;
					return false;
				}
				// 门店自提
				delivery = {
					store_id: this.store.id,
					lading_name: this.store_pick.name,
					lading_mobile: this.store_pick.mobile
				};
			}

			// 发票信息
			data['tax_type'] = this.invoice.type;
			data['tax_name'] = this.invoice.name;
			data['tax_code'] = this.invoice.code;

			// #ifdef H5
			data['source'] = 2;
			// #endif
			// #ifdef MP-WEIXIN
			data['source'] = 3;
			data['formId'] = e.detail.formId;
			// #endif
			// #ifdef MP-ALIPAY
			data['source'] = 4;
			// #endif
			// #ifdef APP-PLUS || APP-PLUS-NVUE
			data['source'] = 5;
			// #endif
			// #ifdef MP-TOUTIAO
			data['source'] = 6;
			// #endif
			data = Object.assign(data, delivery);
			this.$api.createOrder(
				data,
				res => {
					if (res.status) {
						// 创建订单成功 去支付
						// this.submitStatus = false;
						// 判断是否为0元订单,如果是0元订单直接支付成功
						if (res.data.pay_status == '2') {
							this.$common.redirectTo('/pages/goods/payment/result?order_id=' + res.data.order_id);
						} else {
							this.$common.redirectTo('/pages/goods/payment/index?order_id=' + res.data.order_id + '&type=' + this.orderType);
						}
					} else {
						this.$common.errorToShow(res.msg);
						// this.submitStatus = false;
					}
				},
				res => {
					this.submitStatus = false;
				}
			);
		},
		// 跳转发票页面
		goInvoice() {
			this.$common.navigateTo('./invoice');
		},
		// 跳转我的收货地址列表
		showAddressList() {
			this.$common.navigateTo('/pages/member/address/list?type=order');
		},
		// tab点击切换
		onClickItem(index) {
			if (this.current !== index) {
				this.current = index;
			}
		},
		// 获取默认店铺
		getDefaultStore() {
			if (this.storeSwitch == 1) {
				this.$api.defaultStore({}, res => {
					if (res.status) {
						if (res.data && res.data.id) {
							let store = {
								id: res.data.id || 0,
								name: res.data.store_name || '',
								mobile: res.data.store_mobile || '',
								address: res.data.all_address || ''
							};
							this.store = store;
						} else {
							this.$common.errorToShow('商家未配置默认自提店铺！');
						}
					}
				});
			}
		}
		//扫码
		// 		scanCode() {
		// 			console.log(1);
		// 			uni.scanCode({
		// 				success(res) {
		// 					console.log(2);
		// 					console.log(res)
		// 				},
		// 				complete(res){
		// 					console.log(3);
		// 					console.log(res)
		// 				},
		// 				fail(res) {
		// 					console.log(4);
		// 					console.log(res)
		// 				}
		// 			})
		// 		}
	},
	computed: {
		// 计算购物车商品数量
		productNums() {
			let nums = 0;
			for (let i in this.cartData.list) {
				if (this.cartData.list[i].is_select) {
					nums += this.cartData.list[i].nums;
				}
			}
			return nums;
		},
		// 判断商户是否开启积分抵扣 1开启 2未开启
		isOpenPoint() {
			return this.$store.state.config.point_switch;
		},
		// 获取使用的优惠券名称
		usedCouponsCompute() {
			let name = '未使用';
			if (Object.keys(this.usedCoupons).length) {
				let coupons = [];
				for (let i in this.usedCoupons) {
					coupons.push(this.usedCoupons[i]);
				}
				name = coupons.join();
			}
			return name;
		},
		// 判断是否开启发票功能
		invoiceSwitch() {
			return this.$store.state.config.invoice_switch || 2;
		},
		// 判断店铺开关
		storeSwitch() {
			return this.$store.state.config.store_switch || 2;
		},
		// 根据接口返回数据判断是否使用优惠券
		couponIsUsed() {
			return this.cartData.coupon instanceof Array;
		}
	},
	watch: {
		// 监听数据状态(切换收货地址, 是否使用优惠券, 是否使用积分) 重新请求订单数据
		params: {
			handler() {
				this.getCartList();
			},
			deep: true
		}
	}
};
</script>

<style>
.content-top {
	/* #ifdef MP-WEIXIN */
	padding-bottom: 116rpx;
	/* #endif */
}
.coupon-enter{
    display: flex;
    height: 60upx;
    margin: 40upx;
}
.coupon-enter>view{
    display: inline-block;
}
.coupon-input{
    /* width: 450upx; */
    flex: 1;
    border: 2upx solid #e8e8e8;
    background-color: #fff;
    height: 100%;
        display: block;
}
.coupon-input input{
    height: 100%;
    font-size: 26upx;
    padding: 2upx 10upx;
}
.coupon-code{
    margin: 4upx 30upx;
}
.input-arr{

}
.coupon-enter-btn{
    height: 100%;
    margin-left: 20upx;
}
.coupon-enter-btn .btn{
    font-size: 24upx;
    height: 100%;
    width: 108upx;
    line-height: 58upx;
}
.bg-c{
    background-color: #ccc;
}
.no-store{
    text-align: center;
    padding: 30upx 0;
    font-size: 26upx;
    color: #666;
    /* min-height: 60upx; */
}
.coupon-none{
    text-align: center;
    padding: 120upx 0;
}
.coupon-none-img{
    width: 274upx;
    height: 274upx;
}
.coupon-list{
    padding: 0 40rpx;
}
.coupon-item{
    background-color: #fff;
    margin-bottom: 20rpx;
    position: relative;
}
.coupon-name{
    padding: 10rpx 34rpx 10rpx 10rpx;
}
.coupon-code1{
    padding: 10rpx;
}
.delete{
    position: absolute;
    top: 10rpx;
    right: 0;
}
.delete image{
    width: 40rpx;
    height: 40rpx;
}

.margin-cell-group {
	margin: 0 0 2upx 0;
}

.add-title-items {
	text-align: center;
}
.add-title-items .btn {
	height: ;
	font-size: 24upx;
	/* margin: 0 auto; */
}
.add-title-item .cell-item-hd {
	min-width: 40upx;
	color: #666;
	font-size: 28upx;
}
.add-title-item .cell-item-bd {
	color: #333;
	font-size: 28upx;
	width: 100%;
}
.add-title-item .cell-bd-text {
	bottom: 0;
}
.add-title-item .cell-item-bd .cell-bd-view {
	justify-content: space-between;
}
.cell-bd-view:first-child {
	margin-bottom: 8upx;
}
.cell-ft-view:first-child {
	margin-bottom: 8upx;
}
.address {
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-line-clamp: 2;
	overflow: hidden;
	width: 100%;
}
.img-list {
	margin-bottom: 20upx;
}
.button-bottom button {
	height: 100%;
	width: 280upx;
}
.button-bottom-c {
	display: inline-block;
	position: relative;
	padding: 10upx 26upx;
	height: 100%;
	width: 470upx;
	float: left;
	font-size: 22upx;
	color: #666;
	overflow: hidden;
}
.button-bottom-c-t {
	font-size: 22upx;
	color: #999;
	display: inline-block;
	float: left;
	height: 100%;
	line-height: 70upx;
}
.button-bottom-c-b {
	font-size: 26upx;
	color: #333;
	display: inline-block;
	float: right;
	height: 100%;
	line-height: 70upx;
}
.invoice .cell-ft-text {
	/* top: 4upx; */
	color: #666;
	font-size: 24upx;
}
.cell-item-ft .cell-bd-input{
    text-align: right;
    width: 250px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
}
.pop-t {
	border-bottom: 2upx solid #f4f4f4;
	background-color: #fff;
}
.pop-b {
	margin-bottom: 90upx;
}
.pop-b-t {
	background-color: #fff;
	width: 100%;
	padding-top: 10upx;
}
.coupon-c {
	/* padding: 50upx; */
	height: 546upx;
	box-sizing: border-box;
}
.coupon-c-item {
	margin: 30upx;
	/* width: 100%; */
	height: 150upx;
	margin-bottom: 20upx;
}
.cci-l {
	width: 60upx;
	height: 100%;
	background-color: #ff7159;
	font-size: 32upx;
	display: inline-block;
	box-sizing: border-box;
	float: left;
	border-top-left-radius: 16upx;
	border-bottom-left-radius: 16upx;
}
.cci-l-c {
	height: 60upx;
	line-height: 44upx;
	width: 150upx;
	text-align: center;
	transform-origin: 30upx 30upx;
	transform: rotate(90deg);
}
.cci-r {
	position: relative;
	height: 150upx;
	width: calc(100% - 70upx);
	margin-left: 10upx;
	display: inline-block;
	background-color: #fff;
}
.cci-r-img {
	position: absolute;
	width: 100%;
	height: 100%;
	background-color: #fff;
}
.cci-r-c {
	position: relative;
	z-index: 99;
}
.ccirc-t {
	font-size: 24upx;
	padding: 10upx 20upx;
	min-height: 56upx;
}
.ccirc-b {
	padding: 10upx;
	position: relative;
}
.ccirc-b-l {
	display: inline-block;
	max-width: 400upx;
}
.ccirc-b-tip {
	font-size: 28upx;
	width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	min-height: 38upx;
}
.ccirc-b-tip text {
	font-size: 34upx;
}
.ccirc-b-time {
	font-size: 24upx;
	width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
.ccirc-b-r {
	display: inline-block;
	background-color: #ff7159;
	font-size: 26upx;
	padding: 4upx 10upx;
	border-radius: 4upx;
	position: absolute;
	right: 20upx;
	bottom: 12upx;
}
.pop-c .btn {
	width: 100%;
}
.leave-message {
	margin: 20upx 0;
}
.leave-message .cell-item {
	border-bottom: 0;
}
.cell-textarea {
	padding: 0 26upx 20upx;
}

/* #ifndef MP-WEIXIN */
.cell-textarea textarea {
	width: 100%;
	height: 100upx;
	font-size: 26upx;
	color: #333;
}
/* #endif */
/* #ifdef MP-WEIXIN */
.cell-textarea input {
	width: 100%;
	font-size: 26upx;
	color: #333;
}
/* #endif */
.coupon-enter {
	display: flex;
	height: 60upx;
	margin: 40upx;
}
.coupon-enter > view {
	display: inline-block;
}
.coupon-input {
	/* width: 450upx; */
	flex: 1;
	border: 2upx solid #e8e8e8;
	background-color: #fff;
	height: 100%;
	display: block;
}
.coupon-input input {
	height: 100%;
	font-size: 26upx;
	padding: 2upx 10upx;
}
.coupon-code {
	margin: 4upx 30upx;
}
.input-arr {
}
.coupon-enter-btn {
	height: 100%;
	margin-left: 20upx;
}
.coupon-enter-btn .btn {
	font-size: 24upx;
	height: 100%;
	width: 108upx;
	line-height: 58upx;
}
.bg-c {
	background-color: #ccc;
}
.no-store {
	text-align: center;
	padding: 30upx 0;
	font-size: 26upx;
	color: #666;
	/* min-height: 60upx; */
}
.coupon-none {
	text-align: center;
	padding: 120upx 0;
}
.coupon-none-img {
	width: 274upx;
	height: 274upx;
}
.coupon-list {
	padding: 0 40rpx;
}
.coupon-item {
	background-color: #fff;
	margin-bottom: 20rpx;
	position: relative;
}
.coupon-name {
	padding: 10rpx 34rpx 10rpx 10rpx;
}
.coupon-code1 {
	padding: 10rpx;
}
.delete {
	position: absolute;
	top: 10rpx;
	right: 0;
}
.delete image {
	width: 40rpx;
	height: 40rpx;
}

.promotion-item{
	display: inline-block;
	float: left;
	margin-right: 10upx;
	margin-bottom: 4upx;
	background-color:#FF7159;
	color: #fff;
	height: 34upx;
	font-size: 24upx;
	line-height: 34upx;
	padding: 0 10upx;
}
</style>
