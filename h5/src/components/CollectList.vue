<template>
    <div class="collectlist" v-if="list.length">
        <yd-list theme="4">
            <yd-list-item v-for="(item, index) in list" :key="index" type="link" :href="{path: '/goodsDetail', query: {goods_id: item.goods_id}}" v-if="item.goods">
                <img slot="img" :src="item.goods.image_url">
                <h3 class="goodsname" slot="title">{{item.goods_name}}</h3>
                <yd-list-other slot="other">
                    <div>
                        <span class="demo-list-price"><em>¥</em>{{item.goods.price}}</span>
                    </div>
                </yd-list-other>
                <yd-list-other slot="other">
                    <div>
                        <span class="time">{{ item.ctime }}</span>
                    </div>
                </yd-list-other>
                <img class="right-img" slot="other" src="../../static/image/right.png"/>
            </yd-list-item>
        </yd-list>
    </div>
    <div class="collectlist" v-else>
        没有更多数据了
    </div>
</template>

<script type="text/babel">
export default {
    props: {
        list: {
            type: [Array, Object],
            default () {
                return []
            }
        }
    }
}
</script>

<style type="text/css">
</style>
