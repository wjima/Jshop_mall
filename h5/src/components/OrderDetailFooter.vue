<template>
    <div class="orderdetailfooter" v-if="status === 1">
        <yd-button type="hollow" shape="circle" class="left-btn" @click.native="cancel">取消订单</yd-button>
        <yd-button type="hollow" shape="circle" class="right-btn" @click.native="pay">立即支付</yd-button>
    </div>
    <div class="orderdetailfooter" v-else-if="status === 2">
        <yd-button type="hollow" shape="circle" class="right-btn" @click.native="afterSales">申请售后</yd-button>
    </div>
    <div class="orderdetailfooter" v-else-if="status === 3">
        <yd-button type="hollow" shape="circle" class="left-btn" @click.native="afterSales">申请售后</yd-button>
        <yd-button type="hollow" shape="circle" class="right-btn" @click.native="logistics">查看物流</yd-button>
        <yd-button type="hollow" shape="circle" class="right-btn" @click.native="confirm">确认收货</yd-button>
    </div>
    <div class="orderdetailfooter" v-else-if="status === 4">
        <yd-button type="hollow" shape="circle" class="left-btn" @click.native="afterSales">申请售后</yd-button>
        <yd-button type="hollow" shape="circle" class="right-btn" @click.native="evaluate">立即评价</yd-button>
    </div>
    <div class="orderdetailfooter" v-else-if="status === 6 || status === 7">
    </div>
    <div class="orderdetailfooter" v-else>
        <yd-button type="hollow" shape="circle" class="left-btn" @click.native="afterSales">申请售后</yd-button>
    </div>
</template>

<script>
export default {
    props: {
        // 状态
        status: {
            type: Number,
            default () {
                return 1
            }
        }
    },
    methods: {
        // 取消订单
        cancel () {
            this.$emit('cancel')
        },
        // 确认收货
        confirm () {
            this.$emit('confirm')
        },
        // 申请售后
        afterSales () {
            this.$emit('afterSales')
        },
        pay () {
            this.$emit('pay')
        },
        // 评价
        evaluate () {
            this.$emit('evaluate')
        },
        // 查看物流信息
        logistics () {
            this.$emit('logistics')
        }

    }
}
</script>

<style>
</style>
