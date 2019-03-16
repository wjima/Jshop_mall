// 我的积分
const app = getApp(); //获取全局app.js

Page({
    // 页面数据
    data: {
        point: 0
    },

    // 页面显示
    onShow: function () {
        this.getUserPoint();
    },

    // 获取余额
    getUserPoint: function () {
        let page = this;
        app.api.getUserPoint({}, function (res) {
            if (res.status) {
                page.setData({
                    point: res.data
                });
            } else {
                wx.showModal({
                    title: '提示',
                    content: '账户积分数据获取失败，请返回稍后查询',
                    showCancel: false,
                    complete: function () {
                        wx.navigateBack(1);
                    }
                });
            }
        });
    },

    // 积分明细
    pointList: function () {
        wx.navigateTo({
            url: 'pointList'
        });
    },

    // 签到页面
    sign: function () {
        wx.navigateTo({
            url: 'pointSign'
        });
    },
})