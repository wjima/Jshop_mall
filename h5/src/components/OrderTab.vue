<template>
    <div class="ordertab">
        <yd-tab v-model="intTab" :prevent-default="false" :item-click="itemClick">
            <yd-tab-panel v-for="(tab, index) in items" :key="index" :label="tab.label">
                <yd-infinitescroll :callback="loadMore" :scroll-top="false" ref="infinitescrollDemo">

                    <div class="order-content" v-for="(item, key) in tab.list" :key="key" slot="list">
                        <div class="order-content-header">
                            <p class="header-left">订单号：{{ item.order_id }}</p>
                            <p class="header-right" v-if="item.status === 1 && item.pay_status === 1">待付款</p>
                            <p class="header-right" v-if="item.status === 1 && item.pay_status === 2 && item.ship_status === 1">待发货</p>
                            <p class="header-right" v-if="item.status === 1 && item.pay_status === 4">售后单</p>
                            <p class="header-right" v-if="item.status === 1 && item.pay_status === 2 && item.ship_status === 3 && item.confirm === 1">待收货</p>
                            <p class="header-right" style="color: #e6a200" v-if="item.status === 1 && item.pay_status === 2 && item.ship_status === 3 && item.confirm === 2 && item.is_comment === 1">待评价</p>
                            <p class="header-right" style="color: #0575f2" v-if="item.status === 1 && item.pay_status === 2 && item.ship_status === 3 && item.confirm === 2 && item.is_comment === 2">已评价</p>
                            <p class="header-right" style="color: #379B2D;" v-if="item.status === 2">已完成</p>
                            <p class="header-right" style="color: #ccc;" v-if="item.status === 3">已取消</p>
                        </div>
                        <yd-list theme="4" @click.native="showDetail(item.order_id)">
                            <yd-list-item v-for="(goods, key) in item.items" :key="key">
                                <img slot="img" v-lazy="goods.image_url">
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
                                <p class="footer-top-left">共计 {{ item.items.length }} 件商品</p>
                                <p class="footer-top-right">合计：¥{{ item.order_amount }}（ 含运费 ¥{{ item.cost_freight }} )</p>
                            </div>
                            <div class="footer-bottom" v-if="item.status === 1 && item.pay_status === 1">
                                <yd-button type="hollow" shape="circle" class="left-btn" @click.native="showDetail(item.order_id)">查看</yd-button>
                                <yd-button type="hollow" shape="circle" class="right-btn" @click.native="pay(item.order_id)">立即付款</yd-button>
                            </div>
                            <div class="footer-bottom" v-else-if="item.status === 1 && item.pay_status === 2 && item.ship_status === 3 && item.confirm === 1">
                                <yd-button type="hollow" shape="circle" class="left-btn" @click.native="showDetail(item.order_id)">查看</yd-button>
                                <!--<yd-button type="hollow" shape="circle" class="left-btn" @click.native="logistics(item.order_id)">物流信息</yd-button>-->
                                <yd-button type="hollow" shape="circle" class="right-btn" @click.native="confirm(item.order_id)">确认收货</yd-button>
                            </div>
                            <div class="footer-bottom" v-else-if="item.status === 1 && item.pay_status === 2 && item.ship_status === 3 && item.confirm === 2 && item.is_comment === 1">
                                <yd-button type="hollow" shape="circle" class="left-btn" @click.native="showDetail(item.order_id)">查看</yd-button>
                                <yd-button type="hollow" shape="circle" class="right-btn" @click.native="evaluate(item.order_id)">立即评价</yd-button>
                            </div>
                            <div class="footer-bottom" v-else>
                                <yd-button type="hollow" shape="circle" class="left-btn" @click.native="showDetail(item.order_id)">查看</yd-button>
                            </div>
                        </div>
                    </div>
                </yd-infinitescroll>
            </yd-tab-panel>
            <yd-backtop></yd-backtop>
        </yd-tab>
        <yd-popup v-model="showLogistics" width="80%" height="80%">
            <div class="express-info">
                <div class="express-num">{{ logisticsInfo.company }}：{{ logisticsInfo.no }}</div>
                <yd-timeline>
                    <yd-timeline-item v-for="(item, index) in logisticsInfo.list" :key="index">
                        <p>{{ item.context }}</p>
                        <p style="margin-top: 10px;">{{ item.time }}</p>
                    </yd-timeline-item>
                </yd-timeline>
            </div>
        </yd-popup>
    </div>
</template>

<script>
export default {
    data () {
        return {
            intTab: parseInt(this.tab),
            page: 1,
            pageSize: 5,
            items: [
                {
                    label: '全部',
                    list: [],
                    page: 1,
                    status: 0
                },
                {
                    label: '待付款',
                    list: [],
                    page: 1,
                    status: 1
                },
                {
                    label: '待发货',
                    list: [],
                    page: 1,
                    status: 2
                },
                {
                    label: '待收货',
                    list: [],
                    page: 1,
                    status: 3
                },
                {
                    label: '待评价',
                    list: [],
                    page: 1,
                    status: 4
                }
            ],
            showLogistics: false, // 是否显示物流窗口
            logisticsInfo: [] // 物流信息
        }
    },
    props: {
        tab: {
            type: [Number, String],
            default () {
                return ''
            }
        }
    },
    created () {
        this.orderList(this.items[this.intTab].page, this.items[this.intTab].status)
    },
    methods: {
        // tab切换请求数据
        itemClick (key) {
            this.intTab = key
            this.items[key].list = this.orderList(this.items[key].page, this.items[key].status)
        },
        // 订单列表
        orderList (page, status) {
            this.$api.orderList({
                page: page,
                limit: this.pageSize,
                status: status
            }, res => {
                const _list = res.data.list
                this.items[this.intTab].list = [..._list]
                if (_list.length < this.pageSize) {
                    this.$refs.infinitescrollDemo[this.intTab].$emit('ydui.infinitescroll.loadedDone')
                }
            })
        },
        // 加载更多
        loadMore () {
            this.$api.orderList({
                page: ++this.items[this.intTab].page,
                limit: this.pageSize,
                status: this.items[this.intTab].status
            }, res => {
                const _list = res.data.list
                this.items[this.intTab].list = [...this.items[this.intTab].list, ..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo[this.intTab].$emit('ydui.infinitescroll.loadedDone')
                }

                /* 单次请求数据完毕 */
                this.$refs.infinitescrollDemo[this.intTab].$emit('ydui.infinitescroll.finishLoad')
            })
        },
        // 查看订单详情
        showDetail (id) {
            this.$router.push({path: '/orderdetail', query: {order_id: id}})
        },
        // 立即付款
        pay (id) {
            this.$router.push({path: 'cashierdesk', query: {order_id: id}})
        },
        // 确认收货
        confirm (id) {
            this.$dialog.confirm({
                mes: '确认执行此操作吗?',
                opts: [
                    {
                        txt: '确定',
                        color: true,
                        callback: () => {
                            this.$api.confirmOrder({
                                order_id: id
                            }, res => {
                                if (res.status) {
                                    this.$dialog.toast({
                                        mes: res.msg,
                                        icon: 'success',
                                        timeout: 1000
                                    })
                                }
                            })
                        }
                    },
                    {
                        txt: '取消',
                        color: false
                    }
                ]
            })
        },
        // 评价
        evaluate (id) {
            this.$router.push({path: '/evaluate', query: {order_id: id}})
        },
        // 查看物流信息
        logistics (id) {
            this.$api.logistics({order_id: id}, res => {
                if (res.status) {
                    this.showLogistics = true
                    this.logisticsInfo = res.data
                }
            })
        }
    },
    watch: {
        intTab () {
            this.$router.replace({path: '/allorder', query: {tab: this.intTab}})
        }
    }
}
</script>

<style>
    .express-info{
        background-color: #fff;
        position: relative;
    }
    .express-num{
        position: fixed;
        top: -1px;
        background-color: #e5e5e5;
        width: 100%;
        height: .8rem;
        line-height: .8rem;
        z-index: 10086;
    }
    .express-info .yd-timeline{
        margin-top: calc(.5rem - 1px);
        font-size: .1rem;
    }
</style>
