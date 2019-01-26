<template>
    <div class="coupon" v-if="couponList.length">
        <div class="indexcoupon-item" :class="item.is_used === 2 || item.is_expire === 2 ? 'used' : ''" v-for="(item, index) in couponList" :key="index">
            <div class="coupon-left">
                <p>{{ item.name }}</p>
            </div>
            <div class="coupon-right">
                <p class="conpon-f">{{ item.expression2 }}</p>
                <p>{{ item.expression1 }}</p>
                <p>{{ item.stime }} - {{ item.etime }}</p>
            </div>
            <div class="coupon-btn" @click="goIndex" v-if="item.is_used === 1 && item.is_expire === 1">
                去使用
            </div>
            <div class="right-pic" v-else-if="item.is_used === 2">
                <img src="../../static/image/used.png" alt="">
            </div>
            <div class="right-pic" v-else-if="item.is_expire === 2">
                <img src="../../static/image/expired.png" alt="">
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
            this.$api.userCoupon({display: 'all'}, res => {
                if (res.status) {
                    this.couponList = res.data.list
                    this.couponList.sort((a, b) => {
                        return a.is_expire - b.is_expire
                    })
                    this.couponList.sort((a, b) => {
                        return a.is_used - b.is_used
                    })
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
    .right-pic{
        position: absolute;
        top: 50%;
        right: .3rem;
        transform: translateY(-50%);
    }
    .used {
        background-image: linear-gradient(to right, rgb(173, 173, 173), rgb(193, 193, 193));
    }
</style>
