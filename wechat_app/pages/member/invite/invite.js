//邀请页面
var app = getApp(); //全局APP

Page({
    //页面数据
    data: {
        code: '',
        money: 0,
        number: 0,
        is_superior: false,
        inviteKey: '',
        pop: false,
        painting: {},
        shareImage: '',
        qrcode: '',
        qrcodeErrorMsg: '小程序二维码生成失败，无法生成海报，请稍候尝试',
        nickname: '',
        avatar: ''
    },


    //页面加载
    onLoad: function (options) {
        this.getInviteData();
        this.getUserInfo();
    },


    //获取用户信息
    getUserInfo: function () {
        let page = this;
        app.api.userInfo(function (res) {
            page.setData({
                nickname: res.data.nickname,
                avatar: res.data.avatar
            });
        });
    },


    //获取我的邀请信息
    getInviteData: function () {
        let page = this;
        app.api.myInvite(function(res){
            page.setData({
                code: res.data.code,
                money: res.data.money,
                number: res.data.number,
                is_superior: res.data.is_superior
            });
            page.getQRCode();
        });
    },


    //获取邀请二维码
    getQRCode: function () {
        let page = this;
        let data = {
            'type': 'index',
            'invite': page.data.code,
            'goods': 0
        }
        app.api.getQRCode(data, function (e) {
            console.log(e);
            if (e.status) {
                let url = app.config.api_url + e.data;
                page.setData({
                    qrcode: url
                });
            } else {
                page.setData({
                    qrcodeErrorMsg: e.msg
                });
            }
        });
    },


    //监听上级邀请码
    inviteKey: function (e) {
        let code = e.detail.value;
        this.setData({
            inviteKey: code
        });
    },


    //提交我的邀请码
    setMyInvite: function () {
        let page = this;
        let code = page.data.inviteKey;
        let data = {
            code: code
        }
        app.api.setMyInvite(data, function(res){
            if(res.status){
                wx.showToast({
                    title: '邀请码填写成功',
                    icon: 'success',
                    duration: 1500
                });
                page.setData({
                    is_superior: true
                });
            }else{
                wx.showToast({
                    title: res.msg,
                    icon: 'none',
                    duration: 1500
                });
            }
        });
    },
 

    //分享到微信群
    onShareAppMessage: function () {
        let page = this;
        let ins = encodeURIComponent('invite=' + page.data.code);
        let path = '/pages/index/index?scene=' + ins;
        let shareImg = '/static/images/share.png';
        let appTitle = '您的好友发现了一家好店，邀您查看！';
        return {
            title: appTitle,
            path: path,
            imageUrl: shareImg
        }
    },


    //触发生成海报
    generatePoster: function () {
        this.setData({
            pop: true
        });
        if (this.data.shareImage == '') {
            wx.showLoading({
                title: '生成中'
            });
            this.eventDraw();
        }
    },


    //生成海报
    eventDraw: function () {
        let page = this;    
        let avatar = page.data.avatar;
        let nickname = page.data.nickname;
        let storename = app.config.app_title;
        let invite = page.data.code;
        console.log(page.data.avatar);
        console.log(page.data.qrcode);
        if (page.data.qrcode == '') {
            wx.showToast({
                title: page.data.qrcodeErrorMsg,
                icon: 'none',
                duration: 2000
            });
            page.clone();
            return false;
        } else {
            let qrcode = page.data.qrcode;
            this.setData({
                painting: {
                    width: 560,
                    height: 860,
                    clear: true,
                    views: [
                        {
                            type: 'image',
                            url: '/static/images/bg.png',
                            top: 0,
                            left: 0,
                            width: 560,
                            height: 860
                        },
                        {
                            type: 'image',
                            url: avatar,
                            top: 38,
                            left: 46,
                            width: 100,
                            height: 100
                        },
                        {
                            type: 'text',
                            content: '您的好友【' + nickname + '】',
                            fontSize: 28,
                            color: '#ffffff',
                            textAlign: 'left',
                            top: 50,
                            left: 170,
                            bolder: true
                        },
                        {
                            type: 'text',
                            content: '发现了一家好店，邀您查看',
                            fontSize: 24,
                            color: '#fecccc',
                            textAlign: 'left',
                            top: 90,
                            left: 170
                        },
                        {
                            type: 'image',
                            url: qrcode,
                            top: 220,
                            left: 135,
                            width: 290,
                            height: 290
                        },
                        {
                            type: 'text',
                            content: storename,
                            fontSize: 28,
                            lineHeight: 28,
                            color: '#383549',
                            textAlign: 'center',
                            top: 530,
                            left: 280,
                            bolder: true
                        },
                        {
                            type: 'text',
                            content: '长按图片识别图中二维码进入' + storename + '小程序一起寻好物',
                            fontSize: 22,
                            lineHeight: 30,
                            color: '#727272',
                            textAlign: 'center',
                            top: 580,
                            left: 280,
                            width: 360,
                            MaxLineNumber: 2,
                            breakWord: true,
                        },
                        {
                            type: 'text',
                            content: invite,
                            fontSize: 30,
                            lineHeight: 30,
                            color: '#d13106',
                            textAlign: 'center',
                            top: 700,
                            left: 280,
                            bolder: true
                        },
                        {
                            type: 'text',
                            content: '我的专属邀请码',
                            fontSize: 24,
                            lineHeight: 24,
                            color: '#727272',
                            textAlign: 'center',
                            top: 750,
                            left: 280,
                            bolder: true
                        },
                    ]
                }
            });
        }
    },


    //获取图片
    eventGetImage: function (event) {
        wx.hideLoading()
        const { tempFilePath, errMsg } = event.detail
        if (errMsg === 'canvasdrawer:ok') {
            this.setData({
                shareImage: tempFilePath
            })
        }
    },


    //保存图片
    eventSave: function () {
        wx.saveImageToPhotosAlbum({
            filePath: this.data.shareImage,
            success(res) {
                wx.showToast({
                    title: '保存图片成功',
                    icon: 'success',
                    duration: 2000
                })
            }
        })
    },


    //关闭海报层
    clone: function() {
        this.setData({
            pop: false
        });
    },


    move: function () {}
})