<template>
    <div class="userheader">
        <!--<div class="topcolor"></div>-->
        <div class="userimg">
            <div class="headerimg">
                <img :src="avatar"/>
                <p>{{ name }}</p>
            </div>
            <div class="user-setting">
                <i class="iconfont icon-shezhi" @click="setting"></i>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['avatar', 'name'],
    methods: {
        setting () {
            this.$router.push({path: '/setting'})
        }
    }
}
</script>

<style>
</style>
