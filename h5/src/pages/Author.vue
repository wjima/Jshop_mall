<template>
    <div class="author-body">
        <div class="author-c">
            <img src="../../static/image/loading.gif" class="loading-img" />
            <p>信息加载中,请稍后...</p>
        </div>
    </div>
</template>

<script>
export default {
  name: 'author',
  data() {
    return {
      code: '',
      state: ''
    }
  },
  mounted() {
    // 获取url上的参数
    this.code = this.getUrlParam('code')
    this.state = this.getUrlParam('state')
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
          arr = arrPara[i].split('=')
          if (arr != null && arr[0] == paraName) {
            if (arr[1].indexOf('#')) {
              let str
              str = arr[1].split('#')
              return str[0]
            }
            return arr[1]
          }
        }
        return ''
      } else {
        return ''
      }
    },
    userTrustLogin() {
      let data = {
        scope: 1,
        code: this.code,
        state: this.state,
        invitecode: this.GLOBAL.getStorage('invitecode') || ''
      }
      this.$api.getOpenId(data, res => {
        if (res.status) {
          if (res.data.token) {
            this.GLOBAL.setStorage('user_token', res.data.token)
            this.$router.replace({ path: '/user' })
          } else if (res.data.user_wx_id) {
            this.$router.replace({
              path: '/authbind',
              query: { user_wx_id: res.data.user_wx_id }
            })
          }
        }
      })
    }
  }
}
</script>

<style>
.author-body {
  position: relative;
  height: 100%;
}
.author-c {
  position: absolute;
  top: 50%;
  left: 50%;
  color: #666;
  transform: translate(-50%, -50%);
}
</style>
