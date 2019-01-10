<template>
    <div class="indexlisthot" v-if="secKill.length">
        <div class="indexlistheader">
            <img src="../../static/image/hot.png"/>
            <h3>限时秒杀</h3>
        </div>
        <yd-list theme="2" slot="list">
            <yd-list-item v-for="(item, key) in secKill" :key="key" type="link" :href="{path:'seckilldetail',query:{goods_id:item.id}}">
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
                        <yd-button type="danger">立即秒杀</yd-button>
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
            secKill: [] // 秒杀列表
        }
    },
    components: { countDown },
    mounted () {
        this.$api.activityList({type: 4}, res => {
            if (res.status) {
                this.secKill = res.data
            }
        })
    },
    methods: {

    }
}
</script>
<style>
    .indexlisthot .count-downs{
        text-align: left;
    }
    .indexlisthot .count-down{

        margin-bottom: .15rem;
        margin-top: .1rem;
        display: inline-block;
    }
    .indexlisthot .count-down i{
        background-color: rgb(255, 194, 203);
        color: #FF3A59;
        padding: 0.05rem .08rem;
        border-radius: 3px;
    }
</style>
