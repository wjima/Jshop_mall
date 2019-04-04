<template>
    <div class="paysuccess">
        <div>
            <div class="paysuccess-t" v-if="orderInfo.pay_status === 2 && orderInfo.payment_time >0">
                <img class="paysuccess-img" src="../../../static/image/win.png"/>
                <p class="paysuccess-tip">支付成功</p>
                <p class="paysuccess-price">￥<i>{{ orderInfo.payed }}</i></p>
                <!--<p class="paysuccess-time">2019-02-13 18:20</p>-->
            </div>
            <div class="paysuccess-t" v-else>
                <img class="paysuccess-img" src="../../../static/image/pastdue.png"/>
                <p class="paysuccess-tip">支付失败</p>
                <!--<p class="paysuccess-time">2019-02-13 18:20</p>-->
            </div>
            <div class="paysuccess-m">
                <p class="paysuccess-name">支付方式: {{ orderInfo.payment_name }}</p>
                <!--<p class="paysuccess-price">￥<i>666.66</i></p>-->
            </div>
            <div class="paysuccess-b">
                <button @click="showOrderDetail">查看详情</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            orderId: this.$route.query.order_id,
            orderInfo: {
                pay_status: 2,
                payment_name: '微信支付',
                payment_time: 1 // 默认展示成功
            } // 支付结果
        }
    },
    mounted () {
        if (this.orderId) {
            this.getOrderInfo()
        }else{
            this.$dialog.toast({mes:'关键参数丢失'});
        }
    },
    methods: {
        // 查询订单详情 获取订单支付状态
        getOrderInfo () {
            let data = {
                order_id: this.orderId
            }
            this.$api.orderDetail(data, res => {
                this.orderInfo = res.data
            })
        },
        showOrderDetail () {
            this.$router.replace({path: '/orderdetail', query: {order_id: this.orderId}})
        }
    }
}
</script>

<style>
    .paysuccess{
        padding: 2rem 1rem;
        background-color: #fff;
        height: 100%;
    }
    .paysuccess-t{
        margin-bottom: .5rem;
    }
    .paysuccess-img{
        width: 1.5rem;
        height: 1.5rem;
        margin-bottom: .1rem;
    }
    .paysuccess-tip{
        font-size: .3rem;
        color: #333;
        margin-bottom: .2rem;
    }
    .paysuccess-time{
        font-size: .22rem;
        color: #999;
    }
    .paysuccess-m{
        margin-bottom: .7rem;
    }
    .paysuccess-name{
        font-size: .24rem;
        color: #666;
        margin-bottom: .2rem;
    }
    .paysuccess-price{
        font-size: .3rem;
        color: #F43530;
    }
    .paysuccess-price i{
        font-size: .55rem;
    }
    .paysuccess-b button{
        width: 100%;
        height: 1rem;
        line-height: 1rem;
        font-size: .36rem;
        color: #F43530;
        border: 1px solid #f43530;
        border-radius: 5px;
    }
</style>