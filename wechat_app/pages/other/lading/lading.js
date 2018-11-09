//提货单管理
var app = getApp(); //全局APP

Page({
    //页面参数
    data: {
        ladingList: [],
    },

    //页面加载
    onLoad: function (options) {
        let page = this;
        app.api.ladingList(function (res) {
            page.setData({
                ladingList: res.data
            });
        });
    },

    //提货单核销
    ladingWrite: function (e) {
        let page = this;
        let id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: './write?id='+id,
        });
    }
})