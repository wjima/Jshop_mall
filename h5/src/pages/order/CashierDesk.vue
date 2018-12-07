<template>
    <div class="cashierdesk">
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">订单编号</span>
                <span slot="right">{{ order_id }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">订单金额</span>
                <span slot="right">￥{{ order_amount }}</span>
            </yd-cell-item>
        </yd-cell-group>
        <payment :payments="payments" @pay="pay"></payment>
    </div>
</template>

<script>
import payment from '../../components/Payment.vue'
export default {
    components: {
        payment
    },
    data () {
        return {
            order_id: this.$route.query.order_id,
            order_amount: '', // 订单总价
            payments: [] // 商户可支付的方式列表
        }
    },
    created () {
        this.orderDetail()
        this.getPaymentType()
    },
    methods: {
        orderDetail () {
            this.$api.orderDetail({order_id: this.order_id}, res => {
                this.order_amount = res.data.order_amount
            })
        },
        getPaymentType () {
            this.$api.paymentList({}, res => {
                let data = res.data
                for (let k in data) {
                    data[k].img = './static/image/' + data[k].code + '.png'
                }
                this.payments = data
            })
        },
        // 根据code 区分支付方式
        pay (code) {
            if (code === 'wechatpay') {
                let isWeiXin = this.isWeiXinBrowser()
                // 微信支付
                let params = {
                    trade_type: isWeiXin ? 'JSAPI_OFFICIAL' : 'MWEB',
                    wap_url: this.GLOBAL.locationHost(), // window.location.protocol + '//' + window.location.host, // 'http://h5.jihainet.com',
                    wap_name: 'mysite'
                }
                let data = {
                    ids: this.order_id,
                    payment_code: code,
                    payment_type: 1,
                    params: params
                }

                if (isWeiXin) {
                    // 微信jsapi支付
                    this.$api.pay(data, res => {
                        if (res.status) {
                            const data = res.data
                            let _this = this
                            WeixinJSBridge.invoke(
                                'getBrandWCPayRequest', {
                                    "appId": data.appid,     //公众号名称，由商户传入
                                    "timeStamp": data.timeStamp,         //时间戳，自1970年以来的秒数
                                    "nonceStr": data.nonceStr, //随机串
                                    "package": data.package,
                                    "signType": data.signType,         //微信签名方式：
                                    "paySign": data.paySign //微信签名
                                },
                                function(res){
                                    if(res.err_msg == "get_brand_wcpay_request:ok" ){
                                        _this.$dialog.alert({
                                            mes: '支付成功',
                                            callback () {
                                                _this.$router.replace({path: '/orderdetail', query: {order_id: _this.order_id}})
                                            }
                                        })
                                        // 使用以上方式判断前端返回,微信团队郑重提示：
                                        //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                                    } else {
                                        _this.$dialog.alert({mes: '支付失败'})
                                    }
                                });
                        }
                    })
                } else {
                    // h5支付
                    this.$api.pay(data, res => {
                        if (res.status) {
                            window.location.href = res.data.mweb_url
                        } else {
                            this.$dialog.alert({mes: res.msg})
                        }
                    })
                }
            } else if (code === 'alipay') {
                let params = {
                    trade_type: 'MWEB',
                    wap_url: this.GLOBAL.locationHost(), // window.location.protocol + '//' + window.location.host, // 'http://h5.jihainet.com',
                    return_url: this.GLOBAL.locationHost() + '/#/orderdetail?order_id=' + this.order_id,
                    wap_name: 'mysite'
                }
                let data = {
                    ids: this.order_id,
                    payment_code: code,
                    payment_type: 1,
                    params: params
                }
                this.$api.pay(data, res => {
                    if (res.status) {
                        this.StandardPost(res.data.url, res.data.data)
                    }
                })
            } else if (code === 'balancepay') {
                let data = {
                    ids: this.order_id,
                    payment_code: code,
                    payment_type: 1
                }
                this.$api.pay(data, res => {
                    if (res.status) {
                        this.$dialog.toast({
                            mes: res.msg,
                            timeout: 1300,
                            callback: () => {
                                this.$router.replace({path: '/orderdetail', query: {order_id: this.order_id}})
                            }
                        })
                    }
                })
            } else if (code === 'offline') {
                this.$dialog.confirm({
                    title: '线下支付说明',
                    mes: '请联系客服进行线下支付',
                    opts: [
                        {
                            txt: '订单详情',
                            color: false,
                            callback: () => {
                                this.$router.push({path: '/orderdetail', query: {order_id: this.order_id}})
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
        // 判断是否是微信浏览器发起不同的微信支付请求
        isWeiXinBrowser () {
            //window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
            let ua = window.navigator.userAgent.toLowerCase();
            //通过正则表达式匹配ua中是否含有MicroMessenger字符串
            return ua.match(/MicroMessenger/i) == 'micromessenger' ? true : false
        }
    },
    watch: {

    }
}
</script>
