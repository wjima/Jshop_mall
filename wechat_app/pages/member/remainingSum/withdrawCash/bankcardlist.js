// 银行卡列表
const app = getApp(); //获取全局app.js

Page({
    // 页面数据
    data: {
        cardList: []
    },

    // 添加银行卡
    addbank: function () {
        wx.navigateTo({
            url: '../withdrawCash/addBank'
        });
    },
  
    // 加载执行
    onShow: function (options) {
        this.getBankCardList();
    },

    // 获取银行卡列表
    getBankCardList: function () {
        let page = this;
        app.db.userToken(function (token) {
            app.api.getBankCardList(function (res) {
                if (res.status) {
                    page.setData({
                        cardList: res.data
                    });
                } else {
                    wx.showModal({
                        title: '提示',
                        content: '银行卡列表数据获取失败，请返回重试',
                        showCancel: false,
                        complete: function () {
                            wx.navigateBack(1);
                        }
                    });
                }
            });
        });
    },

    // 选择银行卡
    selectCard: function (e) {
        this.getCardInfo(e.currentTarget.dataset.id);
    },

    // 获取银行卡信息
    getCardInfo: function (id) {
        let page = this;
        app.db.userToken(function (token) {
            app.api.getCardInfo(id, function (res) {
                if (res.status) {
                    res.data.card_number = res.data.card_number_i;
                    res.data.card_type = res.data.card_type_i;
                    let pages = getCurrentPages();
                    let prevPage = pages[pages.length - 2];
                    prevPage.setData({
                        cardId: res.data.id,
                        isCard: true,
                        cardInfo: res.data
                    });
                    wx.navigateBack(1);
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
})