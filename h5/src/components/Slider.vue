<template v-if="imgList.length">
    <div class="slider" ref="slider">
        <yd-slider ref="slidercontent" class="slidercontent" :autoplay="autoPlay">
            <yd-slider-item v-for="(item, key) in imgList" :key="key">
                <img :src="item">
                <!--<img src="../../static/image/loading.gif" class="loading-img"/>-->
            </yd-slider-item>
        </yd-slider>
        <div class="slider-back">
        </div>
    </div>
</template>

<script type="text/babel">
export default {
    props: {
        imgList: {
            type: [Array, Object],
            default () {
                return []
            }
        }
    },
    data () {
        return {
            autoPlay: 3000
        }
    },
    mounted () {
        let w = this.$refs.slider.offsetWidth
        this.$refs.slider.style.height = w + 'px'
    }
}
</script>

<style type="text/css">
    .slidercontent{
        height: 100%;
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
