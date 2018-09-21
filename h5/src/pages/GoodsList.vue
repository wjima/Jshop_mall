<template>
    <div class="goodslist">
        <yd-infinitescroll :callback="loadMore" :distance="2" :scroll-top="false" ref="infinitescrollDemo">
            <yd-list theme="4" slot="list">
                <yd-list-item v-for='(item, key) in list' :key='key' type="link" :href="{path:'goodsdetail',query:{goods_id:item.id}}">
                    <img slot='img' :src='item.image_url'>
                    <span slot='title'>{{item.name}}</span>
                    <yd-list-other slot='other'>
                        <div>
                            <span class='demo-list-price'><em>¥</em>{{item.price}}</span>
                        </div>
                    </yd-list-other>
                </yd-list-item>
            </yd-list>

            <!-- 数据全部加载完毕显示 -->
            <span slot="doneTip" class="data-none">全都被你看完了</span>

            <!-- 加载中提示，不指定，将显示默认加载中图标 -->
            <img slot="loadingTip" src="http://static.ydcss.com/uploads/ydui/loading/loading10.svg"/>
        </yd-infinitescroll>
        <yd-backtop></yd-backtop>
    </div>
</template>

<script>
export default {
    data () {
        return {
            page: 1,
            pageSize: 10, // 每页10条数据
            catId: this.$route.query.cat_id || 0, // 分类条件
            hot: this.$route.query.hot || 0, // 热门
            keywords: this.$route.query.keywords || 0, // 搜索条件
            list: [] // 指定分类的商品数据
        }
    },
    mounted () {
        this.goodsList()
    },
    methods: {
        // 获取商品数据
        goodsList () {
            this.$api.goodsList({
                page: this.page,
                limit: this.pageSize,
                where: this.conditions()
            }, res => {
                const _list = res.data.list
                this.list = [..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
            })
        },
        // 加载更多
        loadMore () {
            this.$api.goodsList({
                page: ++this.page,
                limit: this.pageSize,
                where: this.conditions()
            }, res => {
                const _list = res.data.list
                this.list = [...this.list, ..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
                /* 单次请求数据完毕 */
                this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad')
            })
        },
        // 筛选条件
        conditions () {
            let where = {}
            if (this.catId) {
                where = {cat_id: this.catId}
            } else if (this.hot) {
                where = {hot: this.hot}
            } else if (this.keywords) {
                where = {search_name: this.keywords}
            }
            return JSON.stringify(where)
        }
    }
}
</script>

<style>
</style>
