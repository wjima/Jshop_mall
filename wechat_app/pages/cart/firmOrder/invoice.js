//发票
var app = getApp();

Page({
    //页面的初始数据
    data: {
        radioItems: [
            { name: '个人或事业单位', value: '0' },
            { name: '企业', value: '1', checked: true }
        ],
        type: 1, //发票类型 0=个人 1=企业 2=不开发票
        name: '', //发票抬头
        code: '' //发票税号
    },

    //页面显示
    onShow: function () {
        let page = this;
        let pages = getCurrentPages();
        let prevPage = pages[pages.length - 2];

        let type = 1
        let name = prevPage.data.tax_name;
        let code = prevPage.data.tax_code;

        let s = {
            detail: {
                value: 1
            }
        }
        if (prevPage.data.tax_type == 1) {
            type = 1;
            name = '';
            code = '';
        } else if (prevPage.data.tax_type == 2) {
            type = 0;
            code = '';
            s = {
                detail: {
                    value: 0
                }
            }
        } else if (prevPage.data.tax_type == 3) {
            type = 1;
        }
        this.radioChange(s);

        page.setData({
            type: type,
            name: name,
            code: code
        });
    },

    //选择更改
    radioChange: function (e) {
        var radioItems = this.data.radioItems;
        for (var i = 0, len = radioItems.length; i < len; ++i) {
            radioItems[i].checked = radioItems[i].value == e.detail.value;
        }
        this.setData({
            radioItems: radioItems,
            type: e.detail.value
        });
    },

    //发票抬头
    setName: function (e) {
        this.setData({
            name: e.detail.value
        });
    },

    //设置好名称
    setOkName: function (e) {
        let data = {
            name: e.detail.value
        }
        app.api.getTaxCode(data, res => {
            if (res.status) {
                if (res.data.length > 0) {
                    this.setData({
                        code: res.data
                    });
                }
            }
        });
    },

    //发票税号
    setCode: function (e) {
        this.setData({
            code: e.detail.value
        });
    },

    //不开发票
    noTax: function () {
        let pages = getCurrentPages();
        let prevPage = pages[pages.length - 2];
        prevPage.setData({
            tax_type: 1,
            tax_name: '本次订单不需要发票',
            tax_code: ''
        });
        wx.navigateBack(1);
    },

    //开发票
    yesTax: function () {
        let page = this;
        let pages = getCurrentPages();
        let prevPage = pages[pages.length - 2];
        let flag = true;
        let tax_type = 1;

        if (page.data.type == 1) {
            if (page.data.name.length < 1) {
                app.common.errorToBack('请填写发票抬头', 0);
                flag = false;
                return false;
            }
            if (page.data.code.length < 1) {
                app.common.errorToBack('请填写发票税号', 0);
                flag = false;
                return false;
            }
            tax_type = 3;
        } else if(page.data.type == 0) {
            if (page.data.name.length < 1) {
                app.common.errorToBack('请填写发票抬头', 0);
                flag = false;
                return false;
            }
            tax_type = 2
        }

        if (flag) {
            prevPage.setData({
                tax_type: tax_type,
                tax_name: page.data.name,
                tax_code: page.data.code
            });
            wx.navigateBack(1);
        }
    }
});