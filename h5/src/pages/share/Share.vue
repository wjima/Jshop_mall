<template>
    <div class="share-index">
        <qriously :value="QCode" :size="size" />
        <p>长按二维码分享</p>
        <p href="">{{ QCode }}</p>
    </div>
</template>

<script>
export default {
    data () {
        return {
            QCode: '', // 二维码value
            size: 240 // 二维码大小
        }
    },
    mounted () {
        this.getShareCode()
    },
    methods: {
        // 获取邀请码
        getShareCode () {
            this.$api.shareCode({}, res => {
                let code = res.data
                this.QCodeUrl(code)
            })
        },
        // 拼接推荐url 路由
        // QCodeUrl (code) {
        //     let protocol = window.location.protocol
        //     let host = window.location.host
        //     this.QCode = protocol + '//' + host + '/#/register?pid=' + code
        // },
        QCodeUrl (code) {
            this.QCode = this.GLOBAL.locationHost() + '/#register?invitecode=' + code
        }
    }
}
</script>

<style>
    .share-index{
        background: #fff;
        height: 100%;
        padding-top: 1rem;
    }
    .share-index img{
        width: 5rem;
        height: 5rem;
        margin-bottom: .3rem;
    }
    .share-index p{
        margin-bottom: .5rem;
        font-size: .4rem;
    }
    .share-index a{
        display: block;
        font-size: .3rem;
    }
</style>
