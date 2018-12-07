<template>
    <div class="goodstitle">
        <div class="goodsheader">
            <div class="goodsheader-left">
                <h3 class="goodsname">{{ product.name }}</h3>
                <p>{{ brief }}</p>
            </div>
            <yd-button class="share" @click.native="show1 = true">
                <img src="../../../static/image/share.png"/>
                <p>分享</p>
            </yd-button>
        </div>
        <div class="group-buying">
            <div class="price">
                <h2>￥{{ this.GLOBAL.formatMoney(product.price, 2, '') }}</h2>
                <p>￥{{ product.mktprice }}</p>
            </div>
            <div class="salesvolume">
                <p>已售{{ buyCount }} {{ unit }}/剩余 {{ stock }} {{ unit }}</p>
                <p>累计销售{{ buyCount }} {{ unit }}</p>
            </div>

            <div class="commodity-time">
                <p>距结束仅剩</p>
                <count-down :endTime="etime" endText="已经结束了"></count-down>
            </div>
            <img class="sanjiao" src="../../../static/image/sanjiao.png" alt="">
        </div>
        <yd-popup v-model="show1" position="center" width="90%">
            <div style="background-color:#fff;padding: 20px;">
                <share :config="config"></share>
            </div>
        </yd-popup>
    </div>
</template>

<script>
import countDown from '../time'
export default {
    props: {
        config: 'config',
        product: {
            type: [Object, Array],
            default () {
                return {}
            }
        },
        brief: {
            type: String
        },
        buyCount: {
            type: Number
        },
        unit: {
            type: String
        },
        stock: {
            type: Number,
            default () {
                return 0
            }
        },
        etime: {
            type: Number,
            default () {
                return 0
            }
        }
    },
    components: { countDown },
    data () {
        return {
            show1: false,
            url: '', // 网址，默认使用 window.location.href
            source: '', // 来源（QQ空间会用到）, 默认读取head标签：<meta name="site" content="http://overtrue" />
            title: '', // 标题，默认读取 document.title 或者 <meta name="title" content="share.js" />
            description: '', // 描述, 默认读取head标签：<meta name="description" content="PHP弱类型的实现原理分析" />
            image: '', // 图片, 默认取网页中第一个img标签
            sites: ['qzone', 'qq', 'weibo', 'wechat', 'douban'], // 启用的站点
            disabled: ['google', 'facebook', 'twitter'], // 禁用的站点
            wechatQrcodeTitle: '微信扫一扫：分享', // 微信二维码提示文字
            wechatQrcodeHelper: '<p>微信里点“发现”，扫一下</p><p>二维码便可将本文分享至朋友圈。</p>'
        }
    }
}
</script>

<style>
    .goodsheader .yd-btn-primary:active{
        background-color: #fff;
    }
    a.icon-google, a.icon-twitter, a.icon-facebook, a.icon-diandian{
        display: none !important;
    }
</style>
