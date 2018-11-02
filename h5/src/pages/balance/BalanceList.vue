<template>
    <div class="balancelist">
        <yd-infinitescroll :callback="loadMore" ref="infinitescrollDemo">
            <div slot="list" class="balancelist-item" v-for="(item, index) in list" :key="index">
                <div class="balancelist-content">
                    <div class="balancelist-top">
                        <span>{{ item.type }}：</span>
                        <span style="color: #999;">[ {{ item.memo }} ]</span>
                        <p>{{ item.ctime }}</p>
                    </div>
                    <div class="balancelist-bottom">
                        <p>余额: {{ item.balance }}</p>
                        <h3 class="balancelist-right">{{ item.money }}</h3>
                    </div>
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
            list: [] // 余额明细
        }
    },
    mounted () {
        this.balanceList()
    },
    methods: {
        // 获取余额明细
        balanceList () {
            this.$api.getBalanceList({
                page: this.page,
                limit: this.pageSize
            }, res => {
                const _list = res.data
                console.log(_list)
                this.list = [..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
            })
        },
        // 加载更多
        loadMore () {
            this.$api.getBalanceList({
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
    .balancelist-item{
        width: 100%;
        text-align: left;
        background-color: #fff;
        margin-bottom: .2rem;
        padding: .35rem .2rem;
        font-size: .25rem;
    }
    .balancelist-top{
        overflow: hidden;
        margin-bottom: .2rem;
    }
    .balancelist-top p{
       display: inline-block;
       float: right;
       color: #999;
    }
    .balancelist-bottom{
        overflow: hidden;
    }
    .balancelist-bottom p{
        display: inline-block;
        color: #999;
    }
     .balancelist-bottom h3{
        display: inline-block;
        float: right;
        font-size: .4rem;
        color: #FF3B44;
     }
</style>
