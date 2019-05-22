<template>
    <div class="cashierdesk">
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">订单类型</span>
                <span slot="right" v-html="type == 1 ? '商品订单' : '充值订单'"></span>
            </yd-cell-item>
            <yd-cell-item v-if="type == 1">
                <span slot="left">订单编号</span>
                <span slot="right">{{ ids }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">订单金额</span>
                <span slot="right">￥{{ money }}</span>
            </yd-cell-item>
        </yd-cell-group>
        <payment :payments="payments" @pay="pay" :money="userInfo.balance"></payment>
        <yd-popup class="payresult"
        v-model="popShow"
        position="center"
        width="70%"
        :close-on-masker="false"
        >
            <div>
                <div class="payresult-i">请确认微信支付是否已完成</div>
                <div class="payresult-i" @click="completed">已完成支付</div>
                <div class="payresult-i" @click="heavyLoad">支付遇到问题,重新支付</div>
            </div>
        </yd-popup>
    </div>
</template>

<script>
import payment from '../../components/Payment.vue'
export default {
    components: {
        payment
    },
    // inject: ['reload'],
    data () {
        return {
            ids: this.$route.query.ids, // 订单号
            type: this.$route.query.type || 1, // 订单类型
            money: this.$route.query.recharge || 0, // 充值单要充值的金额
            order_amount: '', // 订单总价
            payments: [], // 商户可支付的方式列表
            userInfo: {}, // 用户信息
            popShow: false,
            // payType: this.$route.query.pay_type || '', //支付方式
            // payname: this.$route.query.pay_type || '',
            // timer: ''
        }
    },
    created () {
        // this.orderDetail()
        // this.getUserInfo()
        // if(this.pay_type!=''){
        //     if (this.pay_type == 'weixin') {
        //         this.payname = '微信支付'
        //         this.paymentedHandle()
        //     } else if (this.pay_type == 'alipay') {
        //         this.payname = '支付宝支付'
        //         this.paymentedHandle()
        //     }
        //
        // } else {
        //     if(this.popShow) {
        //         this.popShow = false
        //     }
        // }
        // this.timer = setInterval(() => {
        //     this.getOrderInfo()
        // },3000)
        if (this.ids && this.type == 1) {
            // 商品订单
            this.orderDetail()
        } else if (this.money && this.type == 2) {
            // 充值订单 获取用户id
            // this.getUserInfo()
        } else {
            this.$dialog.toast({
                mes: '订单支付参数错误',
                timeout: 1300,
                callback: () => {
                    this.$router.go(-1)
                }
            })
        }

        this.getUserInfo()
        this.getPaymentType()
    },
    computed: {
        payType: {
            get () {

            },
            set (val) {
                if (val === 'weixin') {
                    this.popShow = true
                }
            }
        }
    },
    methods: {
        // 获取订单详情
        orderDetail () {
            this.$api.orderDetail({order_id: this.ids}, res => {
                this.money = res.data.order_amount
            })
        },
        // 获取支付方式列表
        getPaymentType () {
            this.$api.paymentList({}, res => {
                let data = res.data

                // 过滤非线上支付方式
                if (this.GLOBAL.isWeiXinBrowser()) {
                    // h5支付并且是在微信浏览器内 过滤支付宝支付
                    data = data.filter(item => item.code !== 'alipay')
                }

                // 如果是充值订单 过滤余额支付及线下支付
                if (this.type == 2) {
                    data = data.filter(item => item.code !== 'balancepay' && item.is_online === 1)
                }

                data.forEach(item => {
                    this.$set(item, 'img', './static/image/' + item.code + '.png')
                })
                this.payments = data
            })
        },
        getUserInfo () {
            this.$api.userInfo({}, res => {
                if (res.status) {
                    this.userInfo = res.data
                }
            })
        },
        // 根据code 区分支付方式
        pay (code) {
            let data = {
                ids: this.ids,
                payment_code: code,
                payment_type: this.type
            }
            if (code === 'wechatpay') {
                let isWeiXin = this.GLOBAL.isWeiXinBrowser()
                if (isWeiXin) {
                    // 公众号支付参数
                    if (this.type == 1 && this.ids) {
                        // 微信jsapi支付
                        data['params'] = {
                            trade_type: 'JSAPI_OFFICIAL'
                        }
                    } else if (this.type == 2 && this.money) {
                        data['params'] = {
                            money: this.money
                        }
                    }

                    // 微信jsapi支付
                    this.$api.pay(data, res => {
                        if (res.status) {
                            const data = res.data
                            let _this = this
                            WeixinJSBridge.invoke(
                                'getBrandWCPayRequest', {
                                    'appId': data.appid, // 公众号名称，由商户传入
                                    'timeStamp': data.timeStamp, // 时间戳，自1970年以来的秒数
                                    'nonceStr': data.nonceStr, // 随机串
                                    'package': data.package,
                                    'signType': data.signType, // 微信签名方式：
                                    'paySign': data.paySign // 微信签名
                                },
                                function (res) {
                                    if (res.err_msg === 'get_brand_wcpay_request:ok') {
                                        if (_this.type == 1) {
                                            _this.$router.replace({path: '/payresult', query: {order_id: _this.ids}})
                                        } else if (_this.type == 2) {
                                            _this.$router.replace({path: '/balance'})
                                        }
                                        // _this.$dialog.alert({
                                        //     mes: '支付成功',
                                        //     callback () {
                                        //         _this.$router.replace({path: '/payresult', query: {order_id: _this.orderId}})
                                        //     }
                                        // })
                                        // 使用以上方式判断前端返回,微信团队郑重提示：
                                        // res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                                    }
                                })
                        }
                    })
                } else {
                    // h5端支付参数
                    if (this.type == 1 && this.ids) {
                        data['params'] = {
                            trade_type: 'MWEB',
                            return_url: this.GLOBAL.locationHost() + '/#/cashierdesk?ids=' + this.ids + '&type=' + this.type + '&pay_type=weixin'
                        }
                    } else if (this.type == 2 && this.money) {
                        data['params'] = {
                            trade_type: 'MWEB',
                            money: this.money,
                            return_url: this.GLOBAL.locationHost() + '/#/cashierdesk?ids=' + this.ids + '&type=' + this.type + '&pay_type=weixin'
                        }
                    }
                    // 微信h5支付
                    this.$api.pay(data, res => {
                        if (res.status) {
                            this.payType = 'weixin'
                            location.href = res.data.mweb_url
                        } else {
                            this.$dialog.alert({mes: res.msg})
                        }
                    })
                }
            } else if (code === 'alipay') {
                // 支付宝支付
                if (this.type == 1 && this.ids) {
                    data['params'] = {
                        trade_type: 'WAP',
                        return_url: this.GLOBAL.locationHost() + '/#/payresult?order_id=' + this.ids
                    }
                } else if (this.type == 2 && this.money) {
                    data['params'] = {
                        money: this.money,
                        return_url: this.GLOBAL.locationHost() + '/#/balance'
                    }
                }

                this.$api.pay(data, res => {
                    if (res.status) {
                        this.StandardPost(res.data.url, res.data.data)
                    }
                })
            } else if (code === 'balancepay') {
                // 余额支付
                this.$api.pay(data, res => {
                    if (res.status) {
                        this.$router.replace({path: '/payresult', query: {order_id: this.ids}})
                    }
                })
            } else if (code === 'offline') {
                // 线下支付
                this.$dialog.confirm({
                    title: '线下支付说明',
                    mes: '请联系客服进行线下支付',
                    opts: [
                        {
                            txt: '订单详情',
                            color: false,
                            callback: () => {
                                this.$router.push({path: '/orderdetail', query: {order_id: this.ids}})
                            }
                        },
                        {
                            txt: '继续购物',
                            color: true,
                            callback: () => {
                                this.$router.push('/index')
                            }
                        }
                    ]

                })
            }
        },
	    // 查询订单详情 获取订单支付状态
        // getOrderInfo () {
        //     let data = {
        //         order_id: this.orderIds
        //     }
        //     this.$api.orderDetail(data, res => {
        //         this.orderInfo = res.data
        //         if(this.orderInfo.pay_status === 2){
        //             clearInterval(this.timer)
        //             this.$router.replace({path: '/payresult', query: {order_id: this.orderIds}})
        //         }
        //     })
        // },
        // alipay 模拟get提交
        StandardPost (url, data) {
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
            tempForm.addEventListener('submit', function () {}, false)
            document.body.appendChild(tempForm)
            tempForm.dispatchEvent(new Event('submit'))
            tempForm.submit()
            document.body.removeChild(tempForm)
        },
        // 微信H5支付完成后触发的弹窗事件
        // paymentedHandle () {
        //     this.popShow = true
        // },
        // 支付完后点击跳转
        completed () {
            this.popShow = false // 关闭弹窗
            if (this.type == 1) {
                this.$router.replace({path: '/payresult', query: {order_id: this.ids}})
            } else {
                this.$router.replace({path: '/balance'})
            }
        },
        // 支付遇到问题重新刷新页面
        heavyLoad () {
            this.popShow = false // 关闭弹窗
            this.$router.replace({path: '/cashierdesk', query: {ids: this.ids, type: this.type}})
        }
    },
    watch: {
        // pay_type () {
        //     if (pay_type === 'weixin') {
        //         this.payname = '微信支付'
        //     }
        //
        //     if (pay_type === 'alipay') {
        //         this.payname = '支付宝支付'
        //     }
        //     this.paymentedHandle()
        // }
    }
}
</script>

<style>
    .payresult .yd-popup-center .yd-popup-content{
        text-align: center;
        /* padding: 20px 0; */
        background-color: #fff;
        border-radius: 5px;
    }
    .payresult-i{
        border-bottom: 1px solid #e8e8e8;
        padding: 15px
    }
    .payresult-i:nth-child(2){
        color: #ff3b44;
    }
    .payresult-i:last-child{
        border: none;
        color: #999;
    }
</style>
