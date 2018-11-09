//提货单核销
var app = getApp(); //全局APP

Page({
    //页面数据
    data: {
        key: '',
        goodsList: [],
        lading_id: false,
        isgo: false,
        isgotext: '确认核销'
    },

    //页面加载
    onLoad: function (e) {
        if(e.id){
            this.setData({
                key: e.id
            });
        }
        this.getLadingInfo();
    },

    //获取提货单详情
    getLadingInfo: function () {
        let page = this;
        if(page.data.key){
            let data = {
                'key': page.data.key
            }
            app.api.ladingInfo(data, function (e) {
                if (e.status) {
                    page.isgo(e.data);
                } else {
                    wx.showModal({
                        title: '提货单不存在或你无权查看',
                        content: '该提货单不存在或不属于你管辖的店铺，你无法查看该提货单详情。',
                        success: function (res) {

                        }
                    });
                }
            });
        }
    },

    //设置关键字
    addkey: function (e) {
        this.setData({
            key: e.detail.value
        });
    },

    //搜索
    search: function () {
        this.getLadingInfo();
    },

    //查询判断是否可以核销
    isgo: function (data) {
        let page = this;
        let isgo = false;
        
        if (data.order_info.pay_status == 2 && data.order_info.ship_status == 3){
            isgo = true;
            page.setData({
                lading_id: data.id,
                goodsList: data.goods
            });
        } else {
            wx.showModal({
                title: '无法核销',
                content: '订单必须支付并已发货才可以核销',
                success: function (res) {

                }
            });
        }
        
        page.setData({
            isgo: isgo
        });
    },

    //去核销
    write: function () {
        let page = this;
        if(page.data.isgo){
            //去核销
            let lading_id = page.data.lading_id;
            let data = {
                lading_id: lading_id
            }
            app.api.lading(data, function(res){
                wx.showModal({
                    title: res.msg,
                    success: function (res) {
                        page.setData({
                            key: '',
                            goodsList: [],
                            lading_id: false,
                            isgo: false,
                            isgotext: '确认核销'
                        });
                    }
                });
            });
        }
    }
});