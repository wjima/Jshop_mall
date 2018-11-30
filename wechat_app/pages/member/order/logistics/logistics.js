var app = getApp(); //全局APP

Page({
    //页面数据
    data: {
        code: '',
        no: '',
        data: '',
        status: '',
        flag: false
    },

    
    //页面加载
    onLoad: function (e) {
        this.setData({
            code: e.code,
            no: e.no
        });

        this.getLogisticsData();
    },


    //获取数据
    getLogisticsData: function () {
        let page = this;
        let data = {
            code: page.data.code,
            no: page.data.no
        }
        app.api.getLogisticsData(data, function(e){
            if(e.status){
                page.setData({
                    flag: true,
                    data: e.data.info.data,
                    status: e.data.info.state_name
                });
            }else{
                wx.showModal({
                    title: '提示',
                    content: '物流信息异常，请稍候查询',
                    showCancel: false,
                    complete: function () {
                        wx.navigateBack(1);
                    }
                });
            }
        });
    }
});