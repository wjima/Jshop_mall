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
import payment from '../components/Payment.vue'
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
                let params = {
                    trade_type: 'MWEB',
                    wap_url: window.location.protocol + '//' + window.location.host, // 'http://h5.jihainet.com',
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
                        window.location.href = res.data.mweb_url
                    } else {
                        this.$dialog.alert({mes: res.msg})
                    }
                })
            } else if (code === 'alipay') {
                let params = {
                    trade_type: 'MWEB',
                    wap_url: window.location.protocol + '//' + window.location.host, // 'http://h5.jihainet.com',
                    return_url: window.location.protocol + '//' + window.location.host + '/#/orderdetail?order_id=' + this.order_id,
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
        }
    }
}
</script>
