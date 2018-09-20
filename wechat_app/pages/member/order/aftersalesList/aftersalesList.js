//订单列表
var app = getApp(); //全局APP

Page({
  //页面初始数据
  data: {
    order: [], //订单列表
    page: 1, //当前页
    limit: 10, //每页显示几条
    ajaxStatus: true,
    loading: false,
    loadingComplete: false,
    nodata: false,
    toView: ''
  },

  //刷新页面
  onShow: function () {
    this.getOrderList();
  },

  //获取订单数据
  getOrderList: function () {
    var page = this;
    var data = {};
    app.db.userToken(function (token) {
      data['page'] = page.data.page;
      data['limit'] = page.data.limit;
      app.api.getAftersalesList(data, function (res) {
        var orderList = page.dataFormat(res.data.list);
        var c = page.data.order.concat(orderList);
        var p = res.data.page*1+1;
        var allpage = res.data.total_page;
        var lc = false;
        var lo = true;
        if(allpage < p){
          lc = true;
        }
        if (lc == true) {
          lo = false;
        }
        var nodata = false;
        if(c.length < 1){
          nodata = true;
          lc = false;
        }
        page.setData({
          order: c,
          page: p,
          ajaxStatus: true,
          nodata: nodata,
          loading: lo,
          loadingComplete: lc
        });
      });
    });
  },

  //数据格式处理
  dataFormat: function (data) {
    for (var i = 0; i < data.length; i++) {
      var countnum = 0
      for (var j = 0; j < data[i].order.items.length; j++) {
        countnum += data[i].order.items[j].nums;
      }
      data[i].countnum = countnum;
    }
    return data;
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
        page.getOrderList()
      }, 1000);
    }
  },

  //查看详情
  showOrder: function (e) {
    var aftersales_id = e.target.dataset.id;
    wx.navigateTo({
      url: '../aftersales/edit?aftersales_id=' + e.target.dataset.id,
    });
    //todo::待完善
  }
});