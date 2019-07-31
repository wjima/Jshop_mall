<template>
    <div class="reg">
        <div class="reg-img">
            <img src="../../static/image/group14.png" />
        </div>
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">手机号：</span>
                <yd-input type="text" slot="right" ref="tel" required :show-success-icon="false" regex="mobile" v-model="mobile" placeholder="请输入手机号码"></yd-input>
                <yd-sendcode slot="right" storage-key="register" v-model="countDown" init-str="获取验证码" @click.native="sendCode" type="primary"></yd-sendcode>
            </yd-cell-item>
            <yd-cell-item>
                <span slot="left">验证码：</span>
                <yd-input slot="right" v-model="code" placeholder="请输入短信验证码"></yd-input>
            </yd-cell-item>
            <!--<yd-cell-item>-->
            <!--<span slot="left">密码：</span>-->
            <!--<yd-input slot="right" type="password" v-model="pwd" placeholder="请输入密码"></yd-input>-->
            <!--</yd-cell-item>-->
        </yd-cell-group>
        <yd-button size="large" type="danger" @click.native="reg">绑定</yd-button>
    </div>
</template>

<script>
export default {
  data() {
    return {
      invitecode: this.GLOBAL.getStorage('invitecode') || '', // 邀请码
      user_wx_id: this.$route.query.user_wx_id,
      mobile: '',
      // pwd: '',
      code: '', // 验证码
      countDown: false // 发送验证码倒计时 发送成功后修改为true倒计时启动
    }
  },
  created() {
    if (this.GLOBAL.getStorage('user_token')) {
      this.$dialog.toast({
        mes: '您已登录!',
        timeout: 1000,
        callback: () => {
          this.$router.go(-1)
        }
      })
    }
  },
  computed: {
    checkMobile() {
      let res = {}
      if (!this.mobile) {
        res.status = false
        res.msg = '请输入手机号'
      } else if (!/^1[345678]{1}\d{9}$/gi.test(this.mobile)) {
        res.status = false
        res.msg = '手机号格式不正确'
      } else {
        res.status = true
      }
      return res
    }
  },
  methods: {
    // 发送短信验证码
    sendCode() {
      if (!this.checkMobile.status) {
        this.$dialog.toast({ mes: this.checkMobile.msg, timeout: 1000 })
      } else {
        this.$dialog.loading.open('发送中...')
        setTimeout(() => {
          this.$dialog.loading.close()
          this.$api.sms(
            { mobile: this.mobile, code: 'reg', type: 'bind' },
            res => {
              if (res.status) {
                this.countDown = true
                this.$dialog.toast({
                  mes: res.msg,
                  icon: 'success',
                  timeout: 1000
                })
              }
            }
          )
        }, 1000)
      }
    },
    reg() {
      if (!this.checkMobile.status) {
        this.$dialog.toast({ mes: this.checkMobile.msg, timeout: 1000 })
      } else if (!this.code) {
        this.$dialog.toast({ mes: '请输入短信验证码', timeout: 1000 })
      } else {
        let data = {
          mobile: this.mobile,
          code: this.code,
          user_wx_id: this.user_wx_id
        }
        if (this.invitecode) {
          data.invitecode = this.invitecode
        }
        this.$api.smsLogin(data, res => {
          if (res.status) {
            this.GLOBAL.setStorage('user_token', res.data)
            this.$dialog.toast({
              mes: '绑定成功!',
              timeout: 1000,
              icon: 'none',
              callback: () => {
                this.$router.replace('/index')
              }
            })
          }
        })
      }
    }
  }
}
</script>

<style>
.reg .reg-img {
  margin: 0.5rem auto;
}
.reg .reg-img img {
  border-radius: 50%;
}
.reg .yd-btn-block {
  /*background-color: #ff43;*/
  height: 0.8rem;
  width: 90%;
  margin: 0.5rem auto;
}
</style>
