<template>
    <div class="evaluate">
        <div v-if="goodsList.length" v-for="(item, index) in goodsList" :key="index">
            <div class="evaluateheader">
                <div class="evaluateheader-top">
                    <img class="goods-img" :src="item.image_url"/>
                    <div class="evaluateheader-img">
                        <div class="good" @click="clickEvaluate(item.goods_id,$event)" data-type="praise">
                            <img :src="evaluate[item.goods_id].praise ? './static/image/redflower.png' : './static/image/whiteflower.png'" data-type="praise"/>
                            <p data-type="praise">好评</p>
                        </div>
                        <div class="average" @click="clickEvaluate(item.goods_id,$event)" data-type="secondary">
                            <img :src="evaluate[item.goods_id].secondary ? './static/image/redflower.png' : './static/image/whiteflower.png'" data-type="secondary"/>
                            <p data-type="secondary">中评</p>
                        </div>
                        <div class="bad" @click="clickEvaluate(item.goods_id,$event)" data-type="difference">
                            <img :src="evaluate[item.goods_id].difference ? './static/image/redflower.png' : './static/image/whiteflower.png'" data-type="difference"/>
                            <p data-type="difference">差评</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="evaluatebody">
                <yd-cell-item>
                    <yd-textarea slot="right" v-model="textarea[item.goods_id]" placeholder="宝贝满足你的期望吗？说说它的优点和美中不足的地方吧" maxlength="200"></yd-textarea>
                </yd-cell-item>
                <div class="uploadimg-list"  v-if="images[item.goods_id].length">
                    <div v-for="(img, index) in images[item.goods_id]" :key="index">
                        <yd-badge @click.native="remove(item.goods_id, index)">X</yd-badge>
                        <img class="thumbnail-list" :src="img.url">
                    </div>
                </div>
                <div class="evaluatebody-img">
                    <div class="uploadimg">
                        <input name="file" type="file" accept="image/png,image/gif,image/jpeg" @change="update(item.goods_id,$event)"/>
                        <img slot="icon" src="../../static/image/addimg.png" v-show="isupload[item.goods_id]">
                    </div>
                </div>
            </div>
        </div>
        <div class="evaluatefooter">
            <yd-cell-item>
                <img slot="icon" src="../../static/image/shop.png">
                <span slot="left">店铺评分</span>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">描述相符</span>
                <yd-rate slot="right" v-model="star_one" size="20px"></yd-rate>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">描述相符</span>
                <yd-rate slot="right" v-model="star_two" size="20px"></yd-rate>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">描述相符</span>
                <yd-rate slot="right" v-model="star_three" size="20px"></yd-rate>
            </yd-cell-item>
        </div>
        <yd-button-group>
            <yd-button size="large" bgcolor="#ff3b44" color="#fff" @click.native="sendEvaluate">提交评价</yd-button>
        </yd-button-group>
    </div>
</template>

<script>
export default {
    data () {
        return {
            order_id: this.$route.query.order_id || 0,
            images: [],
            evaluate: [], // 商品评价
            goodsList: [], // 商品列表
            textarea: [], // 商品评价信息
            products: [], // 货品号
            isupload: [], // 启/禁用 图片上传按钮
            star_one: 5,
            star_two: 5,
            star_three: 5
        }
    },
    created () {
        if (!this.order_id) {
            this.$dialog.alert({
                mes: '该订单不存在',
                callback: () => {
                    this.$router.go(-1)
                }
            })
            return false
        }
        this.$api.orderDetail({
            order_id: this.order_id
        }, res => {
            if (res.data.text_status !== 'pending_evaluate') {
                this.$dialog.alert({
                    mes: '该订单状态有误暂不可评价',
                    callback: () => {
                        this.$router.go(-1)
                    }
                })
                return false
            }
            this.goodsList = res.data.items
            let images = []
            let textarea = []
            let evaluate = []
            let product = []
            let upload = []
            for (let i in this.goodsList) {
                let key = this.goodsList[i].goods_id
                images[key] = []
                textarea[key] = ''
                evaluate[key] = {praise: true, secondary: false, difference: false} // 默认选中
                product[key] = this.goodsList[i].id
                upload[key] = true
            }
            this.images = images
            this.textarea = textarea
            this.evaluate = evaluate
            this.products = product
            this.isupload = upload
        })
    },
    methods: {
        // 改变选中的评价
        clickEvaluate (goodsId, event) {
            let type = event.target.dataset.type
            let praise = false
            let secondary = false
            let difference = false
            if (type === 'praise') {
                praise = true
            } else if (type === 'secondary') {
                secondary = true
            } else if (type === 'difference') {
                difference = true
            }
            this.$set(this.evaluate, goodsId, {praise: praise, secondary: secondary, difference: difference})
        },
        // 上传对应商品的图片
        update (goodsId, e) {
            let file = e.target.files[0]
            let param = new FormData()
            param.append('upfile', file, file.name)
            this.$api.uploadFile('image', param, res => {
                if (res.status) {
                    let img = {
                        url: res.data.url,
                        id: res.data.image_id
                    }
                    this.images[goodsId].push(img)
                }
            })
        },
        // 删除对应的商品评论图片
        remove (goodsId, index) {
            this.images[goodsId].splice(index, 1)
        },
        // 提交评价
        sendEvaluate () {
            let data = {
                order_id: this.order_id,
                seller: {
                    starOne: this.star_one,
                    starTwo: this.star_two,
                    starThree: this.star_three
                }
            }
            let arr = []
            for (let k in this.images) {
                arr[k] = {
                    images: this.images[k],
                    evaluate: this.evaluate[k],
                    textarea: this.textarea[k],
                    product: this.products[k]
                }
            }
            data.goods = arr
            this.$api.orderEvaluate(data, res => {
                if (res.status) {
                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'success'})
                    setTimeout(() => {
                        this.$router.go(-1)
                    }, 1000)
                }
            })
        }
    },
    watch: {
        // 监听图片数量  是否超出限制
        images () {
            for (let k in this.images) {
                if (this.images[k].length > 4) {
                    this.isupload[k] = false
                } else {
                    this.isupload[k] = true
                }
            }
        }
    }
}
</script>

<style>
    .evaluatefooter{
        background-color: #fff;
        margin: .2rem 0 .5rem;
    }
    .evaluatefooter .yd-cell-item:not(:last-child):after{
        border: none;
    }
    .evaluatefooter .yd-cell-right{
        justify-content: flex-start;
        margin-left: .2rem;
    }
</style>
