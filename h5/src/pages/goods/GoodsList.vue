<template>
    <div class="goodslist">
        <div class="itembox" v-if="list.length">
            <ul>
                <li v-for="(item, index) in label" :key="index">
                    <span :class="{active: item.checked}" @click="clickFilter(index)">{{ item.title }}</span>
                    <div class="sort-img">
                        <img class="top" :src="item.top ? icons.topChecked : icons.top" v-if="index"/>
                        <img class="bottom" :src="item.bottom ? icons.bottomChecked : icons.bottom" />
                    </div>
                </li>
            </ul>
        </div>
        <yd-infinitescroll :callback="loadMore" :distance="2" :scroll-top="false" ref="infinitescrollDemo" class="itemgoodslist">
            <yd-list theme="4" slot="list">
                <yd-list-item v-for='(item, key) in list' :key='key' type="link" :href="{path:'goodsdetail',query:{goods_id:item.id}}">
                    <img slot='img' v-lazy='item.image_url'>
                    <span slot='title'>{{item.name}}</span>
                    <yd-list-other slot='other'>
                        <div>
                            <span class='demo-list-price'><em>¥</em>{{item.price}}</span>
                        </div>
                    </yd-list-other>
                </yd-list-item>
            </yd-list>
        </yd-infinitescroll>
        <yd-backtop></yd-backtop>
    </div>
</template>

<script>
export default {
    data () {
        return {
            page: 1,
            pageSize: 10, // 每页10条数据
            label: [
                {title: '综合', checked: true, top: false, bottom: true, condition: ''},
                {title: '销量', checked: false, top: false, bottom: false, condition: 'buy_count'},
                {title: '价格', checked: false, top: false, bottom: false, condition: 'price'}
            ],
            icons: {
                top: './static/image/graytop.png',
                topChecked: './static/image/redtop.png',
                bottom: './static/image/graybottom.png',
                bottomChecked: './static/image/redbottom.png'
            },
            checkedFilter: 0, // 选中的第几个搜索条件 区分再次点击
            filter: this.GLOBAL.getStorage('filter') ? this.GLOBAL.getStorage('filter') : '', // order查询的筛选条件
            catId: this.$route.query.cat_id || 0, // 分类条件
            brand: this.$route.query.brand, // 品牌id
            hot: this.$route.query.hot || 0, // 热门
            keywords: this.$route.query.keywords || 0, // 搜索条件
            list: [] // 指定分类的商品数据
        }
    },
    mounted () {
        if (this.GLOBAL.getStorage('filter')) {
            // 从本地取出筛选状态
            let arr = this.GLOBAL.getStorage('filter').split(' ')
            this.label.forEach((v, k) => {
                if (v.condition === arr[0]) {
                    this.checkedFilter = k // 第几个筛选条件选中
                    v.checked = true
                    arr[1] === 'asc' ? v.top = true : v.top = false
                    arr[1] === 'desc' ? v.bottom = true : v.bottom = false
                } else {
                    v.checked = false
                    v.top = false
                    v.bottom = false
                }
            })
        } else {
            // 否则选中默认的
            this.label[0].checked = true
        }
        this.goodsList()
    },
    methods: {
        // 筛选条件点击
        clickFilter (index) {
            // 如果已经是选中的 再次点击切换倒序升序切换 且不是综合的筛选
            if (this.checkedFilter === index && index !== 0) {
                this.label[index].top = !this.label[index].top
                this.label[index].bottom = !this.label[index].bottom
            } else {
                // 否则切换对应的筛选条件
                this.label.forEach((v, k) => {
                    if (k === index) {
                        this.checkedFilter = index
                        v.checked = true
                        // 此处默认销量降序 价格升序
                        // 销量 k = 1 价格 k = 2
                        if (k === 2) {
                            v.top = true
                        } else {
                            v.bottom = true
                        }
                    } else {
                        v.checked = false
                        v.top = false
                        v.bottom = false
                    }
                })
            }
            // 拼接搜索条件
            let str = ''
            if (this.label[index].condition) str = this.label[index].top ? ' asc' : ' desc'
            this.filter = this.label[index].condition + str
            this.GLOBAL.setStorage('filter', this.filter)
            this.page = 1 // 筛选条件重新第一页开始
            this.goodsList()
        },
        // 获取商品数据
        goodsList () {
            this.$api.goodsList(this.conditions(), res => {
                const _list = res.data.list
                this.list = [..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
                this.page++
            })
        },
        // 加载更多
        loadMore () {
            this.$api.goodsList(this.conditions(), res => {
                const _list = res.data.list
                this.list = [...this.list, ..._list]
                if (_list.length < this.pageSize) {
                    /* 所有数据加载完毕 */
                    this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.loadedDone')
                }
                /* 单次请求数据完毕 */
                this.$refs.infinitescrollDemo.$emit('ydui.infinitescroll.finishLoad')
                this.page++
            })
        },
        // 统一返回筛选条件 查询条件 分页
        conditions () {
            let where = {}
            if (this.catId) {
                where = {cat_id: this.catId}
            } else if (this.hot) {
                where = {hot: this.hot}
            } else if (this.keywords) {
                where = {search_name: this.keywords}
            } else if (this.brand) {
                where = {brand_id: this.brand}
            }
            let data = {
                page: this.page,
                limit: this.pageSize,
                where: JSON.stringify(where)
            }
            if (this.filter) data['order'] = this.filter
            return data
        }
    },
    // 路由离开 如果不是进入商品详情页则清除筛选状态
    beforeRouteLeave (to, from, next) {
        if (to.path !== '/goodsdetail') {
            this.GLOBAL.removeStorage('filter')
        }
        next()
    }
}
</script>

<style>
    .goodslist  .itemgoodslist{
        margin-top: .8rem;
    }
    .goodslist .itembox{
        overflow: hidden;
        zoom: 1;
        background: #fff;
        border-bottom: solid 1px #f5f5f5;
        position: fixed;
        top: 1rem;
        width: 100%;
        z-index: 99999;
        padding: .1rem 0;
        max-width: 750px;
    }
    .goodslist .itembox ul li{
        width: 33.3%;
        float: left;
        height: .6rem;
        line-height: .6rem;
        position: relative;
    }
    .goodslist .itembox ul li img{
        /*vertical-align: middle;*/
    }
    .goodslist .itembox .sort-img{
        display: inline-block;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        margin-left: .05rem;
    }
    .goodslist .itembox .sort-img img{
        display: block;
        width: .2rem;
    }
    .goodslist .itembox .sort-img img:nth-child(2n){
        margin-top: .05rem;
    }
    .active {
        color: #FF3B44;
    }
</style>
