export const orders = {
	mounted() {},
	methods: {
		// 查看订单详情
		orderDetail(orderId) {
			this.$common.navigateTo(
				'/pages/member/order/orderdetail?order_id=' + orderId
			)
		},
		// 取消订单

		// 去支付
		toPay(orderId) {
			this.$common.navigateTo(
				'/pages/goods/payment/index?order_id=' + orderId + '&type=1'
			)
		},
		// 确认收货

		// 去评价
		toEvaluate(orderId) {
			this.$common.navigateTo(
				'/pages/member/order/evaluate?order_id=' + orderId
			)
		},
		// 申请售后

		// 查看物流信息
		showExpress(code, no, address = '') {
			let params = encodeURIComponent(
				'code=' + code + '&no=' + no + '&add=' + address
			)

			this.$common.navigateTo(
				'/pages/member/order/express_delivery?params=' + params
			)
		}
	}
}

/**
 *  商品接口信息
 *
 */
export const goods = {
	mounted() {},
	methods: {
		// 查看商品详情
		goodsDetail(goodsId) {
			this.$common.navigateTo('/pages/goods/index/index?id=' + goodsId)
		},
		// 跳转商品列表页
		goodsList(obj = {}) {
			let url = '/pages/classify/index'
			if (Object.keys(obj).length) {
				url = this.$common.builderUrlParams(url, obj)
			}
			this.$common.navigateTo(url)
		},
		// 团购秒杀详情
		groupDetail(id, group_id) {
			this.$common.navigateTo(
				'/pages/goods/index/group?id=' + id + '&group_id=' + group_id
			)
		},
		//拼团详情页
		pintuanDetail(id, team_id) {
			if (team_id) {
				this.$common.navigateTo(
					'/pages/goods/index/pintuan?id=' + id + '&team_id=' + team_id
				)
			} else {
				this.$common.navigateTo('/pages/goods/index/pintuan?id=' + id)
			}
		}
	}
}

/**
 *
 *  返回操作处理
 *
 */
export const goBack = {
	onBackPress(options) {
		if (options.from === 'navigateBack') {
			return false
		}
		let loginPages = ['/pages/cart/index/index', '/pages/member/index/index']
		let backPage = this.$store.state.redirectPage
		if (loginPages.indexOf(backPage) > -1) {
			this.$store.commit({
				type: 'redirect',
				page: ''
			})
			uni.switchTab({
				url: '/pages/index/index'
			})
			return true
		}
	},
	backBtn() {
		var pages = getCurrentPages();
		if (pages.length > 1) {
			uni.navigateBack({
				delta: 1
			});
		} else {
			uni.switchTab({
				url: '/pages/index/index'
			});
		}
	},
}

/* Function Info
 * Author:      zhf
 * CreateTime:  2019/7/12 下午12:10:00
 * LastEditor:  zhf
 * ModifyTime:  2019/7/12 下午12:10:00
 * Description: 登录成功统一跳转处理
 */

export const jumpBackPage = {
	methods: {
		handleBack() {
			let redirect = this.$store.state.redirectPage;
			console.log(redirect);
			this.$store.commit({
				type: 'redirect',
				page: ''
			})
			let switchTabs = ['/pages/index/index', '/pages/member/index/index']
			if (switchTabs.indexOf(redirect) > -1) {
				uni.switchTab({
					url: redirect
				})
			} else if (redirect) {
				uni.redirectTo({
					url: redirect
				})
			} else {
				uni.switchTab({
					url: '/pages/index/index'
				})
			}
		}
	}
}

/* Function Info
 * Author:      zhf
 * CreateTime:  2019/7/12 下午12:10:28
 * LastEditor:  zhf
 * ModifyTime:  2019/7/12 下午12:10:28
 * Description: 操作判断登录处理
 */

export const checkLogin = {
	methods: {
		checkIsLogin() {
			uni.showToast({
				title: '请先登录！',
				icon: 'none',
				duration: 800,
				success: function(res) {
					// #ifdef H5 || APP-PLUS
					setTimeout(() => {
						uni.hideToast()
						uni.navigateTo({
							url: '/pages/login/login/index1'
						})
					}, 800)
					// #endif
					// #ifdef MP-WEIXIN || MP-ALIPAY || MP-TOUTIAO
					setTimeout(() => {
						uni.hideToast()
						uni.navigateTo({
							url: '/pages/login/choose/index',
							animationType: 'pop-in',
							animationDuration: 200
						})
					}, 500)
					// #endif
				}
			})
		}
	}
}


/**
 * 工具函数
 */

export const tools = {
	methods: {
		copyData(data) {
			var _this = this;
			uni.setClipboardData({
				data: data,
				success: function() {
					_this.$common.errorToShow('复制成功')
				}
			});
		}
	}
}
