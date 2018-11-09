<template>
    <div>
        公开发动机更换发动机可国防科技多个
    </div>
</template>

<script>
export default {
    name: "author",
    data () {
        return {
            code: this.$route.query.code,
            type: this.$route.query.type,
            state: this.$route.query.state
        }
    },
    mounted () {
        this.userTrustLogin()
    },
    methods: {
        userTrustLogin () {
            this.$api.trustLogin({
                code: this.code,
                type: this.type,
                state: this.state
            }, res => {
                if (res.status) {
                    if (res.data.is_new) {
                        this.$router.push({path: '/authbind', query: {code: res.data}})
                    }
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
