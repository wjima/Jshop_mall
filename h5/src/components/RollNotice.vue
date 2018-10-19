<template>
    <div class="roolnotice">
        <yd-rollnotice autoplay="2000" v-if="noticeList.length">
            <yd-rollnotice-item v-for="(item, index) in noticeList" :key="index" @click.native="showDetail(item.id)">
                {{ item.title }}
            </yd-rollnotice-item>
        </yd-rollnotice>
    </div>
</template>

<script type="text/babel">
export default {
    data () {
        return {
            noticeList: []
        }
    },
    created () {
        this.$api.notice({}, res => {
            if (res.status) {
                this.noticeList = res.data
            }
        })
    },
    methods: {
        showDetail (id) {
            this.$router.push({path: '/notice', query: {notice_id: id}})
        }
    }
}
</script>

<style>
    .roolnotice {
        background-color: #FFFFFF;
    }
    .cashPool span {
        color: #f00;
    }
</style>
