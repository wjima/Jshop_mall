import _this from '../main'

// 设置localStorage
function setStorage(name, content) {
    if (!name) return
    if (typeof content !== 'string') {
        content = JSON.stringify(content)
    }
    window.localStorage.setItem(name, content)
}

// 获取localStorage
function getStorage(name) {
    if (!name) return
    return window.localStorage.getItem(name)
}

// 删除localStorage
function removeStorage(name) {
    if (!name) return
    window.localStorage.removeItem(name)
}

// 把obj对象里的值覆盖到newobj里面
function deepCopy(newobj, obj) {
    if (typeof obj !== 'object') {
        return obj
    }
    for (let attr in obj) {
        let a = {}
        if (newobj[attr]) {
            a = newobj[attr]
        }
        newobj[attr] = deepCopy(a, obj[attr])
    }
    return newobj
}

// 跳转到登陆页面
function jumpToLogin() {
    _this.$router.push({
        path: '/login',
        query: {
            redirect: _this.$route.fullPath
        }
    })
}

// 当出错的时候，显示错误信息，并且跳转
function errorToBack(msg = '出错了，请重试', time = 1500) {
    _this.$dialog.toast({
        mes: msg,
        timeout: time
    })
}
// 操作成功后，的提示信息
function successToShow(msg = '保存成功', callback = function() {}) {
    // wx.showToast({
    //   title: msg,
    //   icon: 'success',
    //   duration: 2000,
    // });
    // setTimeout(function () {
    //   callback();
    // }, 1500);
}

// 时间戳转时间格式
function timeToDate(date) {
    let dateTime = new Date(date * 1000) // 如果date为13位不需要乘1000
    let Y = dateTime.getFullYear() + '-'
    let M = (dateTime.getMonth() + 1 < 10 ? '0' + (dateTime.getMonth() + 1) : dateTime.getMonth() + 1) + '-'
    let D = (dateTime.getDate() < 10 ? '0' + (dateTime.getDate()) : dateTime.getDate()) + ' '
    let h = (dateTime.getHours() < 10 ? '0' + dateTime.getHours() : dateTime.getHours()) + ':'
    let m = (dateTime.getMinutes() < 10 ? '0' + dateTime.getMinutes() : dateTime.getMinutes()) + ':'
    let s = (dateTime.getSeconds() < 10 ? '0' + dateTime.getSeconds() : dateTime.getSeconds())
    return Y + M + D + h + m + s
}

// 货币格式化
function formatMoney(number, places, symbol, thousand, decimal) {
    number = number || 0
    places = !isNaN(places = Math.abs(places)) ? places : 2
    symbol = symbol !== undefined ? symbol : '￥'
    thousand = thousand || ','
    decimal = decimal || '.'
    let negative = number < 0 ? '-' : ''
    let i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + ''
    let j = (i.length) > 3 ? i.length % 3 : 0
    return symbol + negative + (j ? i.substr(0, j) + thousand : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : '')
}

function throttle(fn, context, delay) {
    clearTimeout(fn.timeoutId)
    fn.timeoutId = setTimeout(function() {
        fn.call(context)
    }, delay)
}

// 获取验证码
function getCaptcha() {
    let randomNumber = Math.random() * 10 + 1
    return window.apiUrl.replace('api', 'captcha') + '?' + randomNumber
}

function hecong() {
    // 测试环境
    let entId
    if (process.env.NODE_ENV === 'development') {
        entId = '10519'
    } else if (process.env.NODE_ENV === 'production') {
        // 正式环境
        entId = window.entId
    }
    return entId
}

export default {
    setStorage: setStorage,
    getStorage: getStorage,
    removeStorage: removeStorage,
    deepCopy: deepCopy,
    jumpToLogin: jumpToLogin,
    timeToDate: timeToDate,
    formatMoney: formatMoney,
    errorToBack: errorToBack,
    successToShow: successToShow,
    throttle: throttle,
    getCaptcha: getCaptcha,
    hecong: hecong
}