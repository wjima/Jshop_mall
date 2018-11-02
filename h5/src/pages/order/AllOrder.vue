<template>
    <ordertab :tab="tab"></ordertab>
</template>

<script>
import ordertab from '../../components/OrderTab.vue'
export default {
    data () {
        return {
            tab: this.$route.query.tab || 0 // 0 默认全部
        }
    },
    components: {
        ordertab
    }
}
</script>

<style>
</style>
