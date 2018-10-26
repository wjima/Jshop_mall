<template>
    <div class="indexcoupon"  v-if="couponList.length">
        <div class="indexcoupon-item" v-for="(item, index) in couponList.slice(0, 3)" :key="index" @click="userGetCoupon(index)">
            {{ item.name }}
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

<style>
    .indexcoupon{
        width: 100%;
        margin-bottom: .2rem;
        background-color: #fff;
        padding: .1rem .3rem;
        overflow: hidden;
    }
    .indexcoupon-item{
        display: inline-block;
        width: 32%;
        height: 1.5rem;
        line-height: 1.5rem;
        float: left;
        box-sizing: border-box;
    }
    .indexcoupon-item:nth-child(1){
        color: rgb(249, 164, 203);
        background-color: rgb(255, 245, 244);
        border-left: 1px dashed rgb(249, 164, 203);
        margin-right: 2%;
    }
    .indexcoupon-item:nth-child(2){
        color: rgb(253, 179, 58);
        background-color: rgb(253, 244, 237);
        border-left: 1px dashed rgb(253, 179, 58);
        margin-right: 2%;
    }
    .indexcoupon-item:nth-child(3){
        color: rgb(244, 144, 155);
        background-color: rgb(245, 241, 241);
        border-left: 1px dashed rgb(244, 144, 155);
    }
</style>
