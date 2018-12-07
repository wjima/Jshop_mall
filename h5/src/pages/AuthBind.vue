<template>
    <div class="reg">
        <div class="reg-img">
            <img src="../../static/image/group14.png"/>
        </div>
        <yd-cell-group>
            <yd-cell-item>
                <span slot="left">手机号：</span>
                <yd-input type="text" slot="right" ref="tel" required :show-success-icon="false" regex="mobile" v-model="mobile" placeholder="请输入手机号码"></yd-input>
                <yd-sendcode slot="right"
                             storage-key="register"
                             v-model="countDown"
                             init-str="获取验证码"
                             @click.native="sendCode"
                             type="primary"
                ></yd-sendcode>
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
    data () {
        return {
            invitecode: this.GLOBAL.getStorage('invitecode') || '', // 邀请码
            mobile: '',
            // pwd: '',
            code: '', // 验证码
            countDown: false // 发送验证码倒计时 发送成功后修改为true倒计时启动
        }
    },
    created () {
        if (this.GLOBAL.getStorage('user_token')) {
            this.$dialog.toast({
                mes: '你已经登录!',
                timeout: 2000,
                callback: () => {
                    this.$router.go(-1)
                }
            })
        }
    },
    computed: {
        checkMobile () {
            let res = {}
            if (!this.mobile) {
                res.status = false
                res.msg = '请输入手机号'
            } else if (!(/^1[345678]{1}\d{9}$/gi.test(this.mobile))) {
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
        sendCode () {
            if (!this.checkMobile.status) {
                this.$dialog.toast({mes: this.checkMobile.msg, timeout: 1300})
            } else {
                this.$dialog.loading.open('发送中...')
                setTimeout(() => {
                    this.$dialog.loading.close()
                    this.$api.sms({mobile: this.mobile, code: 'reg', type: 'bind'}, res => {
                        if (res.status) {
                            this.countDown = true
                            this.$dialog.toast({mes: res.msg, icon: 'success', timeout: 1300})
                        }
                    })
                }, 1000)
            }
        },
        reg () {
            if (!this.checkMobile.status) {
                this.$dialog.toast({mes: this.checkMobile.msg, timeout: 1300})
            } else if (!this.code) {
                this.$dialog.toast({mes: '请输入短信验证码', timeout: 1300})
            }  else {
                let data = {mobile: this.mobile, code: this.code}
                if (this.invitecode) data.invitecode = this.invitecode
                data['uuid'] = this.GLOBAL.getStorage('uuid')
                this.$api.trustBind(data, res => {
                    if (res.status) {
                        this.GLOBAL.setStorage('user_token', res.data)
                        this.$dialog.toast({
                            mes: '绑定成功!',
                            timeout: 1000,
                            icon: 'success',
                            callback: () => {
                                this.GLOBAL.removeStorage('uuid')
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
    .reg .reg-img{
        margin: .5rem auto;
    }
    .reg .reg-img img{
        border-radius: 50%;
    }
    .reg .yd-btn-block{
        /*background-color: #ff43;*/
       height: .8rem;
       width: 90%;
       margin: .5rem auto;
    }
</style>
