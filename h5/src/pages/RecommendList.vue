<template>
    <div class="recommendlist">
        <yd-infinitescroll :callback="loadMore" ref="infinitescrollDemo">
            <ul class="recommend" slot="list">
                <li class="recommenddetail" v-for="(item, index) in list" :key="index">
                    <img class="user-img" :src="item.avatar"/>
                    <div class="recommendcontent">
                        <h4>昵称：{{ item.nickname ? item.nickname : '暂无'}}</h4>
                        <p>手机：{{ item.mobile }}</p>
                        <span>推荐时间：{{ item.ctime }}</span>
                    </div>
                </li>
            </ul>
        </yd-infinitescroll>
    </div>
</template>

<script>
export default {
    data () {
        return {
            page: 1,
            pageSize: 10,
            list: [] // 用户推荐列表
        }
    },
    mounted () {
        this.recommend()
    },
    methods: {
        // 获取推荐记录列表
        recommend () {
            this.$api.recommendList({
                page: this.page,
                limit: this.pageSize
            }, res => {
                const _list = res.data
                this.list = [..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
            })
        },
        loadMore () {
            this.$api.recommendList({
                page: ++this.page,
                limit: this.pageSize
            }, res => {
                const _list = res.data
                this.list = [...this.list, ..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
                /* 单次请求数据完毕 */
                this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad')
            })
        }
    }
}
</script>

<style>
    .recommendlist{
        height: 100%;
        background: #fff;
    }
    .recommenddetail{
        text-align: left;
        padding: .3rem .2rem 0;
        width: 100%;
        box-sizing: border-box;
        position: relative;
    }
    .recommenddetail .user-img{
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
    .recommendcontent{
        display: inline-block;
        border-bottom: 1px solid #F2F2F2;
        padding-bottom: .3rem;
        overflow: hidden;
        width: calc(100% - 1.3rem);
        margin-left: 1.2rem;
    }
    .recommendcontent h4{
        font-size: .3rem;
        margin-bottom: .1rem;
    }
    .recommendcontent p{
        color: #666;
        margin-bottom: .15rem;
    }
    .recommendcontent span{
        color: #999;
    }
</style>
