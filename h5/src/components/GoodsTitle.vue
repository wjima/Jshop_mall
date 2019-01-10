<template>
    <div class="goodstitle">
        <div class="goodsheader">
            <div class="goodsheader-left">
                <h3 class="goodsname">{{ product.name }}</h3>
                <p>{{ brief }}</p>
            </div>
            <yd-button class="share" @click.native="share()">
                <img src="../../static/image/share.png"/>
                <p>分享</p>
            </yd-button>
        </div>
        <div class="price-salesvolume">
            <div class="price">
                <h2>￥{{ this.GLOBAL.formatMoney(product.price, 2, '') }}</h2>
                <p>￥{{ product.mktprice }}</p>
            </div>
            <div class="salesvolume">
                <p>销量：{{ buyCount }} {{ unit }}</p>
            </div>
        </div>
        <yd-popup v-model="browserShow" position="center" width="90%">
            <div style="background-color:#fff;padding: 20px;">
                <share :config="config"></share>
            </div>
        </yd-popup>
        <yd-popup class="wechatshare" v-model="weiXinBrowserShow" position="center"  width="90%">
            <img class="wechatshare-img" src="../../static/image/wechatshare.png"/>
        </yd-popup>
    </div>
</template>

<script>
import 'vue-ydui/dist/ydui.px.css'
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
        img: {
            type: String
        },
        name: {
            type: String
        }
    },
    data () {
        return {
            browserShow: false,
            url: '', // 网址，默认使用 window.location.href
            source: '', // 来源（QQ空间会用到）, 默认读取head标签：<meta name="site" content="http://overtrue" />
            title: '', // 标题，默认读取 document.title 或者 <meta name="title" content="share.js" />
            description: '', // 描述, 默认读取head标签：<meta name="description" content="PHP弱类型的实现原理分析" />
            image: '', // 图片, 默认取网页中第一个img标签
            sites: ['qzone', 'qq', 'weibo', 'wechat', 'douban'], // 启用的站点
            disabled: ['google', 'facebook', 'twitter'], // 禁用的站点
            wechatQrcodeTitle: '微信扫一扫：分享', // 微信二维码提示文字
            wechatQrcodeHelper: '<p>微信里点“发现”，扫一下</p><p>二维码便可将本文分享至朋友圈。</p>',
            weiXinBrowserShow: false // 微信分享弹出层
        }
    },
    methods: {
        share () {
            let isWeiXin = this.GLOBAL.isWeiXinBrowser()
            if (isWeiXin) {
                // 微信浏览器分享
                this.weiXinBrowserShow = true
            } else {
                // 其他浏览器分享
                this.browserShow = true
            }
        }
    }
}
</script>
