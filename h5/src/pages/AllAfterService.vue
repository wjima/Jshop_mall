<template>
    <div class="ordertab">
        <yd-infinitescroll :callback="loadMore" ref="infinitescrollDemo">
            <div class="order-content" v-for="(item, index) in list" :key="index" slot="list">
                <div class="order-content-header">
                    <p class="header-left">售后单号：{{ item.aftersales_id }}</p>
                    <p class="header-right" v-if="item.status === 1">待审核</p>
                    <p class="header-right" v-else-if="item.status === 2">审核通过</p>
                    <p class="header-right" v-else-if="item.status === 3">审核拒绝</p>
                </div>
                <yd-list theme="4" @click.native="afterDetail(item.aftersales_id)">
                    <yd-list-item v-for="(goods, key) in item.order.items" :key="key">
                        <img slot="img" :src="goods.image_url">
                        <h3 class="goodsname" slot="title">{{ goods.name }}</h3>
                        <p class="goods" slot="title">{{ goods.addon }}</p>
                        <yd-list-other slot="other">
                            <div>
                                <span class="demo-list-price"><em>¥</em>{{ goods.price }}</span>
                            </div>
                            <div>x{{ goods.nums }}</div>
                        </yd-list-other>
                    </yd-list-item>
                </yd-list>
                <div class="order-content-footer">
                    <div class="footer-top">
                        <p class="footer-top-left">共计 {{ item.order.items.length }} 件商品</p>
                        <p class="footer-top-right">合计：¥{{ item.refund }}</p>
                    </div>
                    <div class="footer-bottom">
                        <yd-button type="hollow" shape="circle" class="right-btn" @click.native="afterDetail(item.aftersales_id)">查看</yd-button>
                    </div>
                </div>
            </div>
        </yd-infinitescroll>
        <yd-backtop></yd-backtop>
    </div>
</template>

<script>
export default {
    data () {
        return {
            page: 1,
            pageSize: 5,
            list: [] // 售后单列表
        }
    },
    created () {
        this.$api.afterSalesList({
            page: this.page,
            limit: this.pageSize
        }, res => {
            const _list = res.data.list
            this.list = [..._list]
            if (_list.length < this.pageSize) {
                /* 所有数据加载完毕 */
                this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
            }
        })
    },
    methods: {
        afterDetail (id) {
            this.$router.push({path: '/aftersalesdetail', query: {aftersales_id: id}})
        },
        loadMore () {
            this.$api.afterSalesList({
                page: ++this.page,
                limit: this.pageSize
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
        }
    }
}
</script>

<style>
    .ordertab .yd-tab-panel{
        background-color: #f5f5f5;
    }
    .ordertab .yd-tab-nav-item:not(:last-child):after{
        border: none;
    }

    .order-content{
        margin-bottom: .2rem;
        background-color: #fff;
    }
    .order-content-header{
        padding: .2rem 10px .1rem;
        overflow: hidden;
    }
    .order-content-header .header-left{
        display: inline-block;
        float: left;
        color: #888888;
    }
    .order-content-header .header-right{
        display: inline-block;
        float: right;
        color: #FF3B44;
    }
    .order-content .orderlist .yd-list-item{
        margin-top: 2px;
    }
    .order-content-footer .footer-top{
        text-align: right;
        padding: .15rem 10px;
        border-bottom: 1px solid #e9e9e9;
    }
    .order-content-footer .footer-top>p{
        display: inline-block;
        color: #888;
    }
    .order-content-footer .footer-bottom{
        text-align: right;
        padding: .15rem 10px;
    }
    .order-content-footer .footer-bottom .yd-btn{
        padding: 0 .4rem;
    }
    .order-content-footer .footer-bottom .left-btn{
        border-color: #e9e9e9;
        color: #888;
    }
    .order-content-footer .footer-bottom .right-btn{
        border-color: #FF3B44;
        color: #FF3B44;
    }
</style>
