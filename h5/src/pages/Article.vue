<template>
    <div class="notice">
        <h2 class="notice-title">{{ article.title }}</h2>
        <div class="notice-content">
            <p v-html="article.content"></p>
        </div>
        <div class="notice-footer">
            <span>{{ this.GLOBAL.timeToDate(article.ctime) }}</span>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            article: []
        }
    },
    created () {
        this.$api.articleInfo({article_id: this.$route.query.article_id}, res => {
            if (res.data) {
                this.article = res.data
            } else {
                this.$dialog.alert({
                    mes: '该文章信息不存在',
                    callback: () => {
                        this.$router.go(-1)
                    }
                })
            }
        })
    }
}
</script>

<style>
</style>
