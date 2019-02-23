<template>
    <div class="scrolltab" style="height: 100%;">
        <div v-if="goodsType" class="goods-type" >
            <div class="goods-panel" v-for="(item, key) in goodsType">
                <div :class="[item.selected ? newhead : head]" @click="change(key)">
                    <p :class="[item.selected ? newtitle : title]">{{item.name}}</p>
                </div>
                <!--<div :class="head" v-else  @click="change(key,item.child)">-->
                    <!--<p :class="title">{{item.name}}</p>-->
                <!--</div>-->
            </div>
        </div>
        <div class="goods-detail">
            <div v-if="childItem.length">
                <span class="scrolltab-li" v-for="(v, k) in childItem" :key="k" @click="clickHandler(v.id)">
                    <img :src="v.image_url">
                    <p style="font-size: 120%">{{v.name}}</p>
                </span>
            </div>
            <div class="nothing" v-else>
                <!--<div class="nothing-c">-->
                    <img class="nothing-img" src="../../static/image/indent.png" alt="">
                    <p>这里什么也没有~</p>
                <!--</div>-->

            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        goodsType: {
            type: [Array, Object],
            default () {
                return []
            }
        },
    },
    data () {
        return {
            key: 0,
            mark: 0,
            head: 'panel-head',
            newhead: 'panel-head-new',
            title: 'title-context',
            newtitle: 'panel-title-new',
            // childrenItem: [{name: '没有内容', img_url: ''}]
        }
    },
    computed: {
        childItem () {
            let child = []
            this.goodsType.forEach(item => {
                if (item.selected) {
                    child = item.hasOwnProperty('child') ? item.child : []
                }
            })
            return child
        }
    },
    methods: {
        clickHandler (id) {
            this.$emit('clickHandler', {cat_id: id})
        },
        // 向父组件传递 点击对应的顶级分类更改选中
        change (key) {
            this.$emit('changeSelected', key)
        }
    },
    watch: {

    }
}
</script>

<style>
    .scrolltab {
        height: 100%;
        width: 100%;
        overflow-y: hidden;
        overflow-x: hidden;
        position: relative;
    }
    .scrolltab::-webkit-scrollbar {
        display: none;
    }
    .goods-type {
        height: 100%;
        width: 19%;
        overflow-y: scroll;
    }
    .goods-detail {
        width: 81%;
        height: 100%;
        position: absolute;
        left: 19%;
        top: 0;
        overflow-y:auto;
    }
    .panel-head-new {
        width: 100%;
        padding-left:calc(2.5% - 2px);
        height: 50px;
        /*background-color: #ff3b44;*/

        border-left: 2px solid #ff3b44;
        /*margin-top: 2px;*/

    }
    .panel-title-new {
        font-size: 100%;
        width: 100%;
        height: 50px;
        line-height: 50px;
        /*padding-top: 20px;*/
        background-color:#fff ;
        color: #ff3b44;
    }
    .panel-head {
        width: 100%;
        padding-left:2.5%;
        height: 50px;
        background-color: #f7f7f7;
        border-left-color: #0e0e0e;
        /*margin-top: 2px;*/
    }
    .panel-head .title-context {
        font-size: 100%;
        width: 100%;
        height: 50px;
        line-height: 50px;
        background-color:#f7f7f7 ;
        color: #666666;
    }
    .nothing {
        margin-top: 3rem;
    }
    .nothing-img {
        width: 1.5rem;
        height: 1.5rem;
        margin-bottom: .5rem;
    }
    .nothing p {
        color: #999;
    }
</style>