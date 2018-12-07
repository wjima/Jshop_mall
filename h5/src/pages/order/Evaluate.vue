<template>
    <div class="evaluate">
        <div v-if="goodsList.length" v-for="(item, index) in goodsList" :key="index">
            <div class="evaluateheader">
                <div class="evaluateheader-top">
                    <img class="goods-img" :src="item.image_url"/>
                    <yd-cell-item>
                        <yd-rate slot="right" v-model="score[item.id]" size="20px"></yd-rate>
                    </yd-cell-item>
                </div>
            </div>
            <div class="evaluatebody">
                <yd-cell-item>
                    <yd-textarea slot="right" v-model="textarea[item.id]" placeholder="宝贝满足你的期望吗？说说它的优点和美中不足的地方吧" maxlength="200"></yd-textarea>
                </yd-cell-item>
                <div class="uploadimg-list"  v-if="images[item.id].length">
                    <div v-for="(img, index) in images[item.id]" :key="index">
                        <yd-badge @click.native="remove(item.id, index)">X</yd-badge>
                        <img class="thumbnail-list" :src="img.url">
                    </div>
                </div>
                <div class="evaluatebody-img">
                    <div class="uploadimg">
                        <input name="file" type="file" accept="image/png,image/gif,image/jpeg" @change="update(item.id,$event)"/>
                        <img slot="icon" src="../../../static/image/addimg.png" v-show="isupload[item.id]">
                    </div>
                </div>
            </div>
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
            score: [], // 商品评价
            goodsList: [], // 商品列表
            textarea: [], // 商品评价信息
            isupload: [] // 启/禁用 图片上传按钮
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
            let score = []
            let upload = []
            for (let i in this.goodsList) {
                let key = this.goodsList[i].id
                images[key] = []
                score[key] = 5
                textarea[key] = ''
                upload[key] = true
            }
            this.images = images
            this.textarea = textarea
            this.score = score
            this.isupload = upload
        })
    },
    methods: {
        // 上传对应商品的图片
        update (key, e) {
            let file = e.target.files[0]
            let data = new FormData()
            data.append('upfile', file, file.name)
            this.$api.uploadFile(data, res => {
                if (res.status) {
                    let img = {
                        url: res.data.url,
                        id: res.data.image_id
                    }
                    this.images[key].push(img)
                }
            })
        },
        // 删除对应的商品评论图片
        remove (key, index) {
            this.images[key].splice(index, 1)
        },
        // 提交评价
        sendEvaluate () {
            let data = {
                order_id: this.order_id,
                items: {}
            }
            for (let k in this.images) {
                data.items[k] = {
                    images: this.images[k],
                    score: this.score[k],
                    textarea: this.textarea[k]
                }
            }
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
                if (this.images[k].length >= 4) {
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
