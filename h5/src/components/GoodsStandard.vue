<template>
    <div class="goodsstandard" v-if="Object.keys(spes).length">
        <div class="standard-content" v-for="(item, index) in spes" :key="index">
            <div class="standard-content-header">
               <p>{{ index }}</p>
            </div>
            <div>
                <div :class="sku | setDefaultActive" v-for="(sku, key) in item" :key="key" @click="change(index, key)">{{ sku.name }}</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        spes: {
            type: [Array, Object, Boolean],
            default () {
                return []
            }
        }
    },
    filters: {
        // 设置 默认及选中 的样式
        setDefaultActive (key) {
            if (key.hasOwnProperty('is_default')) {
                return 'red'
            } else {
                if (!key.product_id) {
                    return 'gray'
                } else {
                    return ''
                }
            }
        }
    },
    methods: {
        // 切换商品规格
        change (index, key) {
            if (this.spes[index][key].hasOwnProperty('product_id') && this.spes[index][key].product_id) {
                this.$emit('changeSpes', this.spes[index][key].product_id)
            }
        }
    }
}
</script>

<style>
    .standard-content .gray{
        border: 1px dashed #f3f3f3 !important;
        color: #eaeaea !important;
        background-color: #fff !important;
    }
</style>
