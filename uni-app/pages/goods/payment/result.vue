<template>
	<view class="content">
		<view class="result succsee" v-if="status && paymentInfo.status === 2">
			<image class="result-img" src="/static/image/win.png" mode=""></image>
			<view class="result-top">支付成功</view>
			<view class="result-mid red-price">{{ paymentInfo.money || '' }}</view>
			<view class="result-bot"><button class="btn btn-g" @click="orderDetail()">查看详情</button></view>
		</view>
		<view class="result fail" v-else-if="status && paymentInfo.status === 1">
			<image class="result-img" src="/static/image/pastdue.png" mode=""></image>
			<view class="result-top">支付失败</view>
			<view class="result-mid red-price">{{ paymentInfo.money || '' }}</view>
			<view class="result-bot"><button class="btn btn-g" @click="orderDetail()">查看详情</button></view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			paymentId: 0,
			paymentInfo: {}, // 支付单详情
			orderId: 0,
			status: false
		};
	},
	onLoad(options) {
		if (options.id) {
			this.paymentId = options.id;
		}
		if (options.order_id) {
			this.orderId = options.order_id;
		}
	},
	mounted() {
		this.getPaymentInfo();
	},
	methods: {
		getPaymentInfo() {
			if (!this.paymentId) {
				this.status = true;
				this.paymentInfo.money = '0.00';
				this.paymentInfo.status = 2;
				this.paymentInfo.type = 1;

				return;
			}
			let data = {
				payment_id: this.paymentId
			};

			this.$api.paymentInfo(data, res => {
				if (res.status) {
					let info = res.data;
					if (info.payment_code === 'alipay') {
						info.payment_name = '支付宝支付';
					} else if (info.payment_code === 'wechatpay') {
						info.payment_name = '微信支付';
					} else if (info.payment_code === 'balancepay') {
						info.payment_name = '余额支付';
					}

					// 获取订单号
					if (info.rel.length) {
						for (let i = 0; i < info.rel.length; i++) {
							if (info.rel[i].source_id) {
								this.orderId = info.rel[i].source_id;
								break;
							}
						}
					}
					this.status = true;
					this.paymentInfo = info;
				} else {
					this.$common.errorToShow(res.msg);
				}
			});
		},
		orderDetail() {
			if (this.orderId && this.paymentInfo.type === 1) {
				this.$common.redirectTo('/pages/member/order/orderdetail?order_id=' + this.orderId);
			} else if (this.paymentInfo.type === 2) {
				this.$common.redirectTo('/pages/member/balance/details');
			} else if (this.paymentInfo.type === 5 || this.paymentInfo.type === 6) {
				uni.navigateTo({
					url: '/pages/index/index'
				});
			}
		}
	}
};
</script>

<style>
.result {
	text-align: center;
	padding-top: 200upx;
}

.result-img {
	width: 140upx;
	height: 140upx;
	margin-bottom: 20upx;
}

.result-num {
	color: #666;
	font-size: 30upx;
	margin-bottom: 20upx;
}

.result-top {
	color: #666;
	font-size: 30upx;
	margin-bottom: 20upx;
}

.result-mid {
	margin-bottom: 60upx;
}

.result-bot .btn {
	margin-top: 40upx;
	font-size: 26upx;
	padding: 0 50upx;
}
</style>
