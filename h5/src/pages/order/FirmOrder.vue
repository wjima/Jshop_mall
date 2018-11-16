<template>
    <div class="firmorder">
        <orderadd
            :openStore="openStore"
            :userShip="userShip"
            @clickHandler="clickHandler"
        ></orderadd>
        <orderlist
            :products="products"
        ></orderlist>
        <ordercoupon
            :coupon="couponList"
            @couponChecked="couponChecked"
        ></ordercoupon>
        <ordercell
            :goods_amount="goods_amount"
            :amount="amount"
            :cost_freight="cost_freight"
            :goods_pmt="goods_pmt"
            :order_pmt="order_pmt"
            :coupon_pmt="coupon_pmt"
            :open_point="point_open"
            :point_sum="point_sum"
            :usable_point="usable_point"
            :point_money="point_money"
            @isUsePoint="isUsePoint"
        ></ordercell>
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
import ordercoupon from '../../components/OrderCoupon.vue'
import ordercell from '../../components/OrderCell.vue'
import orderinput from '../../components/OrderInput.vue'
import orderfooter from '../../components/OrderFooter.vue'

export default {
    components: {
        orderadd, orderlist, ordercoupon, ordercell, orderinput, orderfooter
    },
    data () {
        return {
            cartIds: this.$route.query.cartIds ? this.$route.query.cartIds : '', // 传递过来的购物车id
            openStore: false,
            products: [], // 货品信息
            goods_amount: '', // 商品总金额
            amount: '', // 总金额
            goods_pmt: '', // 商品优惠
            order_pmt: '', // 订单优惠
            cost_freight: '', // 运费
            coupon_pmt: '', // 使用优惠券优惠金额
            msg: '', // 卖家留言内容
            userShip: [], // 用户的默认收货地址
            couponList: [], // 用户的优惠券列表
            selectCoupon: '', // 选中的优惠券
            point_open: false, // 判断后台是否开启积分抵扣
            point_sum: 0, // 用户总积分
            usable_point: 0, // 可抵扣积分
            point_money: 0, // 积分抵扣的金额
            use_point: false // 是否使用积分
        }
    },
    mounted () {
        this.isOpenStore() // 判断是否开启门店
        this.userDefaultShip() // 获取默认收货地址
        this.getUserCoupon() // 获取用户可用的优惠券列表
        this.orderUsablePoint()
    },
    methods: {
        // 判断是否开启门店
        isOpenStore () {
            this.$api.switchStore({}, res => {
                if (res.status) {
                    res.data === '1' ? this.openStore = true : this.openStore = false
                }
            })
        },
        // 获取用户的默认收货地址
        userDefaultShip () {
            this.$api.userDefaultShip({}, res => {
                this.userShip = res.data
                this.getCartList(this.userShip.area_id)
            })
        },
        // 获取用户可用的优惠券列表
        getUserCoupon () {
            this.$api.userCoupon({}, res => {
                this.couponList = res.data
                this.couponList.forEach((item) => this.$set(item, 'checked', false))
            })
        },
        // 获取商品信息
        getCartList (areaId = 0, couponCode = 0, usePoint = false) {
            if (!this.cartIds) {
                this.$dialog.alert({
                    mes: '请选择要购买的商品',
                    callback: () => {
                        this.$router.go(-1)
                    }
                })
                return false
            }
            let data = {ids: this.cartIds}
            if (areaId) data['area_id'] = areaId // 使用的收货地址id
            if (couponCode) data['coupon_code'] = couponCode // 使用的优惠券code
            if (usePoint) data['point'] = this.usable_point
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
                }
            })
        },
        // 判断是否开启积分和 可兑换的积分金额
        orderUsablePoint (money) {
            this.$api.usablePoint({order_money: money},  res => {
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
        // 用户选中/取消的优惠券 选中取消状态
        // 选中false更改状态为true
        // 取消反之
        couponChecked (index = []) {
            if (!index[1]) {
                // 取消选中其他的的优惠券
                this.couponList.forEach((item) => {
                    if (item.checked) item.checked = false
                })
                // 设置选中的优惠券
                this.selectCoupon = this.couponList[index[0]]
                this.$set(this.couponList[index[0]], 'checked', true)
                // 重新请求数据
                this.getCartList(this.userShip.area_id, this.selectCoupon.coupon_code)
            } else {
                this.selectCoupon = ''
                this.$set(this.couponList[index[0]], 'checked', false)
                this.getCartList(this.userShip.area_id, '')
            }
        },
        // 用户的选中的收货地址
        clickHandler (index) {
            this.userShip = index
            this.getCartList(this.userShip.area_id, this.selectCoupon, this.use_point)
        },
        // 卖家留言
        sendMsg (msg) {
            this.msg = msg
        },
        // 用户点击使用积分
        isUsePoint (checked) {
            this.use_point = checked
            this.getCartList(this.userShip.area_id, 0, this.use_point)
        },
        // 去支付  生成支付单
        // 获取收货地址id
        // 商品信息  总价格
        payment () {
            if (!this.userShip) {
                this.$dialog.alert({mes: '请选择收货地址'})
                return false
            }
            let data = {
                uship_id: this.userShip.id,
                cart_ids: this.cartIds,
                memo: this.msg,
                area_id: this.userShip.area_id
            }
            if (this.selectCoupon) data['coupon_code'] = this.selectCoupon.coupon_code
            this.$api.createOrder(data, res => {
                this.$router.replace({path: '/cashierdesk', query: {order_id: res.data.order_id}})
            })
        }
    }
}
</script>

<style>
</style>
