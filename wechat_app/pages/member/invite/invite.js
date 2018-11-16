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
        appTitle: ''
    },


    //页面加载
    onLoad: function (options) {
        this.getInviteData();
        app.api.sharecode(function (e) {
            if (e.status) {
                //获取邀请码成功
                wx.setStorage({
                    key: "myInviteCode",
                    data: e.data
                });
            }
        });
        this.setData({
            appTitle : app.config.app_title
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
        let myInviteCode = wx.getStorageSync('myInviteCode');
        if (myInviteCode) {
            //缓存里面有邀请码
            let path = '/pages/index/index?invite=' + myInviteCode;
            return {
                title: page.data.appTitle,
                path: path,
                imageUrl: '/pages/image/write-banner.png'
            }
        } else {
            let path = '/pages/index/index';
            return {
                title: page.data.appTitle,
                path: path,
                imageUrl: '/pages/image/write-banner.png'
            }
        }
    },


    //触发生成海报
    generatePoster: function () {
        this.setData({
            pop: true
        });
        wx.showLoading({
            title: '生成中',
            // mask: true
        });
        this.eventDraw();
    },


    //生成海报
    eventDraw: function () {
        let avatar = '/static/images/default.png';
        let nickname = '你的名字';
        
        this.setData({
            painting: {
                width: 280,
                height: 430,
                clear: true,
                views: [
                    {
                        type: 'image',
                        url: '/static/images/bg.png',
                        top: 0,
                        left: 0,
                        width: 280,
                        height: 430
                    },{
                        type: 'image',
                        url: avatar,
                        top: 27.5,
                        left: 29,
                        width: 55,
                        height: 55
                    },
                    // {
                    //     type: 'image',
                    //     url: 'https://hybrid.xiaoying.tv/miniprogram/viva-ad/1/1531401349117.jpeg',
                    //     top: 27.5,
                    //     left: 29,
                    //     width: 55,
                    //     height: 55
                    // },
                    {
                        type: 'text',
                        content: '您的好友【' + nickname + '】',
                        fontSize: 16,
                        color: '#402D16',
                        textAlign: 'left',
                        top: 33,
                        left: 96,
                        bolder: true
                    },
                    {
                        type: 'text',
                        content: '发现了一家好店，邀您查看',
                        fontSize: 15,
                        color: '#563D20',
                        textAlign: 'left',
                        top: 59.5,
                        left: 96
                    },
                    {
                        type: 'image',
                        url: 'https://hybrid.xiaoying.tv/miniprogram/viva-ad/1/1531385366950.jpeg',
                        top: 136,
                        left: 42.5,
                        width: 290,
                        height: 186
                    },
                    {
                        type: 'image',
                        url: 'https://hybrid.xiaoying.tv/miniprogram/viva-ad/1/1531385433625.jpeg',
                        top: 443,
                        left: 85,
                        width: 68,
                        height: 68
                    },
                    {
                        type: 'text',
                        content: '正品MAC魅可口红礼盒生日唇膏小辣椒Chili西柚情人',
                        fontSize: 16,
                        lineHeight: 21,
                        color: '#383549',
                        textAlign: 'left',
                        top: 336,
                        left: 44,
                        width: 287,
                        MaxLineNumber: 2,
                        breakWord: true,
                        bolder: true
                    },
                    {
                        type: 'text',
                        content: '￥0.00',
                        fontSize: 19,
                        color: '#E62004',
                        textAlign: 'left',
                        top: 387,
                        left: 44.5,
                        bolder: true
                    },
                    {
                        type: 'text',
                        content: '原价:￥138.00',
                        fontSize: 13,
                        color: '#7E7E8B',
                        textAlign: 'left',
                        top: 391,
                        left: 110,
                        textDecoration: 'line-through'
                    },
                    {
                        type: 'text',
                        content: '长按识别图中二维码帮我砍个价呗~',
                        fontSize: 14,
                        color: '#383549',
                        textAlign: 'left',
                        top: 460,
                        left: 165.5,
                        lineHeight: 20,
                        MaxLineNumber: 2,
                        breakWord: true,
                        width: 125
                    }
                ]
            }
        })
    },


    //获取图片
    eventGetImage: function (event) {
        console.log(event)
        wx.hideLoading()
        const { tempFilePath, errMsg } = event.detail
        if (errMsg === 'canvasdrawer:ok') {
            this.setData({
                shareImage: tempFilePath
            })
        }
    },


    //关闭海报层
    clone: function() {
        this.setData({
            pop: false
        });
    },


    move: function () {}
})