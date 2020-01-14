<template>
	<view class="content">
		<view class="cell-group margin-cell-group">
			<view class="cell-item">
				<view class="cell-item-hd"><view class="cell-hd-title">订单类型</view></view>
				<view class="cell-item-ft">
					<text class="cell-ft-p" v-if="type == 1">商品订单</text>
					<text class="cell-ft-p" v-if="type == 2" @click="toRecharge()">充值订单</text>
					<text class="cell-ft-p" v-if="type == 5">快捷下单</text>
					<text class="cell-ft-p" v-if="type == 6">付款码</text>
				</view>
			</view>
			<view v-if="type == 1">
				<view class="cell-item flex-item">
					<view class="cell-item-hd"><view class="cell-hd-title">订单编号</view></view>
					<view class="cell-item-ft">
						<view class="cell-ft-p" v-for="(item, index) in orderInfo.rel" :key="index" @click="orderDetail(item.source_id)">{{ item.source_id || '' }}</view>
					</view>
				</view>
				<view class="cell-item">
					<view class="cell-item-hd"><view class="cell-hd-title">支付金额</view></view>
					<view class="cell-item-ft">
						<text class="cell-ft-p red-price">￥{{ orderInfo.money || '' }}</text>
					</view>
				</view>
			</view>
			<view v-else-if="type == 2">
				<view class="cell-item">
					<view class="cell-item-hd"><view class="cell-hd-title">充值金额</view></view>
					<view class="cell-item-ft">
						<text class="cell-ft-p red-price">￥ {{ recharge || '' }}</text>
					</view>
				</view>
			</view>
			<view v-else>
				<view class="cell-item">
					<view class="cell-item-hd"><view class="cell-hd-title">支付金额</view></view>
					<view class="cell-item-ft">
						<text class="cell-ft-p red-price">￥ {{ recharge || '' }}</text>
					</view>
				</view>
			</view>
		</view>

		<!-- #ifdef H5 -->
		<payments-by-h5 :orderId="orderId" :recharge="recharge" :type="type" :uid="userInfo.id"></payments-by-h5>
		<!-- #endif -->

		<!-- #ifdef MP-WEIXIN -->
		<payments-by-wx :orderId="orderId" :recharge="recharge" :type="type" :uid="userInfo.id"></payments-by-wx>
		<!-- #endif -->

		<!-- #ifdef MP-ALIPAY -->
		<payments-by-ali :orderId="orderId" :recharge="recharge" :type="type" :uid="userInfo.id"></payments-by-ali>
		<!-- #endif -->

		<!-- #ifdef APP-PLUS || APP-PLUS-NVUE -->
		<payments-by-app :orderId="orderId" :recharge="recharge" :type="type" :uid="userInfo.id"></payments-by-app>
		<!-- #endif -->

		<!-- #ifdef MP-TOUTIAO -->
		<payments-by-tt :orderId="orderId" :recharge="recharge" :type="type" :uid="userInfo.id"></payments-by-tt>
		<!-- #endif -->
	</view>
</template>
<script>
// #ifdef H5
import paymentsByH5 from '@/components/payments/paymentsByH5.vue';
// #endif

// #ifdef MP-WEIXIN
import paymentsByWx from '@/components/payments/paymentsByWx.vue';
// #endif

// #ifdef MP-ALIPAY
import paymentsByAli from '@/components/payments/paymentsByAli.vue';
// #endif

// #ifdef APP-PLUS || APP-PLUS-NVUE
import paymentsByApp from '@/components/payments/paymentsByApp.vue';
// #endif

// #ifdef MP-TOUTIAO
import paymentsByTt from '@/components/payments/paymentsByTt.vue';
// #endif

import { orders } from '@/config/mixins.js';
export default {
	mixins: [orders],
	data() {
		return {
			orderId: 0,
			recharge: 0,
			type: 1, // 订单类型 1商品订单 2充值订单
			orderInfo: {}, // 订单详情
			userInfo: {}, // 用户信息
			form_id: 0
		};
	},
	components: {
		// #ifdef H5
		paymentsByH5,
		// #endif
		// #ifdef MP-WEIXIN
		paymentsByWx,
		// #endif
		// #ifdef MP-ALIPAY
		paymentsByAli,
		// #endif
		// #ifdef APP-PLUS || APP-PLUS-NVUE
		paymentsByApp,
		// #endif
		// #ifdef MP-TOUTIAO
		paymentsByTt
		// #endif
	},
	onLoad(options) {
		this.orderId = options.order_id;
		this.recharge = Number(options.recharge);
		this.type = Number(options.type);
		this.form_id = Number(options.form_id);
		//this.getOrderInfo ()
		if (this.orderId && this.type == 1) {
			// 商品订单

			this.getOrderInfo();
		} else if (this.recharge && this.type == 2) {
			// 充值订单 获取用户id
			this.getUserInfo();
		} else if (this.form_id && (this.type == 5 || this.type == 6)) {
			// 表单订单 id传到订单上
			this.orderId = '' + this.form_id;
		} else {
			this.$common.errorToShow('订单支付参数错误', () => {
				uni.navigateBack({
					delta: 1
				});
			});
		}
	},
	methods: {
		// 获取订单详情
		getOrderInfo() {
			let data = {
				ids: this.orderId,
				payment_type: this.type
			};
			this.$api.paymentsCheckpay(data, res => {
				if (res.status) {
					this.orderInfo = res.data;
					/* console.log(this.orderInfo)
						if(this.orderInfo.pay_status == 2){
							this.$common.redirectTo(
								'/pages/goods/payment/result?order_id=' + this.orderInfo.order_id
							)
						} */
				}
			});
		},
		// 获取用户信息
		getUserInfo() {
			this.$api.userInfo({}, res => {
				if (res.status) {
					this.userInfo = res.data;
				} else {
					this.$common.errorToShow(res.msg);
				}
			});
		},
		// 跳转我的余额页面
		toRecharge() {
			this.$common.navigateTo('/pages/member/balance/index');
		}
	}
};
</script>
<style>
.margin-cell-group {
	margin-bottom: 20upx;
}
.cell-hd-title {
	color: #999;
}
.payment-method .cell-item-hd {
	min-width: 70upx;
}
.payment-method .cell-hd-icon {
	width: 70upx;
	height: 70upx;
}
.payment-method .cell-item-bd {
	border-left: 2upx solid #f0f0f0;
	padding-left: 30upx;
}
.payment-method .cell-bd-text {
	font-size: 28upx;
	color: #666;
}
.payment-method .address {
	font-size: 24upx;
	color: #999;
}
.flex-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.flex-item .cell-item-ft {
	position: relative;
	top: 0;
	transform: translateY(0);
	right: 0;
}
</style>
