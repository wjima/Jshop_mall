<template>
    <div class="notice">
        <h2 class="notice-title">{{ notice.title }}</h2>
        <div class="notice-content">
            <p>{{ notice.content }}</p>
        </div>
        <div class="notice-footer">
            <span>{{ this.GLOBAL.timeToDate(notice.ctime) }}</span>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            notice: []
        }
    },
    created () {
        this.$api.noticeInfo({id: this.$route.query.notice_id}, res => {
            if (res.data) {
                this.notice = res.data
            } else {
                this.$dialog.alert({
                    mes: '该公告信息不存在',
                    callback: () => {
                        this.$router.go(-1)
                    }
                })
            }
        })
    }
}
</script>

<style>
</style>
