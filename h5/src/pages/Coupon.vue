<template>
    <div class="coupon" v-if="couponList.length">
        <div class="coupon-content" v-for="(item, index) in couponList" :key="index">
            <div class="coupon-red">
                <div class="coupon-content-left">{{ item.name }}</div>
                <div class="coupon-content-right">
                    <div class="">
                        <p>{{ item.stime }}</p>
                        <p>到</p>
                        <p>{{ item.etime }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="data-none" v-else>
        竟然什么都没有，快去领券吧
    </div>
</template>

<script>
export default {
    data () {
        return {
            couponList: [] // 用户已领取的优惠券
        }
    },
    created () {
        this.userCoupon()
    },
    methods: {
        userCoupon () {
            this.$api.userCoupon({}, res => {
                if (res.status) {
                    this.couponList = res.data
                }
            })
        }
    }
}
</script>

<style>
</style>
