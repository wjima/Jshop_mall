//提货单管理
var app = getApp(); //全局APP

Page({
    //页面参数
    data: {
        ladingList: [],
        page: 1, //当前页
        limit: 10, //每页显示几条
        ajaxStatus: true,
        loading: false,
        loadingComplete: false,
        status: '',
        toView: "",
        etype: "",
        nodata: false
    },

    //页面加载
    onLoad: function (options) {
        let page = this;
        app.api.ladingList(function (res) {
            page.setData({
                ladingList: res.data
            });
        });
    }
})