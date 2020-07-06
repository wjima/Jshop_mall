import * as db from './db.js' //引入common
import store from './../store'
//把obj对象里的值覆盖到newobj里面
function deepCopy(newobj, obj) {
	if (typeof obj != 'object') {
		return obj
	}
	for (var attr in obj) {
		var a = {}
		if (newobj[attr]) {
			a = newobj[attr]
		}
		newobj[attr] = deepCopy(a, obj[attr])
	}
	return newobj
}

//跳转到登陆页面
function jumpToLogin(method) {
	var now_time = Date.parse(new Date())
	var value = db.get('jump_to_login')
	if (!value) {
		value = 0
	}
	if (now_time - value > 3000) {
		//db.set('jump_to_login',now_time); //暂时屏蔽登录时间查询
		// 将当前页面route存vuex中 登录注册后跳转
		let pages = getCurrentPages()
		let page = pages[pages.length - 1]
		// console.log(page);
		// console.log(page.route);
		// 获取页面参数信息
		let pagePath = ''
		// #ifdef H5 || MP-WEIXIN || APP-PLUS	 || APP-PLUS-NVUE
		if (page.route.indexOf('pages/goods/index/index') !== -1) {
			//商品详情页
			if (page.goodsId) {
				pagePath = '/' + page.route + '?id=' + page.goodsId;
			} else {
				pagePath = '/pages/index/index';
			}
		}
		if (page.route.indexOf('pages/goods/index/group') !== -1) {
			//团购秒杀详情页
			if (page.goodsId && page.groupId) {
				pagePath = '/' + page.route + '?id=' + page.goodsId + '&group_id=' + page.groupId;
			} else {
				pagePath = '/pages/index/index';
			}
		}
		if (page.route.indexOf('pages/share/jump') !== -1) {
			//分享领取红包优惠券
			// console.log(page.$mp.query);
			if (page.$mp.query) {
				pagePath = '/' + page.route + '?scene=' + page.$mp.query.scene;
				// console.log(pagePath);
			} else {
				pagePath = '/pages/index/index';
			}
		}
		
		if (page.route.indexOf('pages/bargain/index') !== -1) {
			//砍价
			if (page.id && page.id != '' && page.type && page.record_id && page.record_id != 0) {
				pagePath = '/' + page.route + '?id=' + page.id + '&type=' + page.type + '&record_id=' + page.record_id;
			} else {
				pagePath = '/pages/index/index';
			}
		}
		// #endif

		// #ifdef MP-ALIPAY
		if (page.__proto__.route.indexOf('pages/goods/index/index') !== -1) {
			//商品详情页
			if (page.data.goodsId) {
				pagePath = '/' + page.__proto__.route + '?id=' + page.data.goodsId;
			} else {
				pagePath = '/pages/index/index';
			}
		}
		if (page.__proto__.route.indexOf('pages/goods/index/group') !== -1) {
			//团购秒杀详情页
			if (page.data.goodsId && page.data.groupId) {
				pagePath = '/' + page.__proto__.route + '?id=' + page.data.goodsId + '&group_id' + page.data.groupId;
			} else {
				pagePath = '/pages/index/index';
			}
		}
		if (page.__proto__.route.indexOf('pages/share/jump') !== -1) {
			//分享领取红包优惠券
			if (page.data.$mp.query) {
				pagePath = '/' + page.__proto__.route + '?scene=' + page.data.$mp.query.scene;
			} else {
				pagePath = '/pages/index/index';
			}
		}
		if (page.__proto__.route.indexOf('pages/bargain/index') !== -1) {
			//砍价
			if (page.data.id && page.data.id != '' && page.data.type && page.data.record_id && page.data.record_id != 0) {
				pagePath = '/' + page.__proto__.route + '?id=' + page.data.id + '&type=' + page.data.type + '&record_id=' + page.data.record_id;
			} else {
				pagePath = '/pages/index/index';
			}
		}
		// #endif
		// console.log(pagePath);
		if (pagePath) {
			store.commit({
				type: 'redirect',
				page: pagePath
			})
		}
		uni.showToast({
			title: '请先登录！',
			icon: 'none',
			duration: 1000,
			success: function(res) {
				setTimeout(() => {
					uni.hideToast();
					let current =  getCurrentPages()
					current = current[current.length - 1]
					if (current.route.indexOf('pages/login/choose/index') > -1 ||  current.route.indexOf('/pages/login/login/index1') > -1 ) {
						return
					}
					uni.navigateTo({
						// #ifdef H5 || APP-PLUS
						url: '/pages/login/login/index1',
						// #endif
						// #ifdef MP-WEIXIN || MP-ALIPAY || MP-TOUTIAO
						url: '/pages/login/choose/index',
						// #endif
						animationType: 'pop-in',
						animationDuration: 200
					})
				}, 500)
			}
		})
	}
}

//当出错的时候，显示错误信息，并且跳转 弃用
/* function errorToBack(msg = '出错了，请重试',delta=1){
  uni.showToast({
    title: msg,
    icon: 'none',
    duration: 2000,
  });
  if(delta > 0){
    setTimeout(function () {
      uni.navigateBack({
        delta: delta
      })
    }, 1000);
  }
} */
//操作成功后，的提示信息
function successToShow(msg = '保存成功', callback = function() {}) {


	setTimeout(function() {
		uni.showToast({
			title: msg,
			icon: 'success',
			duration: 1000,
			success() {
				setTimeout(function() {
					callback()
				}, 500)
			}
		})
	}, 100)
	/*  uni.showToast({
	    title: msg,
	    icon: 'success',
	    duration: 1000
	  }) */
}

//操作失败的提示信息
function errorToShow(msg = '操作失败', callback = function() {}) {
	setTimeout(function() {
		uni.showToast({
			title: msg,
			icon: 'none',
			duration: 1500,
			success() {
				setTimeout(function() {
					callback()
				}, 1500)
			}
		})
	},1000)

}

//加载显示
function loadToShow(msg = '加载中') {
	uni.showToast({
		title: msg,
		icon: 'loading'
	})
}

//加载隐藏
function loadToHide() {
	uni.hideToast()
}

// 提示框
function modelShow(
	title = '提示',
	content = '确认执行此操作吗?',
	callback = () => {},
	showCancel = true,
	cancelText = '取消',
	confirmText = '确定'
) {
	uni.showModal({
		title: title,
		content: content,
		showCancel: showCancel,
		cancelText: cancelText,
		confirmText: confirmText,
		cancelText: cancelText,
		success: function(res) {
			if (res.confirm) {
				// 用户点击确定操作
				setTimeout(() => {
					callback()
				}, 500)
			} else if (res.cancel) {
				// 用户取消操作
			}
		}
	})
}

//时间戳转时间格式
function timeToDate(date, flag = false) {
	var date = new Date(date * 1000) //如果date为13位不需要乘1000
	var Y = date.getFullYear() + '-'
	var M =
		(date.getMonth() + 1 < 10 ?
			'0' + (date.getMonth() + 1) :
			date.getMonth() + 1) + '-'
	var D = (date.getDate() < 10 ? '0' + date.getDate() : date.getDate()) + ' '
	var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':'
	var m =
		(date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':'
	var s = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds()
	if (flag) {
		return Y + M + D
	} else {
		return Y + M + D + h + m + s
	}
}

function time2date(micro_second) {
	var time = {}
	// 总秒数
	var second = Math.floor(micro_second)
	// 天数
	time.day = PrefixInteger(Math.floor(second / 3600 / 24), 2)
	// 小时
	time.hour = PrefixInteger(Math.floor((second / 3600) % 24), 2)
	// 分钟
	time.minute = PrefixInteger(Math.floor((second / 60) % 60), 2)
	// 秒
	time.second = PrefixInteger(Math.floor(second % 60), 2)

	var newtime = ''
	if (time.day > 0) {
		newtime =
			time.day +
			'天' +
			time.hour +
			'小时' +
			time.minute +
			'分' +
			time.second +
			'秒'
	} else {
		if (time.hour != 0) {
			newtime = time.hour + '小时' + time.minute + '分' + time.second + '秒'
		} else {
			newtime = time.minute + '分' + time.second + '秒'
		}
	}
	return newtime
}

function timeToDateObj(micro_second) {
	var time = {}
	// 总秒数
	var second = Math.floor(micro_second)
	// 天数
	time.day = Math.floor(second / 3600 / 24)
	// 小时
	time.hour = Math.floor((second / 3600) % 24)
	// 分钟
	time.minute = Math.floor((second / 60) % 60)
	// 秒
	time.second = Math.floor(second % 60)

	return time

}

//货币格式化
function formatMoney(number, places, symbol, thousand, decimal) {
	// console.log(number)
	// console.log(places)
	number = number || 0
	places = !isNaN((places = Math.abs(places))) ? places : 2
	symbol = symbol !== undefined ? symbol : '￥'
	thousand = thousand || ','
	decimal = decimal || '.'
	var negative = number < 0 ? '-' : '',
		i = parseInt((number = Math.abs(+number || 0).toFixed(places)), 10) + '',
		j = (j = i.length) > 3 ? j % 3 : 0
	return (
		symbol +
		negative +
		(j ? i.substr(0, j) + thousand : '') +
		i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand) +
		(places ?
			decimal +
			Math.abs(number - i)
			.toFixed(places)
			.slice(2) :
			'')
	)
}
//金额格式化还原
function rmoney(s)
{
	return parseFloat(s.replace(/[^\d\.-]/g, ""));
}
function throttle(fn, context, delay) {
	clearTimeout(fn.timeoutId)
	fn.timeoutId = setTimeout(function() {
		fn.call(context)
	}, delay)
}

// 时间格式化输出，如11:03 25:19 每1s都会调用一次
function dateformat(micro_second) {
	var time = {}
	// 总秒数
	var second = Math.floor(micro_second / 1000) // 天数
	time.day = PrefixInteger(Math.floor(second / 3600 / 24), 2) // 小时
	time.hour = PrefixInteger(Math.floor((second / 3600) % 24), 2) // 分钟
	time.minute = PrefixInteger(Math.floor((second / 60) % 60), 2) // 秒
	time.second = PrefixInteger(Math.floor(second % 60), 2)
	return time
}

//不足位数前面补0
function PrefixInteger(num, length) {
	return (Array(length).join('0') + num).slice(-length)
}

//验证是否是手机号
function isPhoneNumber(str) {
	var myreg = /^[1][3,4,5,6,7,8,9][0-9]{9}$/
	if (!myreg.test(str)) {
		return false
	} else {
		return true
	}
}

/**
 *
 * 对象参数转为url参数
 *
 */
function builderUrlParams(url, data) {
	if (typeof url == 'undefined' || url == null || url == '') {
		return ''
	}
	if (typeof data == 'undefined' || data == null || typeof data != 'object') {
		return ''
	}
	url += url.indexOf('?') != -1 ? '' : '?'
	for (var k in data) {
		url += (url.indexOf('=') != -1 ? '&' : '') + k + '=' + encodeURI(data[k])
	}
	return url
}

/**
 * 使用循环的方式判断一个元素是否存在于一个数组中
 * @param {Object} arr 数组
 * @param {Object} value 元素值
 */
function isInArray(arr, value) {
	for (var i = 0; i < arr.length; i++) {
		if (value === arr[i]) {
			return true
		}
	}
	return false
}
/**
 * 统一跳转
 */
function navigateTo(url) {
	uni.navigateTo({
		url: url,
		animationType: 'pop-in',
		animationDuration: 300
	})
}

/**
 *  关闭当前页面并跳转
 */
function redirectTo(url) {
	uni.redirectTo({
		url: url,
		animationType: 'pop-in',
		animationDuration: 300
	})
}

/**
 * 获取url参数
 *
 * @param {*} name
 * @param {*} [url=window.location.serach]
 * @returns
 */
function getQueryString(name, url) {
	var url = url || window.location.href
	var reg = new RegExp('(^|&|/?)' + name + '=([^&|/?]*)(&|/?|$)', 'i')
	var r = url.substr(1).match(reg)
	if (r != null) {
		return r[2]
	}
	return null
}

/**
 *
 *  判断是否在微信浏览器 true是
 */
function isWeiXinBrowser() {
	// #ifdef H5
	// window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
	let ua = window.navigator.userAgent.toLowerCase()
	// 通过正则表达式匹配ua中是否含有MicroMessenger字符串
	if (ua.match(/MicroMessenger/i) == 'micromessenger') {
		return true
	} else {
		return false
	}
	// #endif

	return false
}

/**
 * 金额相加
 * @param {Object} value1
 * @param {Object} value2
 */
function moneySum(value1, value2) {
	return (parseFloat(value1) + parseFloat(value2)).toFixed(2);
}
/**
 * 金额相减
 * @param {Object} value1
 * @param {Object} value2
 */
function moneySub(value1, value2) {
	let res = (parseFloat(value1) - parseFloat(value2)).toFixed(2);
	return res > 0 ? res : 0;
}


/**
 * 分享URL解压缩
 * @param {Object} url
 */
function shareParameterEncode(url) {
	let urlArray = url.split('-');
	let newUrl = 'type=' + urlArray[0] + '&invite=' + urlArray[1] + '&id=' + urlArray[2] + '&team_id=' + urlArray[3] +
		'&id_type=' + urlArray[4] + '&page_code=' + urlArray[5] + '&group_id=' + urlArray[6];
	return newUrl;
}


/**
 * 分享URL压缩
 * @param {Object} url
 */
function shareParameterDecode(url) {
	var urlArray = url.split('&');
	var allParameter = {
		'type': '',
		'invite': '',
		'id': '',
		'team_id': '',
		'id_type': '',
		'page_code': '',
		'group_id': '',
	};
	for (var i = 0; i < urlArray.length; i++) {
		var parameter = urlArray[i].split('=');
		allParameter[parameter[0]] = parameter[1];
	}
	var newUrl = allParameter.type + '-' + allParameter.invite + '-' + allParameter.id + '-' + allParameter.team_id + '-' +
		allParameter.id_type + '-' + allParameter.page_code + '-' + allParameter.group_id;
	return newUrl;
}

export {
	deepCopy,
	jumpToLogin,
	timeToDate,
	formatMoney,
	/* errorToBack, */
	successToShow,
	throttle,
	errorToShow,
	time2date,
	isPhoneNumber,
	isInArray,
	loadToShow,
	loadToHide,
	navigateTo,
	redirectTo,
	modelShow,
	builderUrlParams,
	isWeiXinBrowser,
	dateformat,
	getQueryString,
	timeToDateObj,
	moneySum,
	moneySub,
	shareParameterEncode,
	shareParameterDecode,
	rmoney
}
