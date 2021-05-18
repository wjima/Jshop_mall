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
					}, 200)
					// #endif
					// #ifdef MP-WEIXIN || MP-ALIPAY || MP-TOUTIAO
					setTimeout(() => {
						uni.hideToast()
						uni.navigateTo({
							url: '/pages/login/choose/index',
							animationType: 'pop-in',
							animationDuration: 200
						})
					}, 200)
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
		setBarTitle(val) {
			uni.setNavigationBarTitle({
				title: this.$t(`${val}`)
			})
		},
		copyData(data) {
			var _this = this;
			uni.setClipboardData({
				data: data,
				success: function() {
					_this.$common.errorToShow('复制成功')
				}
			});
		},
		// 预览图片
		previewImage(urls, current) {
			uni.previewImage({
				current: current || 0,
				urls,
				indicator: 'default'
			});
		},
		// 保存海报到本地
		savePoster() {
			let _this = this;
			// #ifdef H5 
			_this.downloadIamge(_this.poster, 'image');
			// #endif

			// #ifdef MP || MP-ALIPAY || APP-PLUS || APP-PLUS-NVUE
			_this.downloadImageOfMp(_this.poster)
			// #endif
		},
		//下载图片地址和图片名
		downloadIamge(imgsrc, name) {
			var image = new Image();
			// 解决跨域 Canvas 污染问题
			image.setAttribute('crossorigin', 'anonymous');
			image.onload = () => {
				var canvas = document.createElement('canvas');
				canvas.width = image.width;
				canvas.height = image.height;
				var context = canvas.getContext('2d');
				context.drawImage(image, 0, 0, image.width, image.height);
				var url = canvas.toDataURL('image/png'); //得到图片的base64编码数据
				var a = document.createElement('a'); // 生成一个a元素
				var event = new MouseEvent('click'); // 创建一个单击事件
				a.download = name || 'photo'; // 设置图片名称
				a.href = url; // 将生成的URL设置为a.href属性
				a.dispatchEvent(event); // 触发a的单击事件
			};
			image.src = imgsrc;
		},
		downloadImageOfMp(image) {
			let _this = this

			// #ifdef APP-PLUS || APP-PLUS-NVUE
			uni.downloadFile({
				url: image,
				success(res) {
					uni.saveImageToPhotosAlbum({
						filePath: res.tempFilePath,
						success() {
							_this.$common.successToShow('保存成功')
						},
						fail() {
							_this.$common.errorToShow('图片保存失败')
						}
					});
				},
				fail() {
					_this.$common.errorToShow('下载失败')
				}
			})
			// #endif

			// #ifdef MP
			uni.authorize({
				scope: 'scope.writePhotosAlbum',
				success() {
					// 先下载到本地
					uni.downloadFile({
						url: image,
						success(res) {
							uni.saveImageToPhotosAlbum({
								filePath: res.tempFilePath,
								success() {
									_this.$common.successToShow('保存成功')
								},
								fail() {
									_this.$common.errorToShow('图片保存失败')
								}
							});
						},
						fail() {
							_this.$common.errorToShow('下载失败')
						}
					})
				},
				fail() {
					//console.log('授权失败')
				}
			})
			// #endif
		}
	}
}


export const clickUrl = {
	methods: {
		action(type, val) {
			if (!val) {
				return;
			}
			if (type == 1) {
				if (val.indexOf('http') != -1) {
					// #ifdef H5 
					window.location.href = val
					// #endif

					// #ifndef H5
					uni.navigateTo({
						url: '/pages/webview/index.vue?src=' + val
					})
					// #endif
				} else {
					// #ifdef H5 || APP-PLUS || APP-PLUS-NVUE || MP
					if (val == '/pages/index/index' || val == '/pages/classify/classify' || val == '/pages/cart/index/index' || val ==
						'/pages/me/index') {
						uni.switchTab({
							url: val
						});
						return;
					} else if (val.indexOf('/pages/coupon/coupon') > -1) {
						var id = val.replace('/pages/coupon/coupon?id=', "");
						this.receiveCoupon(id)
					} else {
						navigateTo(val);
						return;
					}
					// #endif
				}
			} else if (type == 2) {
				// 商品详情
				this.goodsDetail(val)
			} else if (type == 3) {
				// 文章详情
				this.$common.navigateTo('/pages/article/index?id=' + val + '&id_type=1')
			} else if (type == 4) {
				// 文章列表
				this.$common.navigateTo('/pages/article/list?cid=' + val)
			} else if (type == 5) {
				//智能表单 
				this.$common.navigateTo('/pages/form/detail/form?id=' + val)
			}
		},
		receiveCoupon(couponId) {
			let data = {
				promotion_id: couponId
			}
			this.$api.getCoupon(data, res => {
				if (res.status) {
					this.$common.successToShow(res.msg)
				} else {
					this.$common.errorToShow(res.msg)
				}
			})
		},
		goodsDetail(id) {
			let url = '/pages/goods/index/index?id=' + id;
			navigateTo(url);
		}

	}
}

export const subscription = {
	data() {
		return {
			msgList: [{
					name: '下单通知',
					desc: '商城下单成功后通知我',
					func: 'order',
					tmpl: '',
					status: false
				},
				{
					name: '支付通知',
					desc: '订单支付后通知我',
					func: 'pay',
					tmpl: '',
					status: false
				},
				{
					name: '待付通知',
					desc: '未支付订单取消前通知我',
					func: 'cancel',
					tmpl: '',
					status: false
				},
				{
					name: '发货通知',
					desc: '订单发货后通知我',
					func: 'ship',
					tmpl: '',
					status: false
				},
				{
					name: '售后通知',
					desc: '订单售后结果通知我',
					func: 'after_sale',
					tmpl: '',
					status: false
				},
				{
					name: '退款通知',
					desc: '售后退款结果通知我',
					func: 'refund',
					tmpl: '',
					status: false
				}
			]
		}
	},
	methods: {
		getSubscriptionTmplIds(id) {
			this.$api.getSubscriptionTmplIds({
				type: id
			}, res => {
				if (res.status) {
					console.log('res.statue', res);
					for (let i = 0; i < this.msgList.length; i++) {
						if (res.data[this.msgList[i].func]) {
							this.msgList[i].tmpl = res.data[this.msgList[i].func].template_id;
							if (this.msgList[i].tmpl != '') {
								this.msgList[i].status = true;
							}
						}
					}
				} else {
					//this.$common.errorToShow('消息订阅配置信息获取失败');
				}
			});
		},
		subAction(func) {
			const _this = this
			// #ifdef MP-WEIXIN
			let tmplIds = []; //msglist没有数据时，不需要唤起消息订阅
			this.msgList.forEach(function(element, index) {
				if (element.status) {
					tmplIds.push(element.tmpl);
				}
			});
			
			let subscriptionsSetting = {};
			uni.getSetting({
				withSubscriptions: true,
				success(res) {
					
					if (res.subscriptionsSetting.itemSettings) {
						subscriptionsSetting = res.subscriptionsSetting.itemSettings;
						for (let index in res.subscriptionsSetting.itemSettings) {
							let element = res.subscriptionsSetting.itemSettings[index];
							var tIndex = tmplIds.indexOf(index);
							if (tIndex > -1 && element == 'accept') { //找到并接受的，剔除下，不需要重复同意
								tmplIds.splice(tIndex, 1);
							}
						}
					}
					
					if (tmplIds.length > 0) {
						uni.requestSubscribeMessage({
							tmplIds: tmplIds,
							success(res) {
								if (res.errMsg == 'requestSubscribeMessage:ok') {
									tmplIds.forEach(function(element, index) {
										let data = {
											template_id: element,
											status: res[element]
										};
										//设置订阅状态
										_this.$api.setSubscriptionStatus(data, e => {});
									});

								} else {
									_this.$common.errorToShow('操作失败，请稍候重试！', r => {
										_this.getSubscriptionTmplIds();
									});
								}
							},
							fail(error) {
								console.log('error', error);
							},
							complete() {
								typeof func == 'function' && func()
							}
						});
					} else {
						typeof func == 'function' && func()
					}


				},
			});

			// #endif

			// #ifndef MP-WEIXIN
			typeof func == 'function' && func()
			// #endif
		},
	}
}
