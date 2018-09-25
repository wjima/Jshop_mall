<template>
    <div class="goodsdetail">
        <slider
            :imgList="goodsData.album"
        ></slider>
        <goodstitle
            :product="goodsProduct"
            :brief="goodsData.brief"
            :buyCount="goodsData.buy_count"
            :unit="goodsData.unit"
        ></goodstitle>
        <goodsservice :promotion="checkPromotion"></goodsservice>
        <goodsstandard :spes="goodsSpes" :products="goodsData.products" @changeSpes="changeSpes"></goodsstandard>
        <goodsnum :stock="stock" @num="goodsNum"></goodsnum>
        <yd-tab>
            <yd-tab-panel label="图文详情">
                <ul v-html="goodsData.intro"></ul>
            </yd-tab-panel>
            <yd-tab-panel class="params" label="产品参数">
                <ul v-if="params.length">
                    <li v-for="(item, index) in params" :key="index">
                        <span id="paramsleft">{{ item.name }} : </span><span id="paramsright">{{ item.value }}</span>
                    </li>
                </ul>
                <ul v-else>没有详细参数</ul>
            </yd-tab-panel>
            <yd-tab-panel class="comment" label="买家评论">
                <ul>
                    <li v-for="(item, index) in comment" :key="index">
                        <div class="">
                            <img :src="item.user.avatar" alt="" class="user-img">
                            <p class="user-name">{{ item.user.nickname }}</p>
                            <yd-rate slot="left" v-model="rate1" v-if="item.score === 1" :readonly="true" size=".2rem"></yd-rate>
                            <yd-rate slot="left" v-model="rate2" v-else-if="item.score === 0" :readonly="true" size=".2rem"></yd-rate>
                            <yd-rate slot="left" v-model="rate3" v-else :readonly="true" size=".2rem"></yd-rate>
                        </div>
                        <p>{{ item.ctime }}  &nbsp;&nbsp;&nbsp;&nbsp;{{ item.addon }}</p>
                        <p>{{ item.content }}</p>
                        <p v-if="item.images_url.length">
                            <img :src="img" v-for="(img, key) in item.images_url" :key="key" width="10px">
                        </p>
                    </li>
                    <li style="text-align: center">
                    <yd-button size="small" type="hollow" color="#F00" shape="circle" v-if="load" @click.native="loadMore">加载更多评论</yd-button>
                    <yd-button size="small" type="hollow" color="#AAA" shape="circle" v-else>没有更多评论了</yd-button>
                    </li>
                </ul>
            </yd-tab-panel>
        </yd-tab>
        <goodsdetailfooter :is_fav="is_fav" @collection="collection" @addCart="add" @buyNow="buyNow"></goodsdetailfooter>
    </div>
</template>

<script>
import slider from '../components/Slider.vue'
import goodstitle from '../components/GoodsTitle.vue'
import goodsservice from '../components/GoodsService.vue'
import goodsstandard from '../components/GoodsStandard.vue'
import goodsnum from '../components/GoodsNum.vue'
import goodsdetailfooter from '../components/GoodsDetailFooter.vue'
export default {
    data () {
        return {
            page: 1, // 商品评论分页参数
            pageSize: 5, // 商品评论分页数量
            goodsId: this.$route.query.goods_id ? this.$route.query.goods_id : '', // vueRouter传过来的商品id
            goodsData: [], // 商品数据
            goodsSpes: [], // 商品全部规格
            goodsProducts: [], // 商品全部规格信息
            goodsProduct: [], // 选中的商品规格信息
            promotion: [], // 促销信息
            stock: '', // 选中规格的库存数量
            params: [], // 商品参数信息
            comment: [], // 商品的评论
            load: true, // 是否显示更多
            is_fav: false, // 是否收藏
            num: 1, // 购买的商品数量 默认为1
            rate1: 5, // 好评
            rate2: 3, // 中评
            rate3: 1 // 差评

        }
    },
    components: {
        slider, goodstitle, goodsservice, goodsstandard, goodsnum, goodsdetailfooter
    },
    created () {
        this.goodsDetail()
        this.goodsParams()
        this.goodsComment()
    },
    computed: {
        // 获取当前选中规格的商品价格及库存信息
        product: {
            get () {
                return this.goodsProduct
            },
            set (spes) {
                if (this.goodsSpes.length !== 0) {
                    let goodsProducts = []
                    for (let i in this.goodsData.products) {
                        if (this.goodsData.products[i].spes_desc === spes) {
                            goodsProducts = this.goodsData.products[i]
                        }
                    }
                    this.goodsProduct = goodsProducts
                    this.stock = goodsProducts.stock
                    this.promotion = this.goodsProduct.promotion_list
                } else {
                    // 单规格商品
                    this.goodsProduct = this.goodsData.default
                    this.stock = this.goodsData.default.stock
                    this.promotion = this.goodsData.promotion_list
                }
            }
        },
        // 促销信息重新计算满足的条件 如果存在不满足的条件 就不显示促销信息
        checkPromotion () {
            if (this.promotion) {
                let arr = []
                for (let k in this.promotion) {
                    if (this.promotion[k].type === 2 || this.promotion[k].type === true) {
                        arr.push(this.promotion[k])
                    }
                }
                return arr
            }
        }
    },
    methods: {
        // 获取商品详情
        goodsDetail () {
            this.$api.goodsDetail({id: this.goodsId, token: this.GLOBAL.getStorage('user_token')}, res => {
                if (res.status && res.data.length !== 0) {
                    this.goodsData = res.data
                    // 获取用户是否收藏
                    if (res.data.isfav === 'false') {
                        this.is_fav = false
                    } else {
                        this.is_fav = true
                    }
                    // 是否有多规格属性
                    if (res.data.spes_desc) {
                        this.goodsSpes = res.data.spes_desc
                    }
                    // 商品默认规格
                    if (res.data.products) {
                        this.goodsProduct = res.data.default
                        this.stock = res.data.default.stock
                        this.promotion = res.data.default.promotion_list
                    }
                    // 添加用户浏览足迹
                    if (this.GLOBAL.getStorage('user_token')) {
                        this.goodsBrowsing()
                    }
                } else {
                    this.$dialog.alert({
                        mes: '该商品不存在',
                        callback: () => {
                            this.$router.go(-1)
                        }
                    })
                }
            })
        },
        // 获取商品参数信息
        goodsParams () {
            this.$api.goodsParams({id: this.goodsId}, res => {
                if (res.status) {
                    this.params = res.data
                }
            })
        },
        // 获取商品评论信息
        goodsComment () {
            this.$api.goodsComment({
                page: this.page,
                limit: this.pageSize,
                goods_id: this.goodsId
            }, res => {
                if (res.status) {
                    const _list = res.data.list
                    for (let k in _list) {
                        _list[k].ctime = this.GLOBAL.timeToDate(_list[k].ctime)
                    }
                    if (_list.length < this.pageSize) {
                        this.load = false
                    }
                    this.comment = [..._list]
                }
            })
        },
        loadMore () {
            this.$api.goodsComment({
                page: ++this.page,
                limit: this.pageSize,
                goods_id: this.goodsId
            }, res => {
                if (res.status) {
                    const _list = res.data.list
                    for (let k in _list) {
                        _list[k].ctime = this.GLOBAL.timeToDate(_list[k].ctime)
                    }
                    if (_list.length < this.pageSize) {
                        this.load = false
                    }
                    this.comment = [...this.comment, ..._list]
                }
            })
        },
        // 切换商品规格
        changeSpes (event) {
            for (let i in this.goodsSpes) {
                if (this.goodsSpes[i].sku_name === event.index) {
                    for (let j in this.goodsSpes[i].sku_value) {
                        if (this.goodsSpes[i].sku_value[j].name === event.val) {
                            this.$set(this.goodsSpes[i].sku_value[j], 'is_defalut', '1')
                        } else {
                            this.$set(this.goodsSpes[i].sku_value[j], 'is_defalut', '2')
                        }
                    }
                    let spes = []
                    for (let i in this.goodsSpes) {
                        for (let j in this.goodsSpes[i].sku_value) {
                            if (this.goodsSpes[i].sku_value[j].is_defalut === '1') {
                                spes += this.goodsSpes[i].sku_name + ':' + this.goodsSpes[i].sku_value[j].name + ','
                            }
                        }
                    }
                    this.product = spes.toString().replace(/,$/, '')
                }
            }
        },
        // 子组件传过来的商品数量
        goodsNum (num) {
            this.num = num
        },
        // 添加商品浏览足迹
        goodsBrowsing () {
            this.$api.addGoodsBrowsing({goods_id: this.goodsData.id}, res => {})
        },
        // 商品关注/取消
        collection () {
            this.$api.goodsCollection({goods_id: this.goodsData.id}, res => {
                if (res.status) {
                    this.is_fav = !this.is_fav
                    this.$dialog.toast({mes: res.msg, timeout: 1000})
                }
            })
        },
        // 加入购物车
        add () {
            this.$api.addCart({product_id: this.goodsProduct.id, nums: this.num}, res => {
                if (res.status) {
                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'success'})
                }
            })
        },
        // 立即购买
        buyNow () {
            this.$api.addCart({product_id: this.goodsProduct.id, nums: this.num, type: 2}, res => {
                if (res.status) {
                    let cartIds = res.data
                    this.$router.push({path: '/firmorder', query: {cartIds}})
                }
            })
        }
    }
}
</script>

<style>
    .comment{
        text-align: left;
    }
    .comment ul{
        padding: 10px;
    }
    .comment li{
        padding: 10px 0;
    }
    .comment .user-img{
        width: 30px !important;
        height: 30px !important;
        border-radius: 50% !important;
    }
    .comment .user-name{
        display: inline-block;
        position: relative;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        color: #a0a0a0;
    }
</style>
