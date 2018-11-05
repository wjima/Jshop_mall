<template>
    <div class="history">
        <yd-infinitescroll :callback="loadMore" :distance="2" ref="infinitescrollDemo">
            <ul slot="list">
                <li class="list-item" data-type="0" v-for="(item, index) in list" :key="index" v-if="item.goods">
                    <div class="list-box" style="padding: .15rem;" @touchstart.capture="touchStart" @touchend.capture="touchEnd" @click="showDetail(item.goods_id)">
                        <img class="goodsimg" slot="img" v-lazy="item.goods.image_url">
                        <div class="list-body">
                            <h3 class="goodsname" slot="title">{{ item.goods_name }}</h3>
                            <div class="btn-numbox">
                                <div>
                                    <span class="demo-list-price"><em>¥</em>{{ item.goods.price }}</span>
                                </div>
                            </div>
                            <div slot="other">
                                <div>
                                    <span class="time">{{ item.ctime }}</span>
                                </div>
                            </div>
                            <img class="right-img" slot="other" src="../../../static/image/right.png"/>
                        </div>
                    </div>
                    <div class="delete" @click="deleteItem" :data-index="index">删除</div>
                </li>
            </ul>
        </yd-infinitescroll>
    </div>
</template>

<script>
export default {
    data () {
        return {
            page: 1,
            pageSize: 10,
            list: [] // 商品浏览足迹
        }
    },
    created () {
        this.goodsBrowsing()
    },
    methods: {
        showDetail (goodsId) {
            this.$router.push({path: '/goodsdetail', query: {goods_id: goodsId}})
        },
        goodsBrowsing () {
            this.$api.goodsBrowsing({
                page: this.page,
                limit: this.pageSize
            }, res => {
                const _list = this.dateFormat(res.data.list)
                this.list = [..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
            })
        },
        // 加载更多
        loadMore () {
            this.$api.goodsBrowsing({
                page: ++this.page,
                limit: this.pageSize
            }, res => {
                const _list = this.dateFormat(res.data.list)
                this.list = [...this.list, ..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                    return
                }
                /* 单次请求数据完毕 */
                this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad')
            })
        },
        dateFormat (list) {
            for (let k in list) {
                list[k].ctime = this.GLOBAL.timeToDate(list[k].ctime)
            }
            return list
        },
        // 跳转
        skip () {
        // if (this.checkSlide()) {
        //    this.restSlide()
        // } else {
        //    alert('You click the slide!')
        // }
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
            this.$api.delGoodsBrowsing({
                goods_ids: this.list[index].goods_id
            }, res => {
                if (res.status) {
                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'success'})
                    // 复位
                    this.restSlide()
                    // 删除
                    this.list.splice(index, 1)
                } else {
                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'error'})
                }
            })
        }
    }
}
</script>

<style>
</style>
