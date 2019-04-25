<template>
    <div class="recharge">
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">余额(元)：</span>
                <span slot="right">{{ balance }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">充值金额：</span>
                <yd-input slot="right" type="number" v-model="money" placeholder="请输入要充值的金额"></yd-input>
            </yd-cell-item>

            
        </yd-cell-group>
		<div class="recharge-btn">
		    <yd-button size="large" type="danger" @click.native="toRecharge()">去支付</yd-button>
		</div>
<!--		<div v-else>-->
<!--		    <yd-button size="large" type="hollow">暂无可充值方式</yd-button>-->
<!--		</div>-->
    </div>
</template>

<script>
export default {
    data() {
        return {
            balance: '0.00', // 默认余额展示
            userInfo: {}, // 用户余额
            paymentList: [], // 充值方式列表
            money: '' // 充值金额
        }
    },
    mounted () {
        this.getUserInfo()
        // 获取充值方式列表
        // this.getPayments()
    },
    methods: {
        getUserInfo () {
            this.$api.userInfo({}, res => {
                if (res.status) {
                    this.userInfo = res.data
                    this.balance = this.userInfo.balance
                }
            })
        },
        // 获取支付方式列表
        // getPayments () {
        //     this.$api.paymentList({}, res => {
        //         let _list = res.data
        //         let ali = _list.filter(item => item.code === 'alipay')
        //         let wx = _list.filter(item => item.code === 'wechatpay')
        //         this.paymentList = [...ali, ...wx]
        //     })
        // },
        // 去支付充值
        toRecharge () {
            if (!Number(this.money)) {
                this.$dialog.toast({
                    mes: '请选择要充值的金额',
                    timeout:1300
                })
            } else {
                this.$router.replace({
                    path: '/cashierdesk',
                    query: {
                        ids: this.userInfo.id,
                        recharge: Number(this.money),
                        type: 2
                    }
                })
            }
        }
        // 去充值
        // toRecharge (code) {
        //     if (code === 'wechatpay') {
        //         let isWeiXin = this.GLOBAL.isWeiXinBrowser()
        //         if (isWeiXin) {
        //             // 公众号支付参数
        //             let data = {
        //                 ids: this.userInfo.id,
        //                 payment_code: code,
        //                 payment_type: 2,
        //                 params: {
        //                     trade_type: 'JSAPI_OFFICIAL',
        //                     money: this.recharge
        //                 }
        //             }
        //             // 微信jsapi支付
        //             this.$api.pay(data, res => {
        //                 if (res.status) {
        //                     const data = res.data
        //                     let _this = this
        //                     WeixinJSBridge.invoke(
        //                         'getBrandWCPayRequest', {
        //                             'appId': data.appid, // 公众号名称，由商户传入
        //                             'timeStamp': data.timeStamp, // 时间戳，自1970年以来的秒数
        //                             'nonceStr': data.nonceStr, // 随机串
        //                             'package': data.package,
        //                             'signType': data.signType, // 微信签名方式：
        //                             'paySign': data.paySign // 微信签名
        //                         },
        //                         function (res) {
        //                             if (res.err_msg === 'get_brand_wcpay_request:ok') {
        //                                 _this.$dialog.toast({
        //                                     mes: '充值成功',
        //                                     callback () {
        //                                         _this.$router.back(-1)
        //                                     }
        //                                 })
        //                                 // 使用以上方式判断前端返回,微信团队郑重提示：
        //                                 // res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
        //                             }
        //                         })
        //                 }
        //             })
        //         } else {
        //             // h5端支付参数
        //             let data = {
        //                 ids: this.userInfo.id,
        //                 payment_code: code,
        //                 payment_type: 2,
        //                 params: {
        //                     money: this.recharge,
        //                     trade_type: 'MWEB',
        //                     return_url: this.GLOBAL.locationHost() + '/#/balance'
        //                 }
        //             }
        //             // 微信h5支付
        //             this.$api.pay(data, res => {
        //                 if (res.status) {
        //                     window.location.href = res.data.mweb_url
        //                 }
        //             })
        //         }
        //     } else if (code === 'alipay') {
        //         let data = {
        //             ids: this.userInfo.id,
        //             payment_code: code,
        //             payment_type: 2,
        //             params: {
        //                 trade_type: 'WAP',
        //                 money: this.recharge,
        //                 return_url: this.GLOBAL.locationHost() + '/#/balance'
        //             }
        //         }
        //         this.$api.pay(data, res => {
        //             if (res.status) {
        //                 this.StandardPost(res.data.url, res.data.data)
        //             }
        //         })
        //     }
        // },
        // // alipay 模拟get提交
        // StandardPost (url, data) {
        //     let tempForm = document.createElement('form')
        //     tempForm.id = 'aliPay'
        //     tempForm.methods = 'post'
        //     tempForm.action = url
        //     tempForm.target = '_self'
        //     let input = []
        //     for (let k in data) {
        //         input[k] = document.createElement('input')
        //         input[k].type = 'hidden'
        //         input[k].name = k
        //         input[k].value = data[k]
        //         tempForm.appendChild(input[k])
        //     }
        //     tempForm.addEventListener('submit', function () {}, false)
        //     document.body.appendChild(tempForm)
        //     tempForm.dispatchEvent(new Event('submit'))
        //     tempForm.submit()
        //     document.body.removeChild(tempForm)
        // }
    }
}
</script>

<style scoped>
.recharge{
    background-color: #fff;
    height: 100vh;
}
.recharge-btn{
    padding: 0 .5rem;
}
.recharge-btn .btn{
    background-color: #FF3B44;
}
.recharge .yd-cell-item:not(:last-child):after{
	border-bottom: 1px solid #e9e9e9;
}
.recharge .yd-cell:after{
	border: none !important;
	background-image: none !important;
	
}
.yd-cell{
	background-color: #000;
}
</style>