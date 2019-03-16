//我的优惠券
var app = getApp(); //全局APP

Page({
    //页面数据
    data: {
        listData: [], //数据
        page: 1, //当前页
        limit: 10, //每页显示几条
        ajaxStatus: true,
        loading: false,
        loadingComplete: false,
        toView: "",
        nodata: false,
        notused: true,
        used: false,
        invalid: false,
    },

    //加载执行
    onLoad: function (options) {
        this.getDataList();
    },

    //获取数据
    getDataList: function () {
        var page = this;
        var data = {}
        data['page'] = page.data.page;
        data['limit'] = page.data.limit;
        if (page.data.notused == true) {
            data['display'] = 'no_used';
        }
        if (page.data.used == true) {
            data['display'] = 'yes_used';
        }
        if (page.data.invalid == true) {
            data['display'] = 'invalid';
        }
        app.api.myCouponList(data, function (res) {
            if (res.status) {
                //防止网速过慢的问题
                let now_type = 'no_used';
                if (page.data.used == true) {
                    now_type = 'yes_used';
                }
                if (page.data.invalid == true) {
                    now_type = 'invalid';
                }
                if (now_type == res.data.q_type) {
                    if (res.data.page >= page.data.page) {
                        var c = page.data.listData.concat(res.data.list);
                        var p = res.data.page * 1 + 1;
                        var allpage = Math.ceil(res.data.count / res.data.limit * 1);
                        var lc = false;
                        var lo = true;
                        if (allpage < p) {
                            lc = true;
                        }
                        if (lc == true) {
                            lo = false;
                        }
                        var nodata = false;
                        if (c.length < 1) {
                            nodata = true;
                            lc = false;
                        }
                        page.setData({
                            listData: c,
                            page: p,
                            ajaxStatus: true,
                            loading: lo,
                            nodata: nodata,
                            loadingComplete: lc,
                            toView: ''
                        });
                    }
                }
            }
        });
    },

    //上拉加载
    lower: function () {
        var page = this;
        page.setData({
            toView: 'loading'
        });
        if (page.data.ajaxStatus && !page.data.loadingComplete) {
            page.setData({
                ajaxStatus: false
            });
            setTimeout(function () {
                page.getDataList()
            }, 1000);
        }
    },

    //未使用
    notused: function (e) {
        this.setData({
            used: false,
            invalid: false,
            notused: true,
            listData: [],
            page: 1
        });
        this.getDataList();
    },

    //已使用
    used: function (e) {
        this.setData({
            notused: false,
            invalid: false,
            used: true,
            listData: [],
            page: 1
        });
        this.getDataList();
    },

    //已失效
    invalid: function (e) {
        this.setData({
            notused: false,
            used: false,
            invalid: true,
            listData: [],
            page: 1
        });
        this.getDataList();
    },

    goIndex: function () {
        wx.switchTab({
            url: '/pages/index/index'
        });
    }
});