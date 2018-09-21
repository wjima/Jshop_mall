<template>
	<div class="index">
        <search></search>
        <indexslider></indexslider>
        <rollnotice></rollnotice>
        <indexcoupon :couponList="couponList" v-if="couponList.length"></indexcoupon>
        <indexgrids></indexgrids>
        <indexlistrec></indexlistrec>
        <indexlisthot></indexlisthot>
        <yd-backtop></yd-backtop>
	</div>
</template>

<script>
import search from '../components/Search.vue'
import indexslider from '../components/IndexSlider.vue'
import rollnotice from '../components/RollNotice.vue'
import indexcoupon from '../components/IndexCoupon.vue'
import indexgrids from '../components/IndexGrids.vue'
import indexlisthot from '../components/IndexListHot.vue'
import indexlistrec from '../components/IndexListRec.vue'

export default {
    data () {
        return {
            sellerName: '',
            couponList: []
        }
    },
    components: {
        search, indexslider, rollnotice, indexcoupon, indexgrids, indexlisthot, indexlistrec
    },
    // 实例创建并挂载后调用此方法
    mounted () {
        this.getSellerName()
        this.getCouponList()
    },
    methods: {
        // 获取商户名称
        getSellerName () {
            this.$api.getStoreName({}, res => {
                this.sellerName = res.data
                document.title = res.data
            })
        },
        // 获取商户优惠券信息
        getCouponList () {
            this.$api.couponList({}, res => {
                if (res.status) {
                    this.couponList = res.data
                }
            })
        }
    },
    activated () {
        if (this.sellerName) {
            document.title = this.sellerName
        } else {
            this.getSellerName()
        }
    }
}
</script>

<style>
</style>
