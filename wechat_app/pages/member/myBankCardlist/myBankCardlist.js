// 我的银行卡列表
const app = getApp(); //获取全局app.js

Page({
    // 页面数据
    data: {
        lists: []
    },

    // 加载执行
    onShow: function (options) {
        this.getBankCardList(); //获取银行卡列表
    },

    // 获取银行卡列表
    getBankCardList: function () {
        let page = this;
        app.db.userToken(function (token) {
            app.api.getBankCardList(function (res) {
                if (res.status) {
                    page.setData({
                        lists: res.data
                    });
                } else {
                    wx.showModal({
                        title: '提示',
                        content: '银行卡信息获取失败，请返回重新操作',
                        showCancel: false,
                        complete: function () {
                            wx.navigateBack(1);
                        }
                    });
                }
            });
        });
    },

    // 设置默认
    setDefault: function (e) {
        let page = this;
        let id = e.currentTarget.dataset.id;
        let lists = page.data.lists;

        app.db.userToken(function (token) {
            app.api.setDefaultBankCard(id, function (res) {
                if (res.status) {
                    for (let i = 0; i < lists.length; i++) {
                        if (lists[i].id == id) {
                            lists[i].is_default = 1;
                        } else {
                            lists[i].is_default = 2;
                        }
                    }
                    page.setData({
                        lists: lists
                    });
                } else {
                    wx.showModal({
                        title: '提示',
                        content: res.msg,
                        showCancel: false
                    });
                }
            });
        });
    },

    // 删除银行卡
    delCard: function (e) {
        let page = this;
        let id = e.currentTarget.dataset.id;
        let lists = page.data.lists;

        wx.showModal({
            title: '警告',
            content: '确认删除银行卡？删除银行卡后将无法恢复',
            success: function(e) {
                if(e.confirm) {
                    app.db.userToken(function (token) {
                        app.api.delCard(id, function (res) {
                            if (!res.status) {
                                wx.showModal({
                                    title: '提示',
                                    content: res.msg,
                                    showCancel: false
                                });
                            }
                            page.getBankCardList();
                        });
                    });
                }
            }
        });
    },

    // 添加银行卡
    addCard: function () {
        wx.navigateTo({
            url: '../remainingSum/withdrawCash/addBank'
        });
    }
})