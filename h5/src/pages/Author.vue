<template>
    <div class="author-body">
        <div class="author-c">
            <img src="../../static/image/loading.gif" class="loading-img"/>
            <p>信息加载中,请稍后...</p>
        </div>
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

<style>
    .author-body{
        position: relative;
        height: 100%;
    }
    .author-c{
        position: absolute;
        top: 50%;
        left: 50%;
        color: #666;
        transform: translate(-50%, -50%);
    }
</style>

