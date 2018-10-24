// 添加银行卡
const app = getApp(); // 获取全局app.js

Page({
    //页面数据
    data: {
        cardNumber: '', //银行卡号
        bankName: '', //银行名
        bankCode: '', //银行编码
        cardType: 0, //银行卡类型
        cardTypeName: '', //银行卡类型名称
        accountName: '', //持卡人
        accountBank: '', //开户行名
        region: ['河南省', '郑州市', '中原区'], //开户行地区
        areaId: 410102, //开户行地区ID
        isDefault: 1, //是否默认
    },

    //获取银行卡号
    cardNumber: function (e) {
        let cardCode = e.detail.value;
        let page = this;
        if(cardCode.length > 5)
        {
            app.api.getBankCardOrganization(e.detail.value, function(res){
                if(res.status){
                    page.setData({
                        cardNumber: cardCode,
                        bankName: res.data.name,
                        bankCode: res.data.bank_code,
                        cardType: res.data.type,
                        cardTypeName: res.data.type_name
                    });
                } else {
                    wx.showModal({
                        title: '提示',
                        content: '银行卡信息获取失败，请检查银行卡号是否正确',
                        showCancel: false
                    });
                }
            });
        }
    },

    //持卡人
    accountName: function (e) {
        let name = e.detail.value;
        this.setData({
            accountName: name
        });
    },

    //开户行名
    accountBank: function (e) {
        let name = e.detail.value;
        this.setData({
            accountBank: name
        });
    },

    //开户行地区
    regionChange: function (e) {
        let province_name = e.detail.value[0];
        let city_name = e.detail.value[1];
        let county_name = e.detail.value[2];
        let postal_code = 0;
        let page = this;
        let data = {
            province_name: province_name,
            city_name: city_name,
            county_name: county_name,
            postal_code: postal_code
        }
        let regionName = [province_name, city_name, county_name];
        page.setData({
            region: regionName
        });
        app.api.getAreaId(data, function (res) {
            if(res.status) {
                page.setData({
                    areaId: res.data
                });
            } else {
                wx.showModal({
                    title: '提示',
                    content: '地区选择出现问题，请重新选择地区',
                    showCancel: false
                });
            }
        });
    },

    //是否默认
    isDefault: function (e) {
        let isDefault = 2
        if (e.detail.value) {
            //默认
            isDefault = 1;
        }
        this.setData({
            isDefault: isDefault
        });
    },

    //添加银行卡
    addCard: function () {
        let data = {
            bankName: this.data.bankName,
            bankCode: this.data.bankCode,
            areaId: this.data.areaId,
            accountBank: this.data.accountBank,
            accountName: this.data.accountName,
            cardNumber: this.data.cardNumber,
            cardType: this.data.cardType,
            isDefault: this.data.isDefault,
        }
        app.db.userToken(function (token) {
            app.api.addBankCard(data, function (res) {
                if (res.status) {
                    wx.showToast({
                        title: '银行卡添加成功',
                        icon: 'success',
                        mask: true,
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
});