<template>
    <div class="goodsdetail">
        <div class="goodsdetail-back">
            <i class="iconfont icon-zuo" @click="goBack"></i>
        </div>
        <slider
            :imgList="goodsData.album"
        ></slider>
        <activitytitle
            :product="productSpes"
            :brief="goodsData.brief"
            :buyCount="goodsData.buy_count"
            :unit="goodsData.unit"
            :stock="productSpes.stock"
            :etime="goodsData.etime"
        ></activitytitle>
        <goodsservice
            :promotion="checkPromotion"
        ></goodsservice>
		<!-- <div class="group-slider">
			<div class="gs-title">
				<div class="gs-title-l">这些人刚刚购买成功</div>
				<div class="gs-title-r">查看更多</div>
			</div>
			<div class="gs-body" >
				<div style="height: 110px;min-height: 110px;">
					<yd-slider autoplay="3000" direction="vertical">
						<yd-slider-item>
							<div class="gs-body-item">
								<div class="gs-body-item-l">
									<img class="user-img" src="../../../../thinkphp/logo.png" alt="">
									<div class="user-name">debug</div>
								</div>
								<div class="gs-body-item-r">
									<div class="gsbir-l">
										<div class="gsbir-l-t">还差<span>1人</span>拼成</div>
										<div class="gsbir-l-b">剩余23:55:24</div>
									</div>
									<yd-button class="gsbir-btn">去拼单</yd-button>
								</div>
							</div>
							<div class="gs-body-item">
								<div class="gs-body-item-l">
									<img class="user-img" src="../../../../thinkphp/logo.png" alt="">
									<div class="user-name">debug</div>
								</div>
								<div class="gs-body-item-r">
									<div class="gsbir-l">
										<div class="gsbir-l-t">还差<span>1人</span>拼成</div>
										<div class="gsbir-l-b">剩余23:55:24</div>
									</div>
									<yd-button class="gsbir-btn">去拼单</yd-button>
								</div>
							</div>
						</yd-slider-item>
						<yd-slider-item>
							<div class="gs-body-item">
								<div class="gs-body-item-l">
									<img class="user-img" src="../../../../thinkphp/logo.png" alt="">
									<div class="user-name">debug</div>
								</div>
								<div class="gs-body-item-r">
									<div class="gsbir-l">
										<div class="gsbir-l-t">还差<span>1人</span>拼成</div>
										<div class="gsbir-l-b">剩余23:55:24</div>
									</div>
									<yd-button class="gsbir-btn">去拼单</yd-button>
								</div>
							</div>
							<div class="gs-body-item">
								<div class="gs-body-item-l">
									<img class="user-img" src="../../../../thinkphp/logo.png" alt="">
									<div class="user-name">debug</div>
								</div>
								<div class="gs-body-item-r">
									<div class="gsbir-l">
										<div class="gsbir-l-t">还差<span>1人</span>拼成</div>
										<div class="gsbir-l-b">剩余23:55:24</div>
									</div>
									<yd-button class="gsbir-btn">去拼单</yd-button>
								</div>
							</div>
						</yd-slider-item>
						<yd-slider-item>
							<div class="gs-body-item">
								<div class="gs-body-item-l">
									<img class="user-img" src="../../../../thinkphp/logo.png" alt="">
									<div class="user-name">debug</div>
								</div>
								<div class="gs-body-item-r">
									<div class="gsbir-l">
										<div class="gsbir-l-t">还差<span>1人</span>拼成</div>
										<div class="gsbir-l-b">剩余23:55:24</div>
									</div>
									<yd-button class="gsbir-btn">去拼单</yd-button>
								</div>
							</div>
							<div class="gs-body-item">
								<div class="gs-body-item-l">
									<img class="user-img" src="../../../../thinkphp/logo.png" alt="">
									<div class="user-name">debug</div>
								</div>
								<div class="gs-body-item-r">
									<div class="gsbir-l">
										<div class="gsbir-l-t">还差<span>1人</span>拼成</div>
										<div class="gsbir-l-b">剩余23:55:24</div>
									</div>
									<yd-button class="gsbir-btn">去拼单</yd-button>
								</div>
							</div>
						</yd-slider-item>
					</yd-slider>
				</div>
			</div>
		</div> -->
        <goodsstandard
            :spes="productSpes.default_spes_desc"
            @changeSpes="changeSpes"
        ></goodsstandard>
        <goodsnum :stock="productSpes.stock" @num="goodsNum"></goodsnum>
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
                        <yd-lightbox class="comment-imgs">
                            <yd-lightbox-img class="comment-img" v-for="(img, key) in item.images_url" :key="key" :src="img"></yd-lightbox-img>
                            <yd-lightbox-txt>
                                <h1 slot="top">
                                    <div class="">
                                        <p class="user-name">{{ item.user.nickname }}</p>
                                        <yd-rate slot="left" v-model="item.score" :readonly="true" size=".2rem"></yd-rate>
                                    </div>
                                </h1>
                                <div slot="content">
                                    <p>{{ item.content }}</p>
                                </div>
                                <div slot="bottom">
                                    <p>{{ item.ctime }}</p>
                                </div>
                            </yd-lightbox-txt>
                        </yd-lightbox>
                    </li>
                    <li style="text-align: center">
                    <yd-button size="small" type="hollow" color="#F00" shape="circle" v-if="load" @click.native="loadMore">加载更多评论</yd-button>
                    <yd-button size="small" type="hollow" color="#AAA" shape="circle" v-else>没有更多评论了</yd-button>
                    </li>
                </ul>
            </yd-tab-panel>
        </yd-tab>
        <activityfooter
            :is_fav="is_fav"
            :label="labelName"
            :cart_nums="cart_nums"
            @collection="collection"
            @buyNow="buyNow"
        ></activityfooter>
    </div>
</template>

<script>
import slider from '../../components/Slider.vue'
import activitytitle from '../../components/activity/ActivityTitle.vue'
import goodsservice from '../../components/GoodsService.vue'
import goodsstandard from '../../components/GoodsStandard.vue'
import goodsnum from '../../components/GoodsNum.vue'
import activityfooter from '../../components/activity/ActivityFooter.vue'
export default {
    data () {
        return {
            labelName: '立即团购',
            page: 1, // 商品评论分页参数
            pageSize: 5, // 商品评论分页数量
            goodsId: this.$route.query.goods_id ? this.$route.query.goods_id : '', // vueRouter传过来的商品id
            goodsData: [], // 商品数据
            productSpes: [], // 商品全部规格
            promotion: [], // 促销信息
            stock: '', // 选中规格的库存数量
            params: [], // 商品参数信息
            comment: [], // 商品的评论
            load: true, // 是否显示更多
            is_fav: false, // 是否收藏
            cart_nums: 0, // 购物车数量
            num: 1, // 购买的商品数量 默认为1
            rate1: 5, // 好评
            rate2: 3, // 中评
            rate3: 1 // 差评

        }
    },
    components: {
        slider, activitytitle, goodsservice, goodsstandard, goodsnum, activityfooter
    },
    created () {
        this.activityDetail()
        this.goodsParams()
        this.goodsComment()
        this.getCartNums()
    },
    computed: {
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
        activityDetail () {
            this.$api.activityDetail({id: this.goodsId, token: this.GLOBAL.getStorage('user_token')}, res => {
                if (res.status && res.data.length !== 0) {
                    this.goodsData = res.data
                    // 获取用户是否收藏
                    if (res.data.isfav === 'false') {
                        this.is_fav = false
                    } else {
                        this.is_fav = true
                    }
                    // 商品规格信息
                    this.productSpes = res.data.product
                    this.promotion = res.data.product.promotion_list
                    // 添加用户浏览足迹
                    if (this.GLOBAL.getStorage('user_token')) {
                        this.goodsBrowsing()
                    }
                    // 微信分享
                    this.weixinConfig()
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
                    let commentCount = res.data.count
                    for (let k in _list) {
                        _list[k].ctime = this.GLOBAL.timeToDate(_list[k].ctime)
                    }
                    if (_list.length >= commentCount) {
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
                    let commentCount = res.data.count
                    for (let k in _list) {
                        _list[k].ctime = this.GLOBAL.timeToDate(_list[k].ctime)
                    }
                    this.comment = [...this.comment, ..._list]
                    if (this.comment.length >= commentCount) {
                        this.load = false
                    }
                }
            })
        },
        // 获取微信分享配置参数
        weixinConfig () {
            let isWeiXinBrowser = this.GLOBAL.isWeiXinBrowser()
            if (isWeiXinBrowser) {
                this.$api.weixinShare(this.goodsData.name, this.goodsData.image_url, this.goodsData.brief)
            }
        },
        // 获取购物车数量
        getCartNums () {
            let userToken = this.GLOBAL.getStorage('user_token')
            if (userToken) {
                this.$api.getCartNum({token: userToken}, res => {
                    if (res.status) {
                        this.cart_nums = res.data
                    }
                })
            }
        },
        // 更改默认货品
        changeSpes (id) {
            this.$api.getProductInfo({id: id}, res => {
                if (res.status) {
                    this.productSpes = res.data
                }
            })
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
            this.$api.addCart({product_id: this.productSpes.id, nums: this.num}, res => {
                if (res.status) {
                    this.getCartNum() // 重新请求接口获取购物车数量
                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'success'})
                }
            })
        },
        // 立即购买
        buyNow () {
            this.$api.addCart({product_id: this.productSpes.id, nums: this.num, type: 2}, res => {
                if (res.status) {
                    let cartIds = res.data
                    this.$router.push({path: '/firmorder', query: {cartIds}})
                }
            })
        },
        goBack () {
            if (window.history.length <= 1) {
                this.$router.push({path: '/'})
                return false
            } else {
                this.$router.go(-1)
            }
            // 上面都没执行就说明卡在当前页不是最后一条， histroy记录数量大于1，又没有回退记录，只能返回首页，
            // 如果上面都执行了 页面都跳走了，这个也就不用管了
            // setTimeout(() => {
            //     this.$router.push({path:'/'})
            // },500)
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
    .goodsdetail-back {
        position: absolute;
        top: .3rem;
        left: .3rem;
        color: #fff;
        z-index: 999;
        width: .7rem;
        height: .7rem;
        line-height: .7rem;
        text-align: center;
        background-color: rgba(0,0,0,.3);
        border-radius: 50%;
    }
    .goodsdetail-back i {
        font-size: .3rem;
    }
    .goodsdetail .comment-imgs{
        width: 100%;
        overflow: hidden;
        margin-top: .15rem;
    }
    .goodsdetail .comment-imgs .comment-img{
        width: 25%;
        height: 2rem;
        margin-right: 3%;
        background-color: #fff;
        float: left;
        position: relative;
    }
    .goodsdetail .comment-imgs .comment-img:nth-child(3n){
        margin-right: 0;
    }
    .goodsdetail .comment-imgs .comment-img img{
        position: absolute;
        top: 50%;
        left: 50%;
        max-height: 100%;
        max-width: 100%;
        transform: translate(-50%, -50%);
    }
	.group-slider{
		margin-bottom: .15rem ;
	}
	.gs-title{
		overflow: hidden;
		padding: .2rem;
		background-color: #fff;
		margin-bottom: 1px;
		font-size: .28rem;
		color: #666;
	}
	.gs-title-l{
		float: left;
		
	}
	.gs-title-r{
		float: right;
	}
	.gs-body{
		background-color: #fff;
	}
	.gs-body .yd-slider-pagination-vertical{
		display: none;
	}
	.gs-body .yd-slider-item{
		min-height: 100px;
	}
	.gs-body-item{
		overflow: hidden;
		padding: .1rem .2rem;
		height: 55px;
		box-sizing: border-box;
		border-bottom: 1px solid #f0f0f0;
	}
	.gs-body-item-l{
		float: left;
		position: relative;
		height: 100%;
	}
	.gs-body-item-l .user-img{
		width: .5rem;
		height: .5rem;
		border-radius: 50%;
	}
	.gs-body-item-l .user-name{
		font-size: .28rem;
		color: #666;
		position: relative;
		top: 50%;
		transform: translateY(-50%);
		left: .6rem;
	}
	.gs-body-item-r{
		float: right;
		overflow: hidden;
		position: relative;
		height: 100%;
	}
	.gsbir-l{
		float: left;
		position: relative;
		top: 50%;
		transform: translateY(-50%);
		margin-right: .2rem;
	}
	.gsbir-l-t{
		color: #666;
		font-size: .26rem;
	}
	.gsbir-l-t span{
		color: #FF3B44;
	}
	.gsbir-l-b{
		float: right;
		font-size: .22rem;
	}
	.gsbir-btn{
		position: relative;
		top: 50%;
		transform: translateY(-50%);
		background-color: #FF3B44 !important;
	}
</style>
