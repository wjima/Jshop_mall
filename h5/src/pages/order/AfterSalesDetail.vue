<template>
    <div class="aftersalesdetail">
        <div class="orderdetailheader">
            <div class="">
                <h3>{{ typeName }}</h3>
                <p>{{ statusName }}  {{ refundName }} {{ reshipName }}</p>
            </div>
        </div>
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">售后单号</span>
                <span slot="right">{{ aftersales_id }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">售后类型</span>
                <span slot="right">{{ typeName }}</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">退款金额</span>
                <span slot="right" class="demo-list-price">￥{{ refund }}</span>
            </yd-cell-item>
        </yd-cell-group>
        <yd-cell-group title="退货商品" v-if="type === 2">
            <yd-list theme="4">
                <yd-list-item v-for="(item, index) in items" :key="index">
                    <img slot="img" :src="item.image_url">
                    <h3 class="goodsname" slot="title">{{ item.name }}</h3>
                    <p class="goods" slot="title">{{ item.addon }}</p>
                    <yd-list-other slot="other">
                        <div>x{{ item.nums }}</div>
                    </yd-list-other>
                </yd-list-item>
            </yd-list>
        </yd-cell-group>
        <yd-cell-group title="图片凭证" v-if="images">
            <div class="afterserviceimg">
                <img class="thumbnail-list" :src="item.url" alt="" v-for="(item, index) in images" :key="index">
            </div>
        </yd-cell-group>
        <yd-cell-group title="问题描述" v-if="reason">
            <yd-cell-item>
                <yd-textarea v-model="reason" slot="right" readonly  :show-counter="false"></yd-textarea>
            </yd-cell-item>
        </yd-cell-group>
        <!--等待退货 用户发送退货包裹  -->
        <div v-if="status === 2 && reshipStatus === 1">
            <h4>退货邮寄地址信息</h4>
            <p>{{ reship.reship_name }}</p>
            <p>{{ reship.reship_mobile }}</p>
            <p>{{ reship.reship_area }} {{ reship.reship_address }}</p>
            选择快递公司:
            <select @change="selected">
                <option :value="item.code" v-for="(item, index) in express" :key="index">{{ item.name }}</option>
            </select>
            请输入快递单号:<input type="text" v-model="logisticsCode">
        <yd-button size="large" type="danger" @click.native="send">提交</yd-button>
        </div>
        <!--用户已退货   显示物流快递单号信息-->
        <div v-if="status === 2 && reshipStatus > 1">
            <span>快递公司:{{ logistics }}</span>
            <span>快递单号:{{ logisticsCode }}</span>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            aftersales_id: this.$route.query.aftersales_id || 0, // 售后单id
            items: [], // 退货商品
            type: '', // 售后类型 退款或退货
            typeName: '', // 售后类型名称
            status: '', // 售后审核状态
            statusName: '', // 售后状态
            reason: '', // 售后问题描述
            mark: '', // 商家回复
            images: [], // 售后的图片
            refund: '', // 退款金额
            refundName: '', // 退款状态
            reship_id: '', // 退货单号
            reshipStatus: '', // 退货单状态
            reshipName: '', // 退款单状态描述
            reship: [], // 商家邮寄地址
            express: [
                {name: '顺丰', code: 'sf'},
                {name: '申通', code: 'sto'},
                {name: '圆通', code: 'yt'},
                {name: '中通', code: 'zto'},
                {name: '百世', code: 'ht'},
                {name: '韵达', code: 'yd'}
            ],
            logistics: '', // 快递公司
            logisticsCode: '' // 快递单号
        }
    },
    created () {
        this.afterSalesDetail()
    },
    methods: {
        afterSalesDetail () {
            this.$api.afterSalesInfo({
                aftersales_id: this.aftersales_id
            }, res => {
                if (res.status) {
                    let info = res.data.info
                    this.reason = info.reason // 问题描述
                    this.refund = info.refund // 退货金额
                    this.images = info.images // 退货凭证图片
                    this.mark = info.mark
                    // 判断type类型是退货还是退款
                    if (info.type === 1) {
                        this.type = 1
                        this.typeName = '仅退款'
                    } else {
                        this.type = 2
                        this.typeName = '退货退款'
                        this.items = info.items // 退货商品
                        if (res.data.reship) {
                            this.reship = res.data.reship
                        }
                    }
                    // 判断审核状态
                    if (info.status === 1) {
                        this.status = 1
                        this.statusName = '审核中'
                    } else if (info.status === 2) {
                        this.status = 2
                        this.statusName = '审核通过'
                        // 退款单状态
                        if (info.bill_refund) {
                            if (info.bill_refund.status === 1) {
                                this.refundName = '退款中'
                            } else if (info.bill_refund.status === 2) {
                                this.refundName = '退款成功'
                            }
                        }
                        // 退货单状态
                        if (info.bill_reship) {
                            this.reship_id = info.bill_reship.reship_id
                            if (info.bill_reship.status === 1) {
                                this.reshipStatus = 1
                                this.reshipName = '待发退货'
                            } else if (info.bill_reship.status === 2) {
                                this.reshipStatus = 2
                                this.reshipName = '待收退货'
                                this.logistics = info.bill_reship.logi_code
                                this.logisticsCode = info.bill_reship.logi_no
                            } else {
                                this.reshipStatus = 3
                                this.reshipName = '已收退货'
                                this.logistics = info.bill_reship.logi_code
                                this.logisticsCode = info.bill_reship.logi_no
                            }
                        }
                    } else {
                        this.statusName = '申请驳回'
                    }
                }
            })
        },
        selected (e) {
            this.logistics = e.target.value
        },
        // 用户发送退货包裹
        send () {
            this.$api.sendShip({
                reship_id: this.reship_id,
                logi_code: this.logistics,
                logi_no: this.logisticsCode
            }, res => {
                if (res.status) {
                    this.afterSalesDetail()
                }
            })
        }
    }
}
</script>

<style scoped>
    .orderdetailheader{
        width: 100%;
        height: 2.3rem;
        background: url(../../../static/image/backimg.png) no-repeat;
        background-size: 100% 100%;
    }
    .orderdetailheader>div{
        width: 70%;
        margin: 0 auto;
        text-align: left;
        color: #fff;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }
    .orderdetailheader h3{
        font-size: .3rem;
        margin-bottom: .2rem;
    }
</style>
