<template>
    <div class="slider" ref="slider">
        <yd-slider ref="slidercontent" class="slidercontent" :autoplay="autoPlay" >
            <yd-slider-item v-for="(item, key) in sliderList" :key="key">
                <img :src="item.img" @click="showDetail(item.type,item.val)">
                <!--<img src="../../static/image/loading.gif" class="loading-img"/>-->
            </yd-slider-item>
        </yd-slider>
        <div class="slider-back">
        </div>
    </div>
</template>

<script type="text/babel">
export default {
    data () {
        return {
            autoPlay: 4000,
            sliderList: []
        }
    },
    mounted () {
        this.getSliderList()
    },
    methods: {
        // 获取 轮播图
        getSliderList () {
            this.$api.sliderHeader({code: 'tpl1_slider'}, res => {
                this.sliderList = res.data.list
            })
        },
        showDetail (type, val) {
            if (type === 1) {
                window.location.href = val
            } else if (type === 2) {
                this.$router.push({path: '/goodsdetail', query: {goods_id: val}})
            } else if (type === 3) {
                this.$router.push({path: '/article', query: {article_id: val}})
            } else if (type === 4) {
                this.$router.push({path: '/articlelist', query: {type_id: val}})
            }
        }
    }
}
</script>

<style type="text/css">
    .slidercontent{
        height: 100%;
        min-height: 4rem;
    }
    .slider-back{
        position: absolute;
        top: .2rem;
        left: .2rem;
        z-index: 1000;
        width: .5rem;
        height: .5rem;
        text-align: center;
        line-height: .5rem;
        background-color: rgba(0,0,0,0.3);
        border-radius: 50%;
        display: none;
    }
</style>
