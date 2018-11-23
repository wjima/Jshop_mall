// 提现
const app = getApp(); // 获取全局app.js

Page({
    // 页面数据
    data: {
        cardId: 0,
        money: 0.00,
        isCard: false,
        cardInfo: {},
        maxMoney: 0.00
    },
    
    // 银行卡选择
    bankcardlist: function () {
        wx.navigateTo({
            url: '../withdrawCash/bankcardlist'
        });
    },

    // 钱
    changeMoney: function (e) {
        let money = e.detail.value;
        this.setData({
            money: money
        });
    },
    
    // 页面加载
    onLoad: function (options) {
        // 获取默认银行卡
        this.getUserDefaultBankCard();

        this.setData({
            maxMoney: options.money
        });
    },

    // 获取默认银行卡信息
    getUserDefaultBankCard: function () {
        let page = this;
        app.db.userToken(function (token) {
            app.api.getUserDefaultBankCard(function (res) {
                if (res.status) {
                    if (res.data.id) {
                        page.setData({
                            cardId: res.data.id,
                            cardInfo: res.data,
                            isCard: true,
                        });
                    }
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

    // 提交提现信息
    confirmWithdraw: function () {
        let id = this.data.cardId;
        let money = parseFloat(this.data.money);
        let maxMoney = parseFloat(this.data.maxMoney);
        let page = this;

        if (id <= 0) {
            wx.showModal({
                title: '提示',
                content: '请确认提现的银行卡信息',
                showCancel: false
            });
            return false;
        } else if (money <= 0) {
            wx.showModal({
                title: '提示',
                content: '提现金额有误，请确认提现金额',
                showCancel: false
            });
            return false;
        } else if (money > maxMoney) {
            wx.showModal({
                title: '提示',
                content: '提现金额不能大于您的账号余额，目前您最大可提现金额为：￥'+maxMoney,
                showCancel: false
            });
            return false;
        }

        let data = {
            cardId: id,
            money: money
        }

        app.db.userToken(function (token) {
            app.api.userCash(data, function (res) {
                if (res.status) {
                    wx.showModal({
                        title: '成功',
                        content: '提现申请成功，请注意查收',
                        showCancel: false,
                        complete: function () {
                            wx.navigateBack(1);
                        }
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
    }
})