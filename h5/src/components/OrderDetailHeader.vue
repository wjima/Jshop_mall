<template>
    <div class="orderdetailheader">
        <div class="" v-if="status === 'pending_payment'">
            <h3>待付款</h3>
            <p>请于 {{ remaining }} 内完成付款操作</p>
        </div>
        <div class="" v-else-if="status === 'pending_delivery'">
            <h3>已付款</h3>
            <p>商家会尽快发货,请等待</p>
        </div>
        <div class="" v-else-if="status === 'pending_receipt'">
            <h3>待签收</h3>
            <p>快递公司: {{ delivery[0].logi_name }}</p>
            <p>快递单号: {{ delivery[0].logi_no }}</p>
        </div>
        <div class="" v-else-if="status === 'pending_evaluate'">
            <h3>待评价</h3>
            <p>订单 {{ remaining }} 后将会自动评价</p>
        </div>
        <div class="" v-else-if="status === 'completed'">
            <h3>交易完成</h3>
            <p></p>
        </div>
        <div class="" v-else-if="status === 'cancel'">
            <h3>交易取消</h3>
            <p></p>
        </div>
        <div class="" v-else>
            <h3>交易成功</h3>
            <p></p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        // 状态
        status: {
            type: [String, Array],
            default () {
                return []
            }
        },
        // 剩余时间
        remaining: {
            type: [String, Array, Boolean],
            default () {
                return []
            }
        },
        // 快递信息
        delivery: {
            type: [Array, Object],
            default () {
                return []
            }
        }
    }
}
</script>

<style>
    .orderdetailheader{
        width: 100%;
        height: 2.3rem;
        background: url(../../static/image/backimg.png) no-repeat;
        background-size: 100% 100%;
    }
    .orderdetailheader>div{
        width: 70%;
        margin: 0 auto;
        text-align: left;
        color: #fff;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }
    .orderdetailheader h3{
        font-size: .3rem;
        margin-bottom: .2rem;
    }
</style>
