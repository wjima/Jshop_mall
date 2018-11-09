<template>
    <div class="indexlisthot" v-if="hotGoods.length">
        <div class="indexlistheader">
            <img src="../../static/image/hot.png"/>
            <h3>店铺热卖</h3>
        </div>
        <yd-list theme="2" slot="list">
            <yd-list-item v-for="(item, key) in hotGoods" :key="key" type="link" :href="{path:'goodsdetail',query:{goods_id:item.id}}">
                <img class="goods-imgs" slot="img" v-lazy="item.image_url">
                <span slot="title">{{item.name}}</span>
                <yd-list-other slot="other">
                    <div>
                        <span class='demo-list-price'><em>¥</em>{{item.price}}</span>
                        <!--<span class='demo-list-del-price'>¥{{item.w_price}}</span>-->
                    </div>
                    <div><button class="indexlist-cart"><img src="../../static/image/cart.png"/></button></div>
                </yd-list-other>
            </yd-list-item>
        </yd-list>
    </div>
</template>

<script type="text/babel">
export default {
    data () {
        return {
            hotGoods: []
        }
    },
    created () {
        let where = {hot: 1}
        this.$api.goodsList({where: JSON.stringify(where)}, res => {
            this.hotGoods = res.data.list
        })
    }
}
</script>
