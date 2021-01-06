<template>
	<view class='cell-group payment-method payment-wx'>
		<form class='cell-item add-title-item cell-item-mid right-img' v-for="item in payments" :key="item.code" @submit="toPayHandler" report-submit="true"
		 v-if="!(type == 2 && item.code == 'balancepay')">
			<input name="code" :value="item.code" class="no-show">
			<button class="btn" form-type="submit">
				<view class='cell-item-hd'>
					<image class='cell-hd-icon' :src='item.icon'></image>
				</view>
				<view class='cell-item-bd cell-item-bd-block'>
					<view class="cell-bd-view">
						<text class="cell-bd-text">{{ item.name }}</text>
					</view>
					<view class="cell-bd-view">
						<text class="cell-bd-text address">{{ item.memo }}</text>
					</view>
				</view>
				<view class='cell-item-ft'>
					<image class='cell-ft-next icon' src='/static/image/right.png'></image>
				</view>
			</button>
		</form>
		<!-- <view class="payment-pop" v-show="popShow">
			<view class="payment-pop-c">
				<image src="/static/image/wait-pay.png"></image>
				<view class="text">支付中，请稍后...</view>
			</view>
			<view class="payment-pop-b">
				<button class="btn btn-c" @click="popBtn">支付失败</button>
				<button class="btn btn-o" @click="popBtn">支付成功</button>
			</view>
		</view> -->
	</view>
</template>

<script>
	import {
		apiBaseUrl
	} from '@/config/config.js'
	export default {
		props: {
			// 如果是商品订单此参数必须
			orderId: {
				type: String,
				default () {
					return ''
				}
			},
			// 如果是充值订单此参数必须
			recharge: {
				type: Number,
				default () {
					return 0
				}
			},
			// 用户id
			uid: {
				type: Number,
				default () {
					return 0
				}
			},
			// 订单类型
			type: {
				type: Number,
				default () {
					return 1
				}
			}
		},
		data() {
			return {
				payments: [],
				popShow: false,
				payStatus: true
			}
		},
		mounted() {
			this.getPayments()
		},
		methods: {
			// 获取可用支付方式列表
			getPayments() {
				this.$api.paymentList({}, res => {
					if (res.status) {
						this.payments = this.formatPayments(res.data)
					}
				})
			},
			// 支付方式处理
			formatPayments(payments) {
				// 过滤支付宝支付
				payments = payments.filter(item => item.code !== 'alipay')

				// 如果是充值订单 过滤余额支付 过滤非线上支付方式
				if (this.type === 2) {
					payments = payments.filter(item => item.code !== 'balancepay' || item.is_online === 1)
				}

				// 设置logo图片
				payments.forEach(item => {
					this.$set(item, 'icon', '/static/image/' + item.code + '.png')
				})

				return payments
			},
			// 用户点击支付方式处理
			toPayHandler(e) {
				if(this.payStatus == false) {
					return
				}
				this.payStatus = false
				this.popShow = true;
				let code = e.target.value.code;
				let formId = e.detail.formId;

				let data = {
					payment_code: code,
					payment_type: this.type,
					params: {
						formid: formId
					}
				}
				data['ids'] = (this.type == 1 || this.type == 5 || this.type == 6) ? this.orderId : this.uid

				// 判断订单支付类型
				if (this.type == 2 && this.recharge) {
					data['params'] = {
						money: this.recharge
					}
				} else if ((this.type == 5 || this.type == 6) && this.recharge) {
					data['params'] = {
						formid: this.orderId
					}
				}
				let _this = this
				switch (code) {
					case 'wechatpay':
						this.$api.pay(data, res => {
							if (res.status) {
								uni.requestPayment({
									provider: 'wxpay',
									timeStamp: res.data.timeStamp,
									nonceStr: res.data.nonceStr,
									package: res.data.package,
									signType: res.data.signType,
									paySign: res.data.paySign,
									success: function(e) {
										_this.payStatus = true
										if (e.errMsg === 'requestPayment:ok') {
											_this.$common.successToShow(res.msg, () => {
												_this.$common.redirectTo('/pages/goods/payment/result?id=' + res.data.payment_id)
											})
										}
									},
									fail() {
										_this.payStatus = true
									}
								});
							} else {
								_this.payStatus = true
								this.$common.errorToShow(res.msg)
							}
						})
						break
					case 'balancepay':
						/**
						 *  用户余额支付
						 * 
						 */
						this.$api.pay(data, res => {
							_this.payStatus = true
							if (res.status) {
								this.$common.redirectTo('/pages/goods/payment/result?id=' + res.data.payment_id)
							} else {
								this.$common.errorToShow(res.msg)
							}
						})
						break
					case 'offline':
						/**
						 * 线下支付
						 */
						_this.payStatus = true
						this.$common.modelShow('线下支付说明', '请联系客服进行线下支付', () => {}, false, '取消', '确定')
						break
				}

			},
			// 支付中显示隐藏
			popBtn() {
				this.popShow = false
			}
		}

	}
</script>

<style>
	.payment-method .cell-item-hd {
		min-width: 70upx;
	}

	.payment-method .cell-hd-icon {
		width: 70upx;
		height: 70upx;
	}

	.payment-method .cell-item-bd {
		border-left: 2upx solid #F0F0F0;
		padding-left: 30upx;
		margin-left: 0;
	}

	.payment-method .cell-bd-text {
		font-size: 28upx;
		color: #666;
	}

	.payment-method .address {
		font-size: 24upx;
		color: #999;
	}

	.no-show {
		display: none;
	}

	.payment-wx .btn {
		background-color: #fff;
		line-height: 1.7;
		padding: 0;
		width: 724upx;
		position: relative;
		/* overflow: hidden;
		float: left; */
		display: flex;
		align-items: center;
	}

	.payment-wx .btn .cell-item-hd {
		min-width: 100upx;
	}

	.payment-pop {
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		width: 400rpx;
		height: 272rpx;
		background-color: #fff;
		text-align: center;
		box-shadow: 0 0 20rpx #ccc;
		/* border-radius: 10rpx; */
	}

	.payment-pop-c {
		padding: 50rpx 30rpx;
		/* line-height: 300rpx; */
		font-size: 32rpx;
		color: #999;
	}

	.payment-pop-c image {
		width: 60upx;
		height: 60upx;
	}

	.payment-pop-b {
		position: absolute;
		bottom: 0;
		display: flex;
		width: 100%;
		justify-content: space-between;
	}

	.payment-pop-b .btn {
		flex: 1;
		justify-content: center;
	}

	.payment-pop-b .btn-o {
		background-color: #FF7159;
	}

	.payment-pop .text {
		font-size: 24upx;
	}
</style>
