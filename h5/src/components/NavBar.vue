<template>
	<yd-navbar slot="navbar" :title="title">
        <div slot="left" @click="back">
            <yd-navbar-back-icon></yd-navbar-back-icon>
        </div>
        <!--<router-link to="/user" slot="right">
            <yd-icon name="ucenter-outline" size="25px" color="#777"></yd-icon>
        </router-link>-->
    </yd-navbar>
</template>

<script>
export default {
    props: ['title'],
    methods: {
        back () {
            this.$router.go(-1)
        }
    }
}
</script>

<style>
</style>
