<template>
    <div class="cashlist">
        <yd-infinitescroll :callback="loadMore" ref="infinitescrollDemo">
            <div class="cashlist-item" slot="list" v-for="(item, index) in cashList" :key="index">
                <div class="cashlist-content">
                    <!--<p class="cashbank">开户行：{{ item.account_bank }}</p>
                    <p class="cashuser">用户名：{{ item.account_name }}</p>-->
                    <div class="cashlist-top">
                        <p class="cashstate">状态：{{ item.type }}</p>
                        <p class="cashtime">时间：{{ item.ctime }}</p>
                    </div>
                    <div class="cashlist-bottom">
                        <p class="cashnum">卡号：{{ item.card_number }}</p>
                        <p class="cashsum">提现金额：{{ item.money }}</p>
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
            cashList: [] // 提现记录列表
        }
    },
    mounted () {
        this.getCashList()
    },
    methods: {
        // 获取提现记录
        getCashList () {
            this.$api.cashList({
                page: this.page,
                limit: this.pageSize
            }, res => {
                const _list = res.data
                this.cashList = [..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
            })
        },
        // 加载更多
        loadMore () {
            this.$api.cashList({
                page: ++this.page,
                limit: this.pageSize
            }, res => {
                const _list = res.data
                this.cashList = [...this.cashList, ..._list]
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
    .cashlist-item{
        width: 100%;
        text-align: left;
        background-color: #fff;
        margin-bottom: .2rem;
        padding: .35rem .2rem;
        font-size: .25rem;
    }
    .cashlist-content{
        overflow: hidden;
    }
    .cashlist-content p{
        display: inline-block;
    }
    .cashlist-top{
        margin-bottom: .25rem;
    }
    .cashtime, .cashsum{
        float: right;
    }
</style>
