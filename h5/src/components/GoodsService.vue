<template>
    <div class="goodsservice">
        <div class="service-content">
            <div class="service-content-header">
                <p>服务</p>
            </div>
            <div>
                <img src="../../static/image/red-right.png"/>
                <p>官方商城</p>
            </div>
            <div>
                <img src="../../static/image/red-right.png"/>
                <p>店铺认证</p>
            </div>
            <div>
                <img src="../../static/image/red-right.png"/>
                <p>担保交易</p>
            </div>
        </div>
        <div class="service-content" v-if="promotion.length">
            <div class="service-content-header">
                <p>促销</p>
            </div>
            <yd-badge shape="square" type="danger" v-for="(item, index) in promotion" :key="index">{{ item.name }}</yd-badge>
        </div>
    </div>
</template>

<script type="text/babel">
export default {
    data () {
        return {
            checkbox: ['1']
        }
    },
    props: {
        promotion: {
            type: [Array, Object],
            default () {
                return []
            }
        }
    }
}
</script>

<style type="text/css">
</style>
