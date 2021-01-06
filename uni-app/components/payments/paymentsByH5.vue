<template>
	<view class='cell-group payment-method'>
		<view class='cell-item add-title-item cell-item-mid right-img' v-for="item in payments" :key="item.code" @click="toPayHandler(item.code)"
		 v-if="!(type == 2 && item.code == 'balancepay')">
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
		</view>
		<!-- <view class="payment-pop" v-show="popShow">
			<view class="payment-pop-c">
				<image src="/static/image/wait-pay.png" style="width: 30px;
		height: 30px;"></image>
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
		baseUrl
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
				openid: '',
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
				// h5支付并且是在微信浏览器内 过滤支付宝支付
				if (this.$common.isWeiXinBrowser()) {
					payments = payments.filter(item => item.code !== 'alipay')
				}

				// 如果是充值订单 过滤余额支付 过滤非线上支付方式
				if (this.type === 2) {
					payments = payments.filter(
						item => item.code !== 'balancepay' || item.is_online === 1
					)
				}

				// 设置logo图片
				payments.forEach(item => {
					this.$set(item, 'icon', '/static/image/' + item.code + '.png')
				})

				return payments
			},
			checkWXJSBridge(data) {
				let that = this
				let interval = setInterval(() => {
					if (typeof window.WeixinJSBridge != 'undefined') {
						clearTimeout(interval)
						that.onBridgeReady(data)
					}
				}, 200)
			},
			onBridgeReady(data) {
				var _this = this
				window.WeixinJSBridge.invoke(
					'getBrandWCPayRequest', {
						appId: data.appid, // 公众号名称，由商户传入
						timeStamp: data.timeStamp, // 时间戳，自1970年以来的秒数
						nonceStr: data.nonceStr, // 随机串
						package: data.package,
						signType: data.signType, // 微信签名方式：
						paySign: data.paySign // 微信签名
					},
					function(res) {
						if (res.err_msg === 'get_brand_wcpay_request:ok') {
							_this.$common.successToShow('支付成功')
						} else if (res.err_msg === 'get_brand_wcpay_request:cancel') {
							_this.$common.errorToShow('取消支付')
						} else {
							_this.$common.errorToShow('支付失败')
						}
						setTimeout(() => {
							_this.$common.redirectTo(
								'/pages/goods/payment/result?id=' + data.payment_id
							)
						}, 1000)
					}
				)
			},
			// 用户点击支付方式处理
			toPayHandler(code) {
				this.popShow = true;
				let data = {
					payment_code: code,
					payment_type: this.type
				}
				data['ids'] = (this.type == 1 || this.type == 5 || this.type == 6) ? this.orderId : this.uid
				switch (code) {
					case 'alipay':
						/**
						 * 支付宝支付需要模拟GET提交数据
						 */
						if (this.type == 1 && this.orderId) {
							data['params'] = {
								trade_type: 'WAP',
								return_url: baseUrl +
									'wap/pages/goods/payment/result'
							}
						} else if (this.type == 2 && this.recharge) {
							data['params'] = {
								money: this.recharge,
								return_url: baseUrl + 'wap/pages/goods/payment/result'
							}
						} else if ((this.type == 5 || this.type == 6) && this.recharge) {
							data['params'] = {
								formid: this.orderId
							}
						}

						this.$api.pay(data, res => {
							if (res.status) {
								const url = res.data.url
								const data = res.data.data

								// 模拟GET提交
								let tempForm = document.createElement('form')
								tempForm.id = 'aliPay'
								tempForm.methods = 'post'
								tempForm.action = url
								tempForm.target = '_self'
								let input = []
								for (let k in data) {
									input[k] = document.createElement('input')
									input[k].type = 'hidden'
									input[k].name = k
									input[k].value = data[k]
									tempForm.appendChild(input[k])
								}
								tempForm.addEventListener('submit', function() {}, false)
								document.body.appendChild(tempForm)
								tempForm.dispatchEvent(new Event('submit'))
								tempForm.submit()
								document.body.removeChild(tempForm)
							}else{
								this.$common.errorToShow(res.msg)
							}
						})
						break
					case 'wechatpay':
						if(this.payStatus == false) {
							return
						}
						this.payStatus = false
						let _this = this
						/**
						 * 微信支付有两种
						 * 判断是否在微信浏览器
						 * 	微信jsapi支付
						 */
						let isWeiXin = this.$common.isWeiXinBrowser()

						if (isWeiXin) {
							var transitUrl =
								baseUrl +
								'wap/pages/goods/payment/auth?order_id=' +
								this.orderId +
								'&type=' +
								this.type;

							if (this.type == 1 && this.orderId) {
								// 微信jsapi支付
								// if (this.openid) {
								//   data['params'] = {
								//     trade_type: 'JSAPI_OFFICIAL',
								//     openid: this.openid
								//   }
								// } else {
								//   data['params'] = {
								//     trade_type: 'JSAPI_OFFICIAL',
								//     url: window.location.href
								//   }
								// }
								data['params'] = {
									trade_type: 'JSAPI_OFFICIAL',
									url: transitUrl
								}
							} else if (this.type == 2 && this.recharge) {
								data['params'] = {
									trade_type: 'JSAPI_OFFICIAL',
									money: this.recharge,
									url: transitUrl + '&uid=' + this.uid + '&money=' + this.recharge
								}
								// if (this.openid) {
								//   data['params'] = {
								//     money: this.recharge,
								//     openid: this.openid
								//   }
								// } else {
								//   data['params'] = {
								//     money: this.recharge,
								//     url: window.location.href
								//   }
								// }
							} else if ((this.type == 5 || this.type == 6) && this.recharge) {
								data['params'] = {
									formid: this.orderId,
									trade_type: 'JSAPI_OFFICIAL',
									url: transitUrl
								}
							}
							this.$api.pay(data, res => {
								if (!res.status && res.data == '10066') {
									window.location.href = res.msg
									return;
								}
								if(!res.status){
									this.$common.errorToShow(res.msg);
									return;
								}
								const data = res.data
								this.checkWXJSBridge(data)
							})
						} else {
							// 微信 H5支付
							if (this.type == 1 && this.orderId) {
								data['params'] = {
									trade_type: 'MWEB',
									return_url: baseUrl +
										'wap/pages/goods/payment/result'
								}
							} else if (this.type == 2 && this.recharge) {
								data['params'] = {
									trade_type: 'MWEB',
									money: this.recharge,
									return_url: baseUrl + 'wap/pages/goods/payment/result'
								}
							} else if ((this.type == 5 || this.type == 6) && this.recharge) {
								data['params'] = {
									formid: this.orderId,
									trade_type: 'MWEB',
									return_url: baseUrl + 'wap/pages/goods/payment/result'
								}
							}
							// 微信h5支付
							this.$api.pay(data, res => {
								_this.payStatus = true
								if (res.status) {
									location.href = res.data.mweb_url
								} else {
									this.$common.errorToShow(res.msg)
								}
							})
						}
						break
					case 'balancepay':
						/**
						 *  用户余额支付
						 *
						 */
						if ((this.type == 5 || this.type == 6) && this.recharge) {
							data['params'] = {
								formid: this.orderId
							}
						}
						this.$api.pay(data, res => {
							if (res.status) {
								this.$common.redirectTo(
									'/pages/goods/payment/result?id=' + res.data.payment_id
								)
							} else {
								this.$common.errorToShow(res.msg)
							}
						})
						break
					case 'offline':
						/**
						 * 线下支付
						 */
						this.$common.modelShow(
							'线下支付说明',
							'请联系客服进行线下支付',
							() => {},
							false,
							'取消',
							'确定'
						)
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

	.payment-pop .text {
		font-size: 24upx;
	}
</style>
