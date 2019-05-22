<template>
    <div class="firmorder">
        <openstore
            v-if="isOpenStore === 1 && storeInfo"
            :storeTab="storeTab"
            :store="storeInfo"
            :ship="shipInfo"
            @storeTab="selectStoreTab"
            @consignee="consignee"
            @shipHandler="shipHandler"
            @storeHandler="storeHandler"
        ></openstore>
        <orderadd
            v-else
            :ship="shipInfo"
            @shipHandler="shipHandler"
        ></orderadd>
        <orderlist
            :products="products"
        ></orderlist>
        <div class="ordercoupon">
            <yd-cell-item @click.native="showCouponHandler">
                <span slot="left">优惠券</span>
                <span slot="right" class="demo-list-price">{{ usedCouponName }}</span>
            </yd-cell-item>
            <yd-popup v-model="couponShow" position="bottom" height="40%" class="ordercoupon-content">
                <yd-tab v-model="couponPopTab" :prevent-default="false" :item-click="couponPopClickHandler">
                    <yd-tab-panel label="已领取优惠券">
                        <yd-cell-group class="pop-cell">
                            <div v-if="userCoupon.length">
                                <yd-cell-item @click.native="couponChecked(index, item.checked)" v-for="(item, index) in userCoupon" :key="index">
                                    <span slot="left">{{ item.name }}</span>
                                    <span slot="right" class="demo-list-price"><img :src="couponCheck[0]" v-show="!item.checked"/><img :src="couponCheck[1]" v-show="item.checked"/></span>
                                </yd-cell-item>
                            </div>
                            <div v-else>
                                没有优惠券 快去领取一个吧
                            </div>
                        </yd-cell-group>
                    </yd-tab-panel>
                    <yd-tab-panel label="输入优惠券号码">
                        <div class="coupon-input">
                            <input type="text" v-model="entryCouponCode" placeholder="请输入优惠券号码">
                            <button type="button" @click.passive="receiveCoupon">确定</button>
                        </div>
                    </yd-tab-panel>
                </yd-tab>
                <yd-button type="danger" style="margin: 30px;" @click.native="couponShow=false">关闭</yd-button>
            </yd-popup>
        </div>
        <ordercell
            ref="mychild"
            :goods_amount="goods_amount"
            :amount="amount"
            :cost_freight="cost_freight"
            :goods_pmt="goods_pmt"
            :order_pmt="order_pmt"
            :coupon_pmt="coupon_pmt"
            :open_point="point_open"
            :use_point="use_point"
            :point_sum="point_sum"
            :usable_point="usable_point"
            :point_money="point_money"
            @isUsePoint="isUsePoint"
        ></ordercell>
        <div class="invoice-item" v-if="invoiceSwitch === 1">
            <yd-cell-group>
                <yd-cell-item arrow>
                    <span slot="left">发票</span>
                    <span slot="right" @click="toInvoice" v-if="invoice.type != 1 && invoice.name">{{invoice.name}}</span>
                    <span slot="right" @click="toInvoice" v-else>{{tax_name}}</span>
                </yd-cell-item>
            </yd-cell-group>
        </div>
        <orderinput
            @msg="sendMsg"
        ></orderinput>
        <orderfooter
            :amount="amount"
            @payment="payment"
        ></orderfooter>
    </div>
</template>

<script>
import orderadd from '../../components/OrderAdd.vue'
import orderlist from '../../components/OrderList.vue'
import ordercell from '../../components/OrderCell.vue'
import orderinput from '../../components/OrderInput.vue'
import orderfooter from '../../components/OrderFooter.vue'
import openstore from '../../components/OpenStore'
import {mapGetters} from 'vuex'
export default {
    components: {
        orderadd, orderlist, ordercell, orderinput, orderfooter, openstore
    },
    data () {
        return {
            cartIds: this.$route.query.cartIds ? this.$route.query.cartIds : '', // 传递过来的购物车id
            products: [], // 货品信息
            goods_amount: '', // 商品总金额
            amount: '', // 总金额
            goods_pmt: '', // 商品优惠
            order_pmt: '', // 订单优惠
            cost_freight: '', // 运费
            coupon_pmt: '', // 使用优惠券优惠金额
            msg: '', // 卖家留言内容
            couponShow: false, // 显示优惠券弹窗
            usedCouponName: '未使用', // 优惠券名称
            usedCouponCode: '', // 使用的优惠券
            entryCouponCode: '', // 手动输入的优惠券码
            couponPopTab: 0, // 优惠券弹窗tab
            userCoupon: [], // 已领取优惠券列表
            couponCheck: [
                'static/image/kong.png', // 未选中
                'static/image/xuanzhong.png' // 选中
            ],
            params: {
                ids: '', // 传递过来的购物车id
                area_id: 0, // 收货地址id
                coupon_code: '', // 优惠券码
                point: 0 // 抵扣积分额
            },
            storeTab: 0,
            shipInfo: {}, // 收货地址id
            storeInfo: {}, // 门店id
            ladingName: '', // 提货人姓名
            ladingMobile: '', // 提货人联系方式
            point_open: false, // 判断后台是否开启积分抵扣
            point_sum: 0, // 用户总积分
            usable_point: 0, // 可抵扣积分
            point_money: 0, // 积分抵扣的金额
            use_point: false, // 是否使用积分
            receiptType: 1, // 1快递配送 2门店自提
            tax_type: 1,
            tax_name: '不开发票',
            tax_code: ''
        }
    },
    mounted () {
        this.params.ids = this.cartIds
        this.userDefaultShip() // 获取默认收货地址
        this.orderUsablePoint()
    },
    computed: {
        // 从vuex 中取门店开启状态
        isOpenStore () {
            let status = this.$store.state.config.store_switch
            if (status === 1) {
                this.getDefaultStore()
            }
            return status
        },
        // 获取发票开启状态
        invoiceSwitch () {
          return this.$store.state.config.invoice_switch || 1
        },
        // 从vuex中获取发票信息
        ...mapGetters([
            'invoice'
        ])
    },
    methods: {
        // 获取默认门店
        getDefaultStore () {
            this.$api.defaultStore({}, res => {
                if (res.status) {
                    this.storeInfo = res.data
                }
            })
        },
        // 门店tab切换
        selectStoreTab (tab) {
            this.storeTab = tab
            if (this.storeTab === 1) {
                this.params.area_id = this.storeInfo.area_id
                this.receiptType = 2
            } else {
                this.params.area_id = this.shipInfo.area_id
                this.receiptType = 1
            }
            // 取消积分选中
            this.use_point = false
            this.$refs.mychild.checked = status
            this.params.point = 0
        },
        // 获取用户的默认收货地址
        userDefaultShip () {
            this.$api.userDefaultShip({}, res => {
                if (res.status) {
                    if (res.data) {
                        this.shipInfo = res.data
                        this.params.area_id = res.data.area_id
                    }
                }
            })
        },
        // 获取商品信息
        getCartList () {
            if (!this.cartIds) {
                this.$dialog.alert({
                    mes: '请选择要购买的商品',
                    callback: () => {
                        this.$router.go(-1)
                    }
                })
                return false
            }

            let data = this.params
            let type = {receipt_type: this.receiptType}
            data = Object.assign(data, type)

            this.$api.cartList(data, res => {
                if (res.status) {
                    let list = res.data.list
                    let resData = res.data
                    this.products = list
                    this.goods_amount = resData.goods_amount
                    this.amount = resData.amount
                    this.cost_freight = resData.cost_freight
                    this.goods_pmt = resData.goods_pmt
                    this.order_pmt = resData.order_pmt
                    this.coupon_pmt = resData.coupon_pmt
                    if (!this.use_point) {
                        this.orderUsablePoint(this.amount)
                    }
                    if (resData.coupon) {
                        for (let i in resData.coupon) {
                            this.usedCouponName = resData.coupon[i]
                            this.usedCouponCode = i
                        }
                        this.couponShow = false
                    }
                } else {
                    if (res.data === 15009) {
                        setTimeout(() => {
                            this.entryCouponCode = ''
                        }, 1500)
                    }
                }
            })
        },
        // 判断是否开启积分和 可兑换的积分金额
        orderUsablePoint (money) {
            this.$api.usablePoint({order_money: money}, res => {
                if (res.status) {
                    if (res.switch === 1) {
                        this.point_open = true
                    }
                    this.point_sum = res.data
                    this.usable_point = res.available_point
                    this.point_money = res.point_rmb
                }
            })
        },
        // 用户的选中的收货地址
        shipHandler (val) {
            this.shipInfo = val
            this.params.area_id = val.area_id
        },
        // 用户选中的门店信息
        storeHandler (val) {
            this.storeInfo = val
            this.params.area_id = val.area_id
        },
        // 卖家留言
        sendMsg (msg) {
            this.msg = msg
        },
        // 去支付  生成支付单
        // 获取收货地址id
        // 商品信息  总价格
        payment () {
            // if (!this.userShip) {
            //     this.$dialog.alert({mes: '请选择收货地址'})
            //     return false
            // }
            let data = {
                cart_ids: this.cartIds,
                memo: this.msg,
                coupon_code: this.usedCouponCode ? this.usedCouponCode : '',
                point: this.use_point ? this.usable_point : '',
                receipt_type: this.receiptType
            }
            if (this.storeTab === 0) {
                // 快递配送
                data['uship_id'] = this.shipInfo.id
                data['area_id'] = this.shipInfo.area_id
            } else {
                // 门店自提
                data['store_id'] = this.storeInfo.id
                data['area_id'] = this.storeInfo.area_id
                data['lading_name'] = this.ladingName
                data['lading_mobile'] = this.ladingMobile
            }
            // 发票信息
            data['tax_type'] = this.invoice.type
            data['tax_name'] = this.invoice.name
            data['tax_code'] = this.invoice.code

            // 跳转支付页面 商品订单类型1 充值订单类型2
            this.$api.createOrder(data, res => {
                if (res.status) {
                    this.$router.replace({path: '/cashierdesk', query: {ids: res.data.order_id, type: 1}})
                }
            })
        },
        // 门店自提 联系人,联系方式
        consignee (val) {
            this.ladingName = val.name
            this.ladingMobile = val.mobile
        },
        // 根据类型判断是 快递配送还是 门店自提 根据地区id 重新请求订单数据
        receipt (key) {
            this.receiptType = key
            if (this.receiptType === 1) {
                this.params.area_id = this.userShip.area_id
            } else {
                this.params.area_id = ''
            }
        },
        showCouponHandler () {
            this.couponShow = true
            if (this.couponPopTab === 0) {
                this.getCouponList()
            }
        },
        // 获取优惠券列表
        getCouponList () {
            this.$api.userCoupon({display: 'no_used'}, res => {
                if (res.status) {
                    this.userCoupon = res.data.list
                    this.userCoupon.forEach(item => {
                        if (this.usedCouponCode) {
                            if (this.usedCouponCode === item.coupon_code) {
                                this.$set(item, 'checked', true)
                            } else {
                                this.$set(item, 'checked', false)
                            }
                        } else {
                            this.$set(item, 'checked', false)
                        }
                    })
                }
            })
        },
        // 优惠券弹窗切换
        couponPopClickHandler (key) {
            this.couponPopTab = key
        },
        // 输入优惠券码
        receiveCoupon () {
            if (!this.entryCouponCode) {
                this.$dialog.toast({
                    mes: '请输入优惠券码',
                    timeout: 1300
                })
            } else {
                this.params.coupon_code = this.entryCouponCode
            }
        },
        // 优惠券选中/取消
        couponChecked (key, checked) {
            this.use_point = false
            this.$refs.mychild.checked = status
            this.params.point = 0
            if (checked === false) {
                // 取消选中其他的的优惠券
                this.userCoupon.forEach((item) => {
                    if (item.checked) item.checked = false
                })
                // 设置选中的优惠券
                this.usedCouponCode = this.userCoupon[key].coupon_code
                this.usedCouponName = this.userCoupon[key].name
                this.userCoupon[key].checked = true
                // 重新请求数据
                this.params.coupon_code = this.usedCouponCode
            } else {
                this.usedCouponCode = ''
                this.userCoupon[key].checked = false
                this.usedCouponName = '未使用'
                this.params.coupon_code = ''
            }
        },
        isUsePoint (status) {
            this.use_point = status
        },
        // 跳转发票页面
        toInvoice () {
            this.$router.push({path: '/invoice'})
        }
    },
    watch: {
        // 监听数据状态(切换收货地址, 是否使用优惠券, 是否使用积分) 重新请求订单数据
        params: {
            handler () {
                this.getCartList()
            },
            deep: true
        },
        // 监听是否使用积分
        use_point () {
            if (this.use_point) {
                this.params.point = this.usable_point
            } else {
                this.params.point = 0
            }
        }
    }
}
</script>

<style>
	.yd-popup{
		max-width: 750px;
		left: auto;
		right: auto;
	}
	.yd-popup .yd-tab-nav:after{
		background-image: none;
	}
    .ordercoupon{
        background-color: #fff;
    }
	.ordercoupon .yd-cell-item:not(:last-child):after{
		border-bottom: 1px solid #e9e9e9 !important;
		margin: 0;
	}
    .ordercoupon-content .yd-btn{
        width: 100%;
        background-color: #FF3B44;
        position: fixed;
        bottom: 0;
        left: 0;
        margin: 0 !important;
        color: #fff;
        border-radius: 0;
        height: .7rem;
		max-width: 750px;
		box-sizing: border-box;
    }
    .ordercoupon-content .yd-cell .demo-list-price img{
        width: .35rem;
        height: .35rem;
    }
    .coupon-input{
        padding: .24rem;
        box-sizing: border-box;
        height: 1rem;
        overflow: hidden;
    }
    .coupon-input input{
        height: 100%;
        padding: 0 .2rem;
        border: 1px solid #eee;
        float: left;
        width: 75%;
    }
    .coupon-input button{
        height: 100%;
        /*width: 1rem;*/
        border: 1px solid #FF3B44;
        background-color: #ff3b44;
        float: left;
        width: 20%;
        margin-left: 4%;
        color: #fff;
    }
	.pop-cell .yd-cell:after{
		background-image: none !important;
		border-bottom: none !important;
	}
</style>
