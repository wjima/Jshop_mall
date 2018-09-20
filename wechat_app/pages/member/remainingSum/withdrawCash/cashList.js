// 提现记录列表
const app = getApp(); //获取全局app.js

Page({
    // 页面数据
    data: {
        lists: [],
        page: 1, //当前页
        limit: 10, //每页显示几条
        ajaxStatus: true,
        loading: true,
        loadingComplete: false,
        nodata: false,
        toView: ''
    },

    // 页面加载
    onLoad: function () {
        this.getDataList();
    },

    // 获取列表数据
    getDataList: function () {
        var page = this;
        var data = {}
        data['page'] = page.data.page;
        data['limit'] = page.data.limit;
        app.db.userToken(function (token) {
            app.api.getCashList(data, function (res) {
                if (res.status) {
                    let loadingComplete = false;
                    let loading = true;
                    let ajaxStatus = true;
                    let pages = page.data.page + 1;
                    if (res.total <= page.data.page) {
                        loadingComplete = true;
                        loading = false;
                        ajaxStatus = false;
                    }
                    let lists = page.data.lists.concat(res.data);
                    let nodata = false;
                    if (lists.length < 1) {
                        nodata = true;
                        loading = false;
                        loadingComplete = false;
                    }

                    page.setData({
                        lists: lists,
                        page: pages,
                        ajaxStatus: ajaxStatus,
                        loading: loading,
                        loadingComplete: loadingComplete,
                        nodata: nodata
                    });
                }
            });
        });
    },

    // 上拉加载
    lower: function () {
        var page = this;
        page.setData({
            toView: 'loading'
        });
        if (page.data.ajaxStatus && !page.data.loadingComplete) {
            page.setData({
                ajaxStatus: false,
            });
            page.getDataList();
        }
    },
})