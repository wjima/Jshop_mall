<template>
    <div class="goodsstandard" v-if="spes.length">
        <div class="standard-content" v-for="(item, index) in spes" :key="index">
            <div class="standard-content-header">
               <p>{{ item.sku_name }}</p>
            </div>
            <div :class="sku.is_defalut | setDefaultActive" v-for="(sku, key) in item.sku_value" :key="key" @click="change(item.sku_name, sku.name)">{{ sku.name }}</div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['spes', 'products'],
    filters: {
        // 设置 默认及选中 的样式
        setDefaultActive (index) {
            if (index === '1') {
                return 'red'
            } else {
                return ''
            }
        }
    },
    methods: {
        change (index, val) {
            this.$emit('changeSpes', {index, val})
        }
    }
}
</script>

<style>
</style>
