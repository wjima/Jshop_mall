<template>
    <div class="login">
        <div class="login-img">
            <img src="../../../static/image/group14.png"/>
        </div>
        <!--<yd-cell-group>-->
        <yd-tab v-model="tab" :prevent-default="false" :item-click="itemClick" v-if="!isWeiXinBrowser">
            <yd-tab-panel label="账号登陆">
                <div class="login-content">
                    <yd-cell-item>
                        <span slot="left">手机号：</span>
                        <yd-input slot="right" ref="tel" required :show-success-icon="false" regex="mobile" v-model="mobile" placeholder="请输入手机号"></yd-input>
                    </yd-cell-item>
                    <yd-cell-item>
                        <span slot="left">登录密码：</span>
                        <yd-input slot="right" type="password" v-model="password" placeholder="请输入密码"></yd-input>
                    </yd-cell-item>
                    <yd-cell-item v-if="isShowCaptcha">
                        <span class="w4" slot="left">验证码：</span>
                        <yd-input slot="right" type="text" v-model="captcha" placeholder="请输入验证码"></yd-input>
                        <img slot="right" :src="localCaptcha" alt="" @click="reloadCaptcha" width="150">
                    </yd-cell-item>
                </div>
            </yd-tab-panel>
            <yd-tab-panel label="手机号登陆">
                <div class="login-content">
                    <yd-cell-item>
                        <span slot="left">手机号：</span>
                        <yd-input type="text" slot="right" ref="tel" required :show-success-icon="false" regex="mobile" v-model="mobile" placeholder="请输入手机号码"></yd-input>
                        <yd-sendcode slot="right"
                                     storage-key="login"
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
                </div>
            </yd-tab-panel>
            <yd-button size="large" type="danger" @click.native="login">登录</yd-button>
            没有账号?<span style="color: #10aeff;" @click="toRegister">&nbsp;立即注册</span>
        </yd-tab>
        <div v-if="authList.length">
            <div v-for="(item, index) in authList" :key="index">
                <div class="wechat-login" v-for="(child, key) in item" :key="key">
                    <yd-button size="large" type="primary" @click.native="toAuth(child.url)" v-if="key === 'weixin'">微信登录</yd-button>
                </div>
            </div>
        </div>
        <!--</yd-cell-group>-->

    </div>
</template>

<script>
import { host } from "../../common/serviceUrl"
export default {
    data () {
        return {
            tab: parseInt(this.GLOBAL.getStorage('loginType')) || 0,
            invitecode: this.GLOBAL.getStorage('invitecode') || '', // 邀请码
            mobile: null,
            password: null,
            code: null, // 短信验证码
            isShowCaptcha: false, // 是否需要登录验证码
            captcha: '', // 用户输入的验证码
            localCaptcha: '', // 登录次数过多验证码图片
            countDown: false, // 发送验证码倒计时 发送成功后修改为true倒计时启动
            authList: []
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
        // 判断是否是微信浏览器
        if (this.isWeiXinBrowser) {
            this.getAuth()
        }
    },
    computed: {
        // 验证手机号
        rightMobile () {
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
        },
        isWeiXinBrowser () {
            return this.GLOBAL.isWeiXinBrowser()
        }
    },
    methods: {
        // 切换登录方式  本地缓存状态tab  防止页面刷新丢失状态
        itemClick (key) {
            this.tab = key
            this.GLOBAL.setStorage('loginType', key)
        },
        // 发送短信验证码
        sendCode () {
            if (!this.rightMobile.status) {
                this.$dialog.toast({mes: this.rightMobile.msg, timeout: 1000})
            } else {
                this.$dialog.loading.open('发送中...')
                setTimeout(() => {
                    this.$dialog.loading.close()
                    this.$api.sms({mobile: this.mobile, code: 'login'}, res => {
                        if (res.status) {
                            this.countDown = true
                            this.$dialog.toast({mes: res.msg, icon: 'success', timeout: 1000})
                        }
                    })
                }, 1000)
            }
        },
        // 登录
        login () {
            if (!this.rightMobile.status) {
                this.$dialog.toast({mes: this.rightMobile.msg, timeout: 1000})
            } else {
                if (!this.tab) {
                    // 账号密码登录
                    if (!this.password) {
                        this.$dialog.toast({mes: '请输入密码!', timeout: 1000})
                    } else {
                        let data = {
                            mobile: this.mobile,
                            password: this.password
                        }
                        // 判断是否需要验证码登录
                        if (this.isShowCaptcha) {
                            data['captcha'] = this.captcha
                        }
                        this.$api.login(data, res => {
                            if (res.status) {
                                this.GLOBAL.setStorage('user_token', res.data)
                                this.redirectHandler()
                            } else {
                                // 需要输入验证码 或者 验证码错误刷新
                                if (res.data === 10013 || res.data === 10012) {
                                    this.isShowCaptcha = true
                                }
                                // 密码错误刷新
                                if (this.isShowCaptcha) {
                                    this.localCaptcha = this.GLOBAL.getCaptcha()
                                }
                            }
                        })
                    }
                } else {
                    // 短信验证码登录
                    if (!this.code) {
                        this.$dialog.toast({mes: '请输入短信验证码!', timeout: 1000})
                    } else {
                        let data = {mobile: this.mobile, code: this.code, invitecode: this.invitecode}
                        this.$api.smsLogin(data, res => {
                            if (res.status) {
                                this.GLOBAL.setStorage('user_token', res.data)
                                this.redirectHandler()
                            }
                        })
                    }
                }
            }
        },
        // 重定向跳转 或者返回上一个页面
        redirectHandler () {
            this.$router.replace(this.$route.query.redirect ? decodeURIComponent(this.$route.query.redirect) : '/')
        },
        // 跳转注册页面
        toRegister () {
            if (this.$route.query.redirect) {
                this.$router.replace({path: '/register', query: {redirect: decodeURIComponent(this.$route.query.redirect)}})
            } else {
                this.$router.replace({path: '/register'})
            }
        },
        // 刷新验证码
        reloadCaptcha () {
            this.localCaptcha = this.GLOBAL.getCaptcha()
        },
        // 获取授权登录方式列表
        getAuth () {
            this.$api.getTrustLogin({
                url: host + '/wap/index.html#/author',
                uuid: this.genNonDuplicateID()
            }, res => {
                if (res.status) {
                    this.authList = res.data
                }
            })
        },
        toAuth (url) {
            window.location.href = url
        },
        // 生成一个用不重复的ID
        genNonDuplicateID () {
            let uid = Math.random().toString(36).substr(3)
            this.GLOBAL.setStorage('uuid', uid)
            return uid
        }
    }
}
</script>

<style>
    .login .login-img{
        margin: .5rem auto;
    }
    .login .login-img img{
        border-radius: 50%;
    }
    .login .yd-tab-panel{
        background-color: #f5f5f5;
    }
    .login .yd-tab-panel-item .login-content{
        background-color: #fff;
    }
    .login .yd-tab-nav .yd-tab-nav-item:not(:last-child):after{
        border: none;
    }
    .login .yd-btn-block{
        /*background-color: #ff43;*/
       height: .8rem;
       width: 90%;
       margin: .5rem auto;
    }
    .wechat-login{
        width: 80%;
        height: 1rem;
        margin: 0 auto .5rem;
    }
    .wechat-login img{
        width: 100%;
        height: 100%;
    }
</style>
