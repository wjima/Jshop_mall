//提货单管理
var app = getApp(); //全局APP

Page({
    //页面参数
    data: {
        ladingList: [],
    },

    //页面加载
    onShow: function (options) {
        this.getLadingList();
    },

    //获取提货单列表
    getLadingList: function () {
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
    },

    //删除
    ladingDel: function (e) {
        let id = e.currentTarget.dataset.id;
        let page = this;

        wx.showModal({
            title: '确认删除？',
            content: '删除提货单后将无法找回！',
            success: function (res) {
                if (res.confirm) {
                    let data = {
                        'lading_id': id
                    }
                    app.api.ladingDel(data, function (res) {
                        wx.showToast({
                            title: res.msg,
                            success: function () {
                                page.getLadingList();
                            }
                        });
                    });
                }
            }
        });
    }
})