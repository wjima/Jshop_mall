<template>
    <div id="classify">
        <!--<search></search>-->
        <scrolltab
                v-if="cateStyle === 1"
                :goodsType="typeList"
        ></scrolltab>
        <newscrolltab
                v-else
                :goodsType="typeList"
                @changeSelected="changeSelected"
        ></newscrolltab>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
import search from '../components/Search'
import scrolltab from '../components/ScrollTab'
import newscrolltab from '../components/Newscrolltab'
export default {
    components: {
        search, scrolltab, newscrolltab
    },
    data () {
        return {
            typeList: []
        }
    },
    computed: {
        ...mapGetters({
            cateStyle: 'shopCateStyle'
        })
    },
    mounted () {
        this.goodsTypeList()
    },
    methods: {
        goodsTypeList () {
            this.$api.categories({}, res => {
                if (res.status) {
                    this.typeList = res.data
                    this.typeList.forEach((item, key) => {
                        key === 0 ? this.$set(item, 'selected', true) : this.$set(item, 'selected', false)
                    })
                }
            })
        },
        // 更改选中状态
        changeSelected (key) {
            this.typeList.forEach (item => {
                if (item.selected) {
                    item.selected = false
                }
            })
            this.typeList[key].selected = true
        }
    }
}
</script>

<style>
</style>
