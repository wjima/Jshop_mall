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
    nodata: false
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
    app.db.userToken(function (token) {
      app.api.myCouponList(data, function (res) {
        // for(var i=0;i<res.data.length;i++ ) {
        //   res.data[i].stime = app.common.timeToDate(res.data[i].stime);
        //   res.data[i].etime = app.common.timeToDate(res.data[i].etime);
        // }
        var c = page.data.listData.concat(res.data);
        var p = res.data.page * 1 + 1;
        var allpage = Math.ceil(res.data.count / res.data.limit * 1);
        var lc = true;
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
      });
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
});