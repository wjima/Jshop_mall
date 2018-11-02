<template>
    <div class="datasetting">
        <yd-tab v-model="tab">
            <yd-tab-panel label="资料设置">
                <div class="datasetting-content">
                    <yd-cell-group>
                        <yd-cell-item>
                            <img slot="icon" src="../../../static/image/man.png">
                            <span slot="left">昵称</span>
                            <input slot="right" type="text" v-model="name" placeholder="请输入昵称">
                        </yd-cell-item>
                        <yd-cell-item>
                            <img slot="icon" src="../../../static/image/calendar.png">
                            <span slot="left">生日</span>
                            <yd-datetime type="date" start-date="1970" :end-date="date" v-model="birthday" slot="right"></yd-datetime>
                        </yd-cell-item>
                        <yd-cell-item>
                            <img slot="icon" src="../../../static/image/calendar.png">
                            <span slot="left">性别</span>
                            <input slot="right" type="text" @click="show = true" readonly v-model="sexName">
                            <input type="hidden" v-model="sexVal">
                        </yd-cell-item>
                    </yd-cell-group>
                </div>
            </yd-tab-panel>
            <yd-tab-panel label="修改密码">
                <yd-cell-group>
                    <yd-cell-item>
                        <span slot="left">旧密码：</span>
                        <yd-input slot="right" type="password" v-model="pwd" placeholder="请输入当前密码"></yd-input>
                    </yd-cell-item>
                    <yd-cell-item>
                        <span slot="left">新密码：</span>
                        <yd-input slot="right" type="password" v-model="newPwd" placeholder="请输入新密码" key="edit"></yd-input>
                    </yd-cell-item>
                    <yd-cell-item>
                        <span slot="left">确认密码：</span>
                        <yd-input slot="right" type="password" v-model="rePwd" placeholder="请输入确认密码" key="edit"></yd-input>
                    </yd-cell-item>
                </yd-cell-group>
            </yd-tab-panel>
            <yd-tab-panel label="重置密码">
                <yd-cell-group>
                    <yd-cell-item>
                        <span slot="left">手机号：</span>
                        <yd-input type="text" slot="right" readonly :show-clear-icon="false" :show-success-icon="false" regex="mobile" v-model="mobile" placeholder="请输入手机号码"></yd-input>
                        <yd-sendcode slot="right"
                                     storage-key="forgotPwd"
                                     v-model="sendDone"
                                     init-str="获取验证码"
                                     @click.native="sendCode"
                                     type="primary"
                        ></yd-sendcode>
                    </yd-cell-item>
                    <yd-cell-item>
                        <span slot="left">验证码：</span>
                        <yd-input slot="right" v-model="smsCode" placeholder="请输入短信验证码"></yd-input>
                    </yd-cell-item>
                    <yd-cell-item>
                        <span slot="left">新密码：</span>
                        <yd-input slot="right" type="password" v-model="newPwd" placeholder="请输入新密码" key="back"></yd-input>
                    </yd-cell-item>
                    <yd-cell-item>
                        <span slot="left">确认密码：</span>
                        <yd-input slot="right" type="password" v-model="rePwd" placeholder="请输入确认密码" key="back"></yd-input>
                    </yd-cell-item>
                </yd-cell-group>
            </yd-tab-panel>
            <yd-actionsheet :items="sexItems" v-model="show" cancel="取消"></yd-actionsheet>
        </yd-tab>
        <!-- 显示性别选择列表 -->
        <yd-actionsheet :items="sexItems" v-model="show" cancel="取消"></yd-actionsheet>
        <yd-button-group>
            <yd-button size="large" bgcolor="#ff3b44" color="#fff" @click.native="submitHandler">确认修改</yd-button>
        </yd-button-group>
    </div>
</template>

<script>
export default {
    data () {
        return {
            tab: parseInt(this.GLOBAL.getStorage('settingTab')) || 0,
            name: '', // 名称
            birthday: '', // 生日
            sexName: '', // 性别名称
            sexVal: '', // 性别val
            pwd: '', // 原始密码
            newPwd: '', // 新密码
            rePwd: '', // 确认密码
            sendDone: false, // 开始计时
            smsCode: '', // 短信验证码
            mobile: '', // 用户手机号
            show: false,
            sexItems: [
                {
                    label: '男',
                    val: 1,
                    callback: (e) => {
                        this.sexName = e.label
                        this.sexVal = e.val
                    }
                },
                {
                    label: '女',
                    val: 2,
                    callback: (e) => {
                        this.sexName = e.label
                        this.sexVal = e.val
                    }
                },
                {
                    label: '保密',
                    val: 3,
                    callback: (e) => {
                        this.sexName = e.label
                        this.sexVal = e.val
                    }
                }
            ]
        }
    },
    computed: {
        // 生日选择不得超过当前日期
        date () {
            let date = new Date()
            let year = date.getFullYear()
            let month = date.getMonth() + 1
            let day = date.getDate()
            if (month < 10) {
                month = '0' + month
            }
            if (day < 10) {
                day = '0' + day
            }
            return year + '/' + month + '/' + day
        }
    },
    created () {
        // 获取用户信息
        this.$api.userInfo({}, res => {
            if (res.status) {
                this.name = res.data.nickname
                this.birthday = res.data.birthday
                this.mobile = res.data.mobile
                if (res.data.sex === 1) {
                    this.sexName = '男'
                } else if (res.data.sex === 2) {
                    this.sexName = '女'
                } else {
                    this.sexName = '保密'
                }
                this.sexVal = res.data.sex
            }
        })
    },
    methods: {
        sendCode () {
            this.$dialog.loading.open('发送中...')
            setTimeout(() => {
                this.$dialog.loading.close()
                this.$api.sms({mobile: this.mobile, code: 'veri'}, res => {
                    if (res.status) {
                        this.sendDone = true
                        this.$dialog.toast({mes: res.msg, icon: 'success', timeout: 1500})
                    }
                })
            }, 1000)
        },
        // 编辑提交
        submitHandler () {
            switch (this.tab) {
            case 0:
                this.$api.editInfo({
                    sex: this.sexVal,
                    birthday: this.birthday,
                    nickname: this.name
                }, res => {
                    this.$dialog.toast({mes: res.msg, timeout: 1000, icon: 'success'})
                })
                break
            case 1:
                if (this.pwd === '') {
                    this.$dialog.toast({mes: '请输入旧密码', timeout: 1000})
                } else if (this.newPwd === '') {
                    this.$dialog.toast({mes: '请输入新密码', timeout: 1000})
                } else if (this.rePwd === '') {
                    this.$dialog.toast({mes: '请输入重复密码', timeout: 1000})
                } else {
                    this.$api.editPwd({
                        pwd: this.pwd,
                        newpwd: this.newPwd,
                        repwd: this.rePwd
                    }, res => {
                        this.resultStatus(res)
                    })
                }
                break
            case 2:
                if (this.newPwd === '') {
                    this.$dialog.toast({mes: '请输入新密码', timeout: 1000})
                } else if (this.rePwd === '') {
                    this.$dialog.toast({mes: '请输入重复密码', timeout: 1000})
                } else if (this.smsCode === '') {
                    this.$dialog.toast({mes: '请输入短信验证码', timeout: 1000})
                } else {
                    this.$api.forgotPwd({
                        mobile: this.mobile,
                        code: this.smsCode,
                        newpwd: this.newPwd,
                        repwd: this.rePwd
                    }, res => {
                        this.resultStatus(res)
                    })
                }
                break
            default:
                break
            }
        },
        // 修改密码  返回结果统一处理
        resultStatus (res) {
            if (res.status) {
                this.$dialog.alert({
                    mes: res.msg + '请重新登录',
                    timeout: 1000,
                    callback: () => {
                        this.GLOBAL.removeStorage('settingTab')
                        this.GLOBAL.removeStorage('user_token')
                        this.$router.push({path: '/login'})
                    }
                })
            }
        }
    },
    watch: {
        tab () {
            this.GLOBAL.setStorage('settingTab', this.tab)
            // 切换tab清空对应表单密码内容
            this.pwd = ''
            this.newPwd = ''
            this.rePwd = ''
        }
    }
}
</script>
