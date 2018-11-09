<template>
	<div class="index">
        <search></search>   <!-- 搜索框 -->
        <indexslider></indexslider> <!-- 轮播图 -->
        <rollnotice></rollnotice>   <!-- 公告 -->
        <indexcoupon :couponList="couponList"></indexcoupon>   <!-- 优惠券 -->
        <indexgrids></indexgrids>   <!-- 菜单menu -->
        <indexlistrec></indexlistrec>   <!-- 推荐商品列表 -->
        <indexlisthot></indexlisthot>   <!-- 热卖商品列表 -->
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
            couponList: []
        }
    },
    components: {
        search, indexslider, rollnotice, indexcoupon, indexgrids, indexlisthot, indexlistrec
    },
    // 实例创建并挂载后调用此方法
    mounted () {
        this.getCouponList()
        var url = GetUrlPara();
        console.log(url)
    },
    methods: {
        // 获取商户优惠券信息
        getCouponList () {
            this.$api.couponList({}, res => {
                if (res.status) {
                    this.couponList = res.data
                }
            })
        }
    }
}

function GetUrlPara()
{
    var url = document.location.toString();
    var arrUrl = url.split("?");

    var para = arrUrl[1];
    return para;
}

</script>

<style>
</style>
