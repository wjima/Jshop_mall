import axios from 'axios'
import qs from 'qs'
import _this from '../main'
import common from './common'
import wx from 'weixin-js-sdk'
import {
    host,
    apiUrl
} from './serviceUrl'

// 需要登陆的，都写到这里，否则就是不需要登陆的接口
let methodToken = [
    'user.info',
    'user.editinfo',
    'user.changeavatar',
    'user.logout',
    'user.addgoodsbrowsing',
    'user.delgoodsbrowsing',
    'user.goodsbrowsing',
    'user.goodscollection',
    'user.goodscollectionlist',
    'user.saveusership',
    'user.vuesaveusership',
    'user.getshipdetail',
    'user.setdefship',
    'user.editship',
    'user.removeship',
    'user.getusership',
    'user.pay',
    'user.orderevaluate',
    'user.getuserdefaultship',
    'user.issign',
    'user.sign',
    'user.mypoint',
    'user.pointlog',
    'user.getbankcardlist',
    'user.getdefaultbankcard',
    'user.addbankcard',
    'user.removebankcard',
    'user.setdefaultbankcard',
    'user.getbankcardinfo',
    'user.editpwd',
    'user.forgotpwd',
    'user.recommend',
    'user.balancelist',
    'user.sharecode',
    'user.cash',
    'user.cashlist',
    'coupon.getcoupon',
    'coupon.usercoupon',
    'cart.add',
    'cart.del',
    'cart.getlist',
    'cart.setnums',
    'cart.getnumber',
    'order.cancel',
    'order.del',
    'order.details',
    'order.confirm',
    'order.getlist',
    'order.create',
    'order.getship',
    'order.getorderlist',
    'order.getorderstatusnum',
    'order.aftersaleslist',
    'order.aftersalesinfo',
    'order.aftersalesstatus',
    'order.addaftersales',
    'order.sendreship',
    'order.iscomment',
    'payments.getinfo',
    'user.getuserpoint',
    'coupon.getcouponkey',
    'store.isclerk',
    'store.storeladinglist',
    'store.ladinginfo',
    'store.lading',
    'store.ladingdel'
]

// 接口token验证
const post = (method, data, callback) => {
    // 如果是需要登陆的，增加token
    if (methodToken.indexOf(method) >= 0) {
        let userToken = common.getStorage('user_token')
        if (!userToken) {
            common.jumpToLogin()
            return false
        } else {
            data.token = userToken
        }
    }
    data.method = method
    sendPost(apiUrl, qs.stringify(data), {}, callback)
}

// axios 发送请求统一处理
const sendPost = (url, data, config = {}, callback) => {
    if (Object.keys(config).length) {
        _this.$dialog.loading.open('上传中...')
    }
    axios.post(url, data, config).then(response => {
        if (Object.keys(config).length) {
            _this.$dialog.loading.close()
        }
        // _this.$dialog.loading.close()
        if (!response.data.status) {
            // 输出错误显示
            common.errorToBack(response.data.msg)
            if (response.data.data === 14007 || response.data.data === 14006) {
                // 用户未登录或者token过期 清空本地user_token
                common.removeStorage('user_token')
                    // 跳转至登录
                common.jumpToLogin()
            }
        }
        callback(response.data)
    }).catch(err => {
        if (err && err.response) {
            switch (err.response.status) {
                case 400:
                    err.message = '请求参数错误'
                    break
                case 401:
                    err.message = '未授权，请登录'
                    break
                case 403:
                    err.message = '跨域拒绝访问'
                    break
                case 404:
                    err.message = `请求地址出错: ${err.response.config.url}`
                    break
                case 408:
                    err.message = '请求超时'
                    break
                case 500:
                    err.message = '服务器内部错误'
                    break
                case 501:
                    err.message = '服务未实现'
                    break
                case 502:
                    err.message = '网关错误'
                    break
                case 503:
                    err.message = '服务不可用'
                    break
                case 504:
                    err.message = '网关超时'
                    break
                case 505:
                    err.message = 'HTTP版本不受支持'
                    break
                default:
                    break
            }
            _this.$dialog.loading.close()
            common.errorToBack(err.message)
        }
    })
}

// 图片上传
export const uploadFile = (data, callback) => {
    data.append('method', 'images.upload')
    let param = {
        headers: { 'Content-Type': 'multipart/form-data' }
    }
    sendPost(apiUrl, data, param, callback)
}

// 获取店铺配置
export const shopConfig = () => axios.get(host + '/api/common/jshopconf').then(response => response.data)

// 用户注册
export const reg = (data, callback) => post('user.reg', data, callback)

// 用户登录
export const login = (data, callback) => post('user.login', data, callback)

// 用户信息
export const userInfo = (data, callback) => post('user.info', data, callback)

// 上传头像
export const changeAvatar = (data, callback) => post('user.changeavatar', data, callback)

// 编辑用户信息
export const editInfo = (data, callback) => post('user.editinfo', data, callback)

// 发送短信验证码
export const sms = (data, callback) => post('user.sms', data, callback)

// 短信验证码登录
export const smsLogin = (data, callback) => post('user.smslogin', data, callback)

// 退出登录
export const logout = (data, callback) => post('user.logout', data, callback)

// 获取首页幻灯片
export const slider = (data, callback) => post('advert.getAdvertList', data, callback)

// 获取公告列表
export const notice = (data, callback) => post('notice.noticeList', data, callback)

// 获取公告详情
export const noticeInfo = (data, callback) => post('notice.noticeInfo', data, callback)

// 获取文章详情
export const articleInfo = (data, callback) => post('articles.getArticleDetail', data, callback)

// 获取文章列表
export const articleList = (data, callback) => post('articles.getArticleList', data, callback)

// 获取商品分类
export const categories = (data, callback) => post('categories.getallcat', data, callback)

// 获取商品列表
export const goodsList = (data, callback) => post('goods.getlist', data, callback)

// 获取商品详情
export const goodsDetail = (data, callback) => post('goods.getdetial', data, callback)

// 获取商品参数信息
export const goodsParams = (data, callback) => post('goods.getgoodsparams', data, callback)

// 获取设置默认货品
export const getProductInfo = (data, callback) => post('goods.getproductinfo', data, callback)

// 获取商品评论信息
export const goodsComment = (data, callback) => post('goods.getgoodscomment', data, callback)

// 添加购物车
export const addCart = (data, callback) => post('cart.add', data, callback)

// 移除购物车
export const removeCart = (data, callback) => post('cart.del', data, callback)

// 获取购物车列表
export const cartList = (data, callback) => post('cart.getlist', data, callback)

// 设置购物车商品数量
export const setCartNum = (data, callback) => post('cart.setnums', data, callback)

// 获取购物车数量
export const getCartNum = (data, callback) => post('cart.getnumber', data, callback)

// 获取用户的收货地址列表
export const userShip = (data, callback) => post('user.getusership', data, callback)

// 获取用户默认收货地址
export const userDefaultShip = (data, callback) => post('user.getuserdefaultship', data, callback)

// 存储用户收货地址
export const saveUserShip = (data, callback) => post('user.vuesaveusership', data, callback)

// 获取收货地址详情
export const shipDetail = (data, callback) => post('user.getshipdetail', data, callback)

// 收货地址编辑
export const editShip = (data, callback) => post('user.editship', data, callback)

// 收货地址删除
export const removeShip = (data, callback) => post('user.removeship', data, callback)

// 设置默认收货地址
export const setDefShip = (data, callback) => post('user.setdefship', data, callback)

// 生成订单
export const createOrder = (data, callback) => post('order.create', data, callback)

// 获取状态订单列表
export const getOrderList = (data, callback) => post('order.getlist', data, callback)

// 取消订单
export const cancelOrder = (data, callback) => post('order.cancel', data, callback)

// 删除订单
export const delOrder = (data, callback) => post('order.del', data, callback)

// 获取订单详情
export const orderDetail = (data, callback) => post('order.details', data, callback)

// 确认收货
export const confirmOrder = (data, callback) => post('order.confirm', data, callback)

// 获取配送方式
export const orderShip = (data, callback) => post('order.getship', data, callback)

// 获取全部订单列表
export const orderList = (data, callback) => post('order.getorderlist', data, callback)

// 获取订单不同状态的数量
export const getOrderStatusSum = (data, callback) => post('order.getorderstatusnum', data, callback)

// 售后单列表
export const afterSalesList = (data, callback) => post('order.aftersaleslist', data, callback)

// 售后单详情
export const afterSalesInfo = (data, callback) => post('order.aftersalesinfo', data, callback)

// 订单售后状态
export const afterSalesStatus = (data, callback) => post('order.aftersalesstatus', data, callback)

// 添加售后单
export const addAfterSales = (data, callback) => post('order.addaftersales', data, callback)

// 用户发送退货包裹
export const sendShip = (data, callback) => post('order.sendreship', data, callback)

// 添加商品浏览足迹
export const addGoodsBrowsing = (data, callback) => post('user.addgoodsbrowsing', data, callback)

// 删除商品浏览足迹
export const delGoodsBrowsing = (data, callback) => post('user.delgoodsbrowsing', data, callback)

// 获取商品浏览足迹
export const goodsBrowsing = (data, callback) => post('user.goodsbrowsing', data, callback)

// 商品收藏 关注/取消
export const goodsCollection = (data, callback) => post('user.goodscollection', data, callback)

// 获取商品收藏关注列表
export const goodsCollectionList = (data, callback) => post('user.goodscollectionlist', data, callback)

// 获取店铺支付方式列表
export const paymentList = (data, callback) => post('payments.getlist', data, callback)

// 获取支付单详情
export const paymentInfo = (data, callback) => post('payments.getinfo', data, callback)

// 支付接口
export const pay = (data, callback) => post('user.pay', data, callback)

// 订单评价接口
export const orderEvaluate = (data, callback) => post('user.orderevaluate', data, callback)

// 判断是否签到
export const isSign = (data, callback) => post('user.issign', data, callback)

// 签到接口
export const sign = (data, callback) => post('user.sign', data, callback)

// 我的积分
export const myPoint = (data, callback) => post('user.mypoint', data, callback)

// 积分记录
export const pointLog = (data, callback) => post('user.pointlog', data, callback)

// 物流信息接口
export const logistics = (data, callback) => post('order.logisticbyapi', data, callback)

// 优惠券列表
export const couponList = (data, callback) => post('coupon.couponlist', data, callback)

// 优惠券详情
export const couponDetail = (data, callback) => post('coupon.coupondetail', data, callback)

// 用户领取优惠券
export const getCoupon = (data, callback) => post('coupon.getcoupon', data, callback)

// 用户已领取的优惠券列表
export const userCoupon = (data, callback) => post('coupon.usercoupon', data, callback)

// 获取店铺设置
export const getSetting = (data, callback) => post('user.getsetting', data, callback)

// 获取商户配置信息
export const getSellerSetting = (data, callback) => post('user.getsellersetting', data, callback)

// 获取我的银行卡列表
export const getBankCardList = (data, callback) => post('user.getbankcardlist', data, callback)

// 获取默认的银行卡
export const getDefaultBankCard = (data, callback) => post('user.getdefaultbankcard', data, callback)

// 添加银行卡
export const addBankCard = (data, callback) => post('user.addbankcard', data, callback)

// 删除银行卡
export const removeBankCard = (data, callback) => post('user.removebankcard', data, callback)

// 设置默认银行卡
export const setDefaultBankCard = (data, callback) => post('user.setdefaultbankcard', data, callback)

// 获取银行卡信息
export const getBankCardInfo = (data, callback) => post('user.getbankcardinfo', data, callback)

// 获取银行卡组织信息
export const getBankCardOrganization = (data, callback) => post('user.getbankcardorganization', data, callback)

// 用户修改密码
export const editPwd = (data, callback) => post('user.editpwd', data, callback)

// 用户找回密码
export const forgotPwd = (data, callback) => post('user.forgotpwd', data, callback)

// 获取用户余额明细
export const getBalanceList = (data, callback) => post('user.balancelist', data, callback)

// 用户推荐列表
export const recommendList = (data, callback) => post('user.recommend', data, callback)

// 邀请码
export const shareCode = (data, callback) => post('user.sharecode', data, callback)

// 用户提现
export const userToCash = (data, callback) => post('user.cash', data, callback)

// 用户提现列表
export const cashList = (data, callback) => post('user.cashlist', data, callback)

// 获取授权登录方式
export const getTrustLogin = (data, callback) => post('user.gettrustlogin', data, callback)

// 绑定授权登录
export const trustBind = (data, callback) => post('user.trustbind', data, callback)

// 获取用户信息
export const trustLogin = (data, callback) => post('user.trustcallback', data, callback)

// 判断用户下单可以使用多少积分
export const usablePoint = (data, callback) => post('user.getuserpoint', data, callback)

// 门店列表
export const storeList = (data, callback) => post('store.getstorelist', data, callback)

// 判断是否开启门店自提
export const switchStore = (data, callback) => post('store.getstoreswitch', data, callback)

// 获取默认的门店
export const defaultStore = (data, callback) => post('store.getdefaultstore', data, callback)

// 判断是否开启积分
export const isPoint = (data, callback) => post('user.ispoint', data, callback)

// 用户输入code领取优惠券
export const couponKey = (data, callback) => post('coupon.getcouponkey', data, callback)

// 判断是否是店员
export const isStoreUser = (data, callback) => post('store.isclerk', data, callback)

// 获取店铺提货单列表
export const storeLadingList = (data, callback) => post('store.storeladinglist', data, callback)

// 获取提货单详情
export const ladingInfo = (data, callback) => post('store.ladinginfo', data, callback)

// 店铺提单操作
export const ladingExec = (data, callback) => post('store.lading', data, callback)

// 提货单删除
export const ladingDel = (data, callback) => post('store.ladingdel', data, callback)

// 获取活动列表
export const activityList = (data, callback) => post('group.getlist', data, callback)

// 获取活动详情
export const activityDetail = (data, callback) => post('group.getgoodsdetial', data, callback)

// 微信浏览器分享
export const weixinShare = (shareTitle, shareImg, shareDesc) => {
    let url = location.href.split('#')[0]

    var length = url.indexOf('?')

    if (length > 0) {
        url = url.substr(0, length)
    }

    if (url.indexOf('index.html') >= 0) {
        url = url.replace('index.html', 'redirect.html')
    } else {
        url = url + 'redirect.html'
    }

    url = url + '?redirect=' + encodeURIComponent(location.href)

    var linkShare = location.href.split('#')[0]

    let param = {
        method: 'weixinshare.share',
        url: linkShare
    }

    axios.post(apiUrl, qs.stringify(param)).then(res => {
        var getMsg = res.data.data

        wx.config({
            debug: false, // 生产环境需要关闭debug模式
            appId: getMsg.appId, // appId通过微信服务号后台查看
            timestamp: getMsg.timestamp, // 生成签名的时间戳
            nonceStr: getMsg.nonceStr, // 生成签名的随机字符串
            signature: getMsg.signature, // 签名
            jsApiList: [ // 需要调用的JS接口列表
                'onMenuShareTimeline', // 分享给好友
                'onMenuShareAppMessage' // 分享到朋友圈
            ]
        })
        wx.ready(function() {
            // 分享到朋友圈
            wx.onMenuShareAppMessage({
                    title: shareTitle, // 分享标题
                    desc: shareDesc, // 分享描述
                    link: url,
                    imgUrl: shareImg, // 分享图标
                    success: function() {
                        // 用户确认分享后执行的回调函数
                    }
                })
                // 分享给朋友
            wx.onMenuShareTimeline({
                title: shareTitle, // 分享标题
                desc: shareDesc, // 分享描述
                link: url,
                imgUrl: shareImg, // 分享图标
                success: function() {
                    // 用户确认分享后执行的回调函数

                }
            })
        })
    })
}