<template>
    <div class="indexlisthot" v-if="groupBuy.length">
        <div class="indexlistheader">
            <img src="../../static/image/hot.png"/>
            <h3>精品团购</h3>
        </div>
        <yd-list theme="2" slot="list">
            <yd-list-item v-for="(item, key) in groupBuy" :key="key" type="link" :href="{path:'groupbuying',query:{goods_id:item.id}}">
                <img class="goods-imgs" slot="img" v-lazy="item.image_url">
                <span slot="title">{{item.name}}</span>
                <div slot="other" class="count-downs">
                    <span>剩:</span><count-down :endTime="item.etime" endText="已经结束了"></count-down>
                </div>
                <yd-list-other slot="other">

                    <div>
                        <span class='demo-list-price'><em>¥</em>{{item.price}}</span>
                        <!--<span class='demo-list-del-price'>¥{{item.w_price}}</span>-->
                    </div>
                    <div>
                        <yd-button type="danger">立即团购</yd-button>
                    </div>
                </yd-list-other>
            </yd-list-item>
        </yd-list>
    </div>
</template>

<script type="text/babel">
import countDown from '../components/time'
export default {
    data () {
        return {
            groupBuy: []
        }
    },
    components: { countDown },
    mounted () {
        // 获取团购列表
        this.$api.activityList({type: 3}, res => {
            this.groupBuy = res.data
        })
    }
}
</script>
