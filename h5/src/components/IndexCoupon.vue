<template>
    <div class="indexcoupon"  v-if="couponList.length">
        <div class="indexcoupon-item" v-for="(item, index) in couponList.slice(0, 3)" :key="index" @click="userGetCoupon(index)">
            <div class="coupon-left">
                <p>{{ item.name }}</p>
            </div>
            <div class="coupon-right">
                <p class="conpon-f">{{ item.expression2 }}</p>
                <p>{{ item.expression1 }}</p>
                <p>{{ item.stime }} - {{ item.etime }}</p>
            </div>
            <div class="coupon-btn">
                立即领取
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        couponList: Array,
        default () {
            return []
        }
    },
    methods: {
        userGetCoupon (index) {
            this.$api.getCoupon({promotion_id: this.couponList[index].id}, res => {
                if (res.status) {
                    this.$dialog.toast({mes: res.msg, timeout: 1300})
                }
            })
        }
    }
}
</script>
