<template>
    <div class="afterservice">
        <afterservicelist :goods="goodsList" @checked="checked" :delivery="is_delivery"></afterservicelist>
        <afterservicecell :delivery="is_delivery" :price="afterPrice" @type="afterType" @custPrice="custPrice"></afterservicecell>
        <afterserviceimg @images="images"></afterserviceimg>
        <afterservicetext @reason="reason"></afterservicetext>
        <yd-button-group>
            <yd-button size="large" bgcolor="#ff3b44" color="#fff" @click.native="formSubmit">提交</yd-button>
        </yd-button-group>
    </div>
</template>

<script>
import afterservicelist from '../../components/AfterServiceList.vue'
import afterservicecell from '../../components/AfterServiceCell.vue'
import afterserviceimg from '../../components/AfterServiceImg.vue'
import afterservicetext from '../../components/AfterServiceText.vue'

export default {
    components: {
        afterservicelist, afterservicecell, afterserviceimg, afterservicetext
    },
    data () {
        return {
            order_id: this.$route.query.order_id || 0,
            is_delivery: true, // 是否已经发货
            goodsList: [], // 售后商品列表
            type: 1, // 退款类型 默认仅退款
            img_id: [], // 上传的售后图片id
            items: [], // 选中的退货商品
            reasonMsg: '', // 问题描述
            afterPrice: '' // 退款金额
        }
    },
    created () {
        if (!this.order_id) {
            this.$dialog.alert({mes: '订单不存在'})
            return false
        }
        this.$api.afterSalesStatus({
            order_id: this.order_id
        }, res => {
            if (res.status) {
                let status = res.data.text_status // 判断订单状态是否可以申请售后
                if (status !== 'pending_payment' && status !== 'completed' && status !== 'cancel') {
                    // 未发货 禁用可退货按钮则只可以申请退款
                    if (status === 'pending_delivery') {
                        this.is_delivery = false
                    }
                    this.goodsList = res.data.items
                    this.afterPrice = res.data.payed - res.data.refunded
                    for (let i in this.goodsList) {
                        this.items = this.items.concat({id: this.goodsList[i].id, nums: this.goodsList[i].nums})
                    }
                } else {
                    this.$dialog.alert({
                        mes: '该订单状态不可申请售后',
                        callback: () => {
                            this.$router.go(-1)
                        }
                    })
                }
            } else {
                this.$dialog.alert({
                    mes: '该订单不存在',
                    callback: () => {
                        this.$router.go(-1)
                    }
                })
            }
        })
    },
    methods: {
        // 获取选中的商品
        checked (list) {
            let arr = []
            for (let i in list) {
                for (let k in this.goodsList) {
                    if (list[i] === this.goodsList[k].id) {
                        arr.push({id: this.goodsList[k].id, nums: this.goodsList[k].nums})
                    }
                }
            }
            this.items = arr
        },
        // 传过来的图片id
        images (imageList) {
            for (let i in imageList) {
                if (this.img_id.indexOf(imageList[i].image_id) === -1) {
                    this.img_id.push(imageList[i].image_id)
                }
            }
        },
        // 问题描述
        reason (reason) {
            this.reasonMsg = reason
        },
        // 退货类型 退款金额
        custPrice (price) {
            this.afterPrice = price
        },
        // 退款类型
        afterType (type) {
            this.type = type
        },
        // 提交售后申请
        formSubmit () {
            this.$api.addAfterSales({
                order_id: this.order_id,
                type: this.type,
                images: this.img_id,
                items: this.items,
                refund: this.afterPrice,
                reason: this.reasonMsg
            }, res => {
                if (res.status) {
                    this.$dialog.toast({mes: '提交成功', time: 1500, icon: 'success'})
                }
            })
        }
    }
}
</script>

<style>
.afterservice .yd-cell-box{
    margin: .15rem 0;
}
.afterservice .yd-cell-title{
    background-color: #fff;
    padding: .1rem .24rem 0rem;
    color: #666;
}
.afterservice .yd-cell-title:after{
    border: none;
}
</style>
