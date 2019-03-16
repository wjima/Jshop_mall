<template>
    <div class="cart">
        <div class="cartlist" v-if="list.length">
            <ul color="#ff3b44">
                <li class="list-item" :val="item.id" v-for="(item, index) in list" :key="index" data-type="0">
                    <div class="list-box" style="padding: .15rem;position: relative;" @touchstart.capture="touchStart" @touchend.capture="touchEnd">
                        <div class="check-box">
                            <input type="checkbox" @click="radioHandler(index)" class="input_check" name="box" :id="item.id" :value="item.id" v-model="item.is_select"><label :for="item.id"></label>
                        </div>
                        <img :src="item.products.image_path" class="goodsimg" @click="showDetail(item.products.goods_id)">
                        <div class="list-body">
                            <h3 class="goodsname" @click="showDetail(item.products.goods_id)">{{ item.products.name }}</h3>
                            <p class="standard" @click="showDetail(item.products.goods_id)">{{ item.products.spes_desc }}</p>
                            <ul class="btn-numbox">
                                <!--<li>-->
                                <p class="price" @click="showDetail(item.products.goods_id)">￥{{ item.products.price }}</p>
                                <!--</li>-->
                                <li class="spinner">
                                    <ul class="count">
                                        <li @click="minus(item.id,item.nums)"><button id="num-jian" class="num-jian">－</button></li>
                                        <li><input type="text" class="input-num" id="input-num" v-model="item.nums" readonly/></li>
                                        <li @click="add(item.id,item.nums)"><button id="num-jia" class="num-jia">＋</button></li>
                                    </ul>
                                </li>
                            </ul>
                            <div class="goods-tip" v-if="item.products.promotion_list">
                                <div v-for="(val, key) in item.products.promotion_list" :key="key">
                                    <yd-badge shape="square" type="danger" v-if="val.type">{{ val.name }}</yd-badge>
                                    <yd-badge shape="square" type="square" v-else>{{ val.name }}</yd-badge>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="delete" @click="deleteItem" :data-index="index">删除</div>
                </li>
            </ul>
        </div>
        <div class="cartlist data-none" v-else>
            空空如也，快去挑选喜欢的商品吧~
        </div>
        <div class="cartfooter-top" v-if="list.length">
            <div class="cartfooter-left-price" v-if="promotion.length !== 0">
                <div class="content-header">
                    <p>促销</p>
                </div>
                <div v-for="(val, key) in promotion" :key="key">
                    <yd-badge shape="square" :type="val.type === 2 ? 'danger' : ''">{{ val.name }}</yd-badge>
                    <!--<yd-badge shape="square" type="square" v-else>{{ val.name }}</yd-badge>-->
                </div>
            </div>
            <div class="cartfooter-right-price">
                <p>商品优惠：<span class="price">￥{{ goods_pmt || 0 }}</span></p>
                <p>订单优惠：<span class="price">￥{{ order_pmt || 0 }}</span></p>
            </div>
        </div>
        <div class="cartfooter">
            <div class="cartfooter-left">
                <!--<yd-checkbox v-model="isCheckAll" shape="circle" color="#ff3b44" :change="checkAll" v-if="list.length"></yd-checkbox>-->
                <div class="check-box" v-if="list.length">
                    <input type="checkbox" id="allcheck" class="input_check" v-model="isCheckAll" @click="checkAll($event)"><label for="allcheck"></label><span>全选</span>
                </div>
                <div class="cartfooter-left-price">
                    <p>合计：<span class="price">￥{{ total || 0 }}</span></p>
                    <p class="annotation">不包含运费</p>
                </div>
            </div>
            <div class="cartfooter-btn">
                <yd-button type="danger" @click.native="balance" v-if="canBuy">去结算</yd-button>
                <yd-button type="disabled" disabled v-else>去结算</yd-button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            isCheckAll: true, // 默认全选
            list: [], // 购物车列表
            total: '', // 商品总金额
            promotion: {}, // 促销信息
            goods_pmt: '', // 商品优惠
            order_pmt: '' // 订单优惠
        }
    },
    created () {
        this.cartList()
    },
    // 数据改变自动执行此方法
    computed: {
        canBuy () {
            let ids = []
            this.list.forEach(item => {
                if (item.is_select) {
                    ids.push(item.id)
                }
            })
            if (ids.length) {
                return true
            } else {
                return false
            }
        }
    },
    methods: {
        // 商品列表 单选点击操作
        radioHandler (index) {
            this.list[index].is_select = !this.list[index].is_select
            this.selectedHandler()
        },
        // 获取选中的商品重新 返回购物车数据
        selectedHandler () {
            let ids = []
            this.list.forEach(item => {
                if (item.is_select) {
                    ids.push(item.id)
                }
            })
            if (!ids.length) {
                this.list.forEach(item => {
                    item.is_select = false
                })
                this.total = '0.00'
                this.goods_pmt = '0.00'
                this.order_pmt = '0.00'
                this.isCheckAll = false
                for (let k in this.promotion) {
                    this.promotion[k].type = 1
                }
            } else {
                this.cartList(ids)
            }
        },
        // 查看商品详情
        showDetail (goodsId) {
            this.$router.push({path: '/goodsdetail', query: {goods_id: goodsId}})
        },
        // 商品数量减
        minus (id, nums) {
            this.setNums(id, --nums)
        },
        // 商品数量加
        add (id, nums) {
            // 购买数量不能大于库存数量
            for (let k in this.list) {
                if (this.list[k].id === id) {
                    if (nums < this.list[k].products.stock) {
                        this.setNums(id, ++nums)
                    } else {
                        return false
                    }
                }
            }
        },
        // 设置商品数量
        setNums (id, nums) {
            this.$api.setCartNum({
                id: id,
                nums: nums
            }, res => {
                if (res.status) {
                    const _list = res.data.list
                    this.total = this.GLOBAL.formatMoney(res.data.amount, 2, '')
                    this.promotion = res.data.promotion_list
                    this.goods_pmt = this.GLOBAL.formatMoney(res.data.goods_pmt, 2, '')
                    this.order_pmt = this.GLOBAL.formatMoney(res.data.order_pmt, 2, '')
                    this.list = [..._list]
                }
            })
        },
        // 获取购物车数据
        cartList (ids = []) {
            let data = {}
            if (ids.length) {
                data['ids'] = ids.toString()
                data['display'] = 'all'
            }
            this.$api.cartList(data, res => {
                if (res.status) {
                    const _list = res.data.list
                    this.total = this.GLOBAL.formatMoney(res.data.amount, 2, '') // 总金额
                    this.promotion = res.data.promotion_list // 促销信息
                    this.goods_pmt = this.GLOBAL.formatMoney(res.data.goods_pmt, 2, '') // 商品名优惠
                    this.order_pmt = this.GLOBAL.formatMoney(res.data.order_pmt, 2, '') // 订单优惠
                    this.list = [..._list]
                }
            })
        },
        // 监听全选
        checkAll (e) {
            // 全部取消/选中状态
            this.list.forEach(item => {
                item.is_select = e.target.checked
            })
            this.selectedHandler()
        },
        // 去结算
        balance () {
            let ids = []
            this.list.forEach(item => {
                if (item.is_select) {
                    ids.push(item.id)
                }
            })
            this.$router.push({path: '/firmorder', query: {cartIds: ids.join(',')}})
        },
        // 跳转
        skip () {
        },
        // 滑动开始
        touchStart (e) {
            // 记录初始位置
            this.startX = e.touches[0].clientX
        },
        // 滑动结束
        touchEnd (e) {
            // 当前滑动的父级元素
            let parentElement = e.currentTarget.parentElement
            // 记录结束位置
            this.endX = e.changedTouches[0].clientX
            // 左滑
            if (parentElement.dataset.type === '0' && this.startX - this.endX > 30) {
                this.restSlide()
                parentElement.dataset.type = 1
            }
            // 右滑
            if (parentElement.dataset.type === '1' && this.startX - this.endX < -30) {
                this.restSlide()
                parentElement.dataset.type = 0
            }
            this.startX = 0
            this.endX = 0
        },
        // 判断当前是否有滑块处于滑动状态
        checkSlide () {
            let listItems = document.querySelectorAll('.list-item')
            for (let i = 0; i < listItems.length; i++) {
                if (listItems[i].dataset.type === '1') {
                    return true
                }
            }
            return false
        },
        // 复位滑动状态
        restSlide () {
            let listItems = document.querySelectorAll('.list-item')
            // 复位
            for (let i = 0; i < listItems.length; i++) {
                listItems[i].dataset.type = 0
            }
        },
        // 删除
        deleteItem (e) {
            // 当前索引
            let index = e.currentTarget.dataset.index
            this.$api.removeCart({
                ids: this.list[index].id
            }, res => {
                if (res.status) {
                    this.$dialog.toast({mes: res.msg, time: 1500, icon: 'success'})
                    // 复位
                    this.restSlide()
                    // 删除
                    this.list.splice(index, 1)
                    let arr = []
                    for (let k in this.list) {
                        arr.push(this.list[k].id)
                    }
                    this.ids = arr
                    this.cartList(this.ids)
                }
            })
        }
    },
    watch: {
        list: {
            handler () {
                let length = this.list.length
                let ids = []
                this.list.forEach(item => {
                    if (item.is_select) {
                        ids.push(item.id)
                    }
                })
                if (length === ids.length) {
                    this.isCheckAll = true
                } else {
                    this.isCheckAll = false
                }
            },
            deep: true
        }
    }
}
</script>

<style type="text/css">
    .check-box{
        float: left;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        height: 100%;
        padding: 0 .1rem;
    }
    .input_check{
        display: none;
    }
    .input_check + label {
        width: 20px;
        height: 20px;
    }
    .input_check:checked + label:before {
        top: 4px;
        left: 4px;
        width: 20px;
        height: 20px;
    }
    .input_check+label {
        background-color: #FFF;
        border: 1px solid #ccc;
        padding: 9px;
        border-radius: 1000px;
        display: inline-block;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }
    .input_check:checked+label {
        background-color: #ECF2F7;
        border: 1px solid #F43530;
        color: #243441
    }

    .input_check:checked+label:before {
        content: ' ';
        border-radius: 100px;
        position: absolute;
        background: #F43530;
        display: none;
        top: 3px;
        left: 3px;
        width: 12px;
        height: 12px
    }

    .input_check:checked+label:before {
        content: ' ';
        display: block
    }
    .cartfooter-left .check-box span{
        float: right;
        margin-left: .2rem;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }
    .not-click {
        background-color: #eee;
        color: #999 !important;
    }
    .goods-tip{
        margin-top: .1rem;
    }
    .goods-tip>div{
        display: inline-block;
        margin-bottom: .1rem;
        margin-left: .1rem;
    }
</style>
