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
            code: '',
            type: this.$route.query.type,
            state: ''
        }
    },
    mounted () {
        // 获取url上的参数
        this.code = this.getUrlParam('code')
        this.state = this.getUrlParam('state')
        console.log(this.state)
        console.log(this.code)
        console.log(this.type)
        this.userTrustLogin()
    },
    methods: {
        getUrlParam(paraName) {
            let url = document.location.toString()
            let arrObj = url.split('?')
            if (arrObj.length > 1) {
                let arrPara = arrObj[1].split('&')
                let arr
                for (let i = 0; i < arrPara.length; i++) {
                    arr = arrPara[i].split("=")
                    if (arr != null && arr[0] == paraName) {
                        if ((arr[1].indexOf('#'))) {
                            let str
                            str = arr[1].split('#')
                            return str[0]
                        }
                        return arr[1]
                    }
                }
                return ''
            }
            else {
                return ''
            }
        },
        userTrustLogin () {
            this.$api.trustLogin({
                code: this.code,
                type: this.type,
                state: this.state
            }, res => {
                if (res.status) {
                    if (res.data.is_new) {
                        this.$router.push({path: '/authbind', query: {code: res.data}})
                    } else if (res.data) {
                        this.$router.replace({path: '/user'})
                    }
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
