<template>
    <div class="coupon" v-if="couponList.length">
        <div class="indexcoupon-item" v-for="(item, index) in couponList" :key="index" @click="userGetCoupon(index)">
            <div class="coupon-left">
                <p>{{ item.name }}</p>
            </div>
            <div class="coupon-right">
                <p class="conpon-f">{{ item.expression2 }}</p>
                <p>{{ item.expression1 }}</p>
                <p>{{ item.stime }} - {{ item.etime }}</p>
            </div>
            <div class="coupon-btn" @click="goIndex">
                去使用
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
                    this.couponList = res.data.list
                }
            })
        },
        goIndex () {
            this.$router.push({
                path: '/index'
            })
        }
    }
}
</script>

<style>
    
</style>
