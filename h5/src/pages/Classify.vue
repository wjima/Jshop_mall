<template>
    <div id="classify">
        <!--<search></search>-->

		<!--分类一级大图-->
		<classbig
				v-if="shopCateStyle === 1"
				:goodsType="typeList"
				@clickHandler="clickHandler"
		></classbig>

		<!--分类一级小图-->
		<classsmall
				v-else-if="shopCateStyle === 2"
				:goodsType="typeList"
				@clickHandler="clickHandler"
		></classsmall>

		<!--正常分类二级-->
		<scrolltab
				v-if="shopCateStyle === 3 && shopCateType === 1"
				:goodsType="typeList"
				@clickHandler="clickHandler"
		></scrolltab>
		<newscrolltab
				v-else-if="shopCateStyle === 3 && shopCateType === 2"
				:goodsType="typeList"
				@changeSelected="changeSelected"
				@clickHandler="clickHandler"
		></newscrolltab>

    </div>
</template>

<script>
import {mapGetters} from 'vuex'
import search from '../components/Search'
import scrolltab from '../components/ScrollTab'
import newscrolltab from '../components/Newscrolltab'
import classbig from '../components/ClassifyBig'
import classsmall from  '../components/ClassifySmall'
export default {
    components: {
        search, scrolltab, newscrolltab, classbig, classsmall
    },
    data () {
        return {
            typeList: []
        }
    },
    computed: {
        ...mapGetters([
            'shopCateStyle',
			'shopCateType'
        ])
    },
    mounted () {
        this.goodsTypeList()
    },
    methods: {
    	// 获取商品分类
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
        },
		// 分类点击 这里统一作跳转
		clickHandler (val) {
			this.$router.push({path: '/goodslist', query: val})
		}
    }
}
</script>

<style>

</style>
