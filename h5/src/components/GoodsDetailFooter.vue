<template>
    <div class="goodsdetailfooter">
        <div class="goodsdetailfooter-left">
            <router-link tag="div" class="customservice" to="">
                <img src="../../static/image/customservice.png"/>
                <p>客服</p>
            </router-link>
            <div class="goods-cart" @click="collection" v-show="!is_fav">
                <img src="../../static/image/star.png"/>
                <p>收藏</p>
            </div>
            <div class="goods-cart" @click="collection" v-show="is_fav">
                <img src="../../static/image/redstar.png"/>
                <p>取消收藏</p>
            </div>
            <router-link tag="div" class="star" to="/cart">
                <img src="../../static/image/goods-cart.png"/>
                <p>购物车</p>
            </router-link>
        </div>
        <div class="goodsdetailfooter-right">
            <div class="addtocart">
                <yd-button type="danger" @click.native="addCart">加入购物车</yd-button>
            </div>
            <div class="buynow">
                <yd-button type="danger" @click.native="buyNow">立即购买</yd-button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        is_fav: {
            type: Boolean,
            default () {
                return false
            }
        }
    },
    methods: {
        addCart () {
            this.$emit('addCart')
        },
        buyNow () {
            this.$emit('buyNow')
        },
        collection () {
            this.$emit('collection')
        }
    }
}
</script>

<style>
</style>
