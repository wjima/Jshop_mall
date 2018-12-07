// 账号余额
const app = getApp(); //获取全局app.js

Page({
    // 页面数据
    data: {
        balance: '0.00'
    },

    // 页面显示
    onShow: function () {
        this.getUserBalance();
    },

    // 获取余额
    getUserBalance: function () {
        let page = this;
        app.db.userToken(function (token) {
            app.api.userInfo(function (res) {
                if (res.status) {
                    page.setData({
                        balance: res.data.balance
                    });
                } else {
                    wx.showModal({
                        title: '提示',
                        content: '账号余额数据获取失败，请返回稍后查询',
                        showCancel: false,
                        complete: function () {
                            wx.navigateBack(1);
                        }
                    });
                }
            });
        });
    },

    // 充值
  recharge: function () {
      wx.navigateTo({
        url: '../withdrawCash/recharge?money=' + this.data.balance
      });
    },

    // 提现
    balance: function () {
        wx.navigateTo({
            url: '../withdrawCash/balance?money=' + this.data.balance
        });
    },

    // 余额明细
    balancelist: function () {
        wx.navigateTo({
            url: '../withdrawCash/balanceList'
        });
    },

    // 提现记录
    cashlist: function () {
        wx.navigateTo({
        url: '../withdrawCash/cashList'
        });
    },

    // 我的银行卡
    mybackcardlist: function () {
      wx.navigateTo({
        url: '../../myBankCardlist/myBankCardlist'
      });
    },
})