<template>
    <div ref="scrolltab" class="scrolltab" style="overflow-y: auto; position: relative;">
        <yd-scrolltab v-if="goodsType">
            <yd-scrolltab-panel :label="item.name" icon="demo-icons-category1" v-for="(item, key) in goodsType" :key="key">
                <div class="scrolltabbody">
                    <ul class="scrolltab-ul" v-if="item.child">
                       <li class="scrolltab-li" v-for="(childItem, key) in item.child" :key="key" @click="showList(childItem.id)">
                           <!--<img src="../../static/image/loading.gif" class="loading-img"/>-->
                           <img v-lazy="childItem.image_url">
                           <p>{{ childItem.name }}</p>
                       </li>
                    </ul>
                </div>
            </yd-scrolltab-panel>
            <!-- ... -->
        </yd-scrolltab>
    </div>
</template>

<script type="text/babel">
export default {
    props: {
        goodsType: {
            type: [Array, Object],
            default () {
                return []
            }
        }
    },
    methods: {
        showList (catId) {
            this.$router.push({path: '/goodslist', query: {cat_id: catId}})
        }
    }
}
</script>

<style>
    .scrolltab{
        height: 100%;
    }
</style>
