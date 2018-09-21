<template>
    <div class="searchpage">
        <div class="searchpage-header">
            <!--<yd-search v-model="search" placeholder="搜索商品" class="search"></yd-search>-->
            <div class="search-input">
                <form action="">
                    <i class="search-icon" ></i>
                    <input v-model="search" type="search" value="" placeholder="搜索商品" @keyup.enter="submitHandler"/>
                </form>
            </div>
            <button class="searchimg" @click="submitHandler"><img src="../../static/image/search.png"/></button>
        </div>
        <div class="searchpage-body">
            <div class="" v-for="(item, index) in history" :key="index" @click="buttonSearch(item)">
                {{ item }}
            </div>

        </div>
        <div class="clear-history">
            <yd-button size="large" type="hollow" v-if="is_clear" @click.native="remove">清除历史记录</yd-button>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            search: '',
            history: [], // 搜索历史本地存储
            is_clear: false
        }
    },
    mounted () {
        let localSearch = this.getlocalSearch()
        if (localSearch) {
            this.history = localSearch.split(',')
            this.is_clear = true
        }
    },
    methods: {
        // 提交搜索
        submitHandler () {
            if (!this.search && this.search === '') {
                return false
            } else {
                this.setLocalSearch(this.search)
                this.$router.push({path: '/goodslist', query: {keywords: this.search}})
            }
        },
        // 设置搜索缓存本地
        setLocalSearch (keywords) {
            let key = this.GLOBAL.getStorage('keywords')
            if (!key) {
                this.GLOBAL.setStorage('keywords', keywords)
            } else {
                if (key.indexOf(keywords) === -1) {
                    this.GLOBAL.setStorage('keywords', keywords + ',' + key)
                }
            }
        },
        // 获取本地缓存
        getlocalSearch () {
            return this.GLOBAL.getStorage('keywords')
        },
        // 点击搜索记录搜索
        buttonSearch (val) {
            this.search = val
            this.submitHandler()
        },
        // 清除搜索缓存
        remove () {
            this.is_clear = false
            this.GLOBAL.removeStorage('keywords')
            this.history = []
        }
    }
}
</script>
