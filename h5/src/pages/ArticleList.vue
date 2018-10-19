<template>
    <div class="articlelist">
        <yd-infinitescroll :callback="loadMore" ref="infinitescrollDemo">
            <div slot="list" class="articlelist-item" v-for="(item, index) in articleList" :key="index" @click="showDetail(item.id)">
                <img :src="item.cover" alt="">
                <div class="articlelist-item-body">
                    <h3 class="articlelist-title">{{ item.title }}</h3>
                    <p class="articlelist-time">{{ item.ctime }}</p>
                </div>
            </div>
        </yd-infinitescroll>
    </div>
</template>

<script>
export default {
    data () {
        return {
            page: 1,
            pageSize: 10,
            typeId: this.$route.query.type_id || '',
            articleList: []
        }
    },
    mounted () {
        if (this.typeId) {
            this.getArticleList()
        } else {
            this.$dialog.alert({
                mes: '该文章分类不存在',
                callback: () => {
                    this.$router.go(-1)
                }
            })
        }
    },
    methods: {
        getArticleList () {
            this.$api.articleList({
                page: this.page,
                limit: this.pageSize,
                type_id: this.typeId
            }, res => {
                const _list = this.dateFormat(res.data.list)
                this.articleList = [..._list]
                if (_list.length < this.pageSize) {
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
            })
        },
        loadMore () {
            this.$api.articleList({
                page: ++this.page,
                limit: this.pageSize,
                type_id: this.typeId
            }, res => {
                const _list = this.dateFormat(res.data.list)
                this.articleList = [...this.articleList, ..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                    return
                }
                /* 单次请求数据完毕 */
                this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad')
            })
        },
        // 时间格式化
        dateFormat (list) {
            list.forEach((item) => {
                item.ctime = this.GLOBAL.timeToDate(item.ctime)
            })
            return list
        },
        showDetail (id) {
            this.$router.push({path: '/article', query: {article_id: id}})
        }
    }
}
</script>

<style>
    .articlelist{
        text-align: left;
    }
    .articlelist-item{
        padding: .2rem;
        background-color: #fff;
        margin-bottom: .1rem;
        overflow: hidden;
        height: 1.5rem;
    }
    .articlelist-item img{
        width: 20%;
        height: 100%;
        float: left;
        display: inline-block;
    }
    .articlelist-item-body{
        width: 77%;
        display: inline-block;
        margin-left: 3%;
        box-sizing: border-box;
        position: relative;
        height: 100%;
    }
    .articlelist-title{
        font-size: .26rem;
        width: 100%;
        overflow : hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp:2;
        -webkit-box-orient: vertical;
    }
    .articlelist-time{
        color: #999;
        position: absolute;
        bottom: 0;
    }
    .articlelist-bottom{
        text-align: center;
    }
</style>
