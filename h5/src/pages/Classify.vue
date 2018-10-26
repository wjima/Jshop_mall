<template>
    <div id="classify">
        <!--<search></search>-->
        <scrolltab :goodsType="typeList"></scrolltab>
    </div>
</template>

<script>
import search from '../components/Search.vue'
import scrolltab from '../components/ScrollTab.vue'
export default {
    components: {
        search, scrolltab
    },
    data () {
        return {
            typeList: []
        }
    },
    mounted () {
        this.goodsTypeList()
    },
    methods: {
        goodsTypeList () {
            this.$api.categories({}, res => {
                if (res.status) {
                    this.typeList = res.data
                }
            })
        }
    }
}
</script>

<style>
</style>
