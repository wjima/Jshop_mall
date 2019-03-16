// 提现记录列表
const app = getApp(); //获取全局app.js

Page({
    // 页面数据
    data: {
        lists: [],
        typeAll: [
            '全部',
            '消费',
            '退款',
            '充值',
            '提现',
            '佣金',
            '平台调整'
        ],
        type: 0, //类型
        page: 1, //当前页
        limit: 10, //每页显示几条
        ajaxStatus: true,
        loading: true,
        loadingComplete: false,
        nodata: false,
        toView: ''
    },

    // 页面加载
    onLoad: function (e) {
        if (e.type) {
            this.setData({
                type: e.type
            });
        }
        this.getDataList();
    },

    // 获取列表数据
    getDataList: function () {
        var page = this;
        var data = {}
        data['page'] = page.data.page;
        data['limit'] = page.data.limit;
        page.data.type = page.data.type == 6 ? 7 : page.data.type;
        data['type'] = page.data.type;
        app.api.userBalance(data, function (res) {
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


    typeChange: function (e) {
        let page = this;
        page.setData({
            type: e.detail.value,
            lists: [],
            page: 1
        });

        page.getDataList();
    }
})