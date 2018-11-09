//订单列表
var app = getApp(); //全局APP

Page({
  //页面初始数据
  data: {
    all: true,
    pendingpayment: false,
    pendingdelivery: false,
    goodstobereceived: false,
    tobeevaluated: false,
    order: [], //订单列表
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

  //加载执行
  onLoad: function (e) {
    this.setData({
      etype: e.type
    });
  },

  //刷新页面
  onShow: function () {
    var page = this;
    switch (page.data.etype) {
      case 'all': //全部订单列表
        page.all();
        break;
      case 'pendingpayment': //待支付订单列表
        page.pendingpayment();
        break;
      case 'pendingdelivery': //待发货订单列表
        page.pendingdelivery();
        break;
      case 'goodstobereceived': //待收货订单列表
        page.goodstobereceived();
        break;
      case 'tobeevaluated': //待评价订单列表
        page.tobeevaluated();
        break;
      default:
        page.all(); //全部订单列表
        break;
    }
  },

  //全部订单
  all: function (e) {
    this.setData({
      order: [],
      page: 1,
      all: true,
      pendingpayment: false,
      pendingdelivery: false,
      goodstobereceived: false,
      tobeevaluated: false,
      status: 'all',
      etype: 'all'
    });
    this.getOrderList();
  },

  //待支付订单
  pendingpayment: function (e) {
    this.setData({
      order: [],
      page: 1,
      all: false,
      pendingpayment: true,
      pendingdelivery: false,
      goodstobereceived: false,
      tobeevaluated: false,
      status: 1,
      etype: 'pendingpayment'
    });
    this.getOrderList();
  },

  //待发货订单
  pendingdelivery: function (e) {
    this.setData({
      order: [],
      page: 1,
      all: false,
      pendingpayment: false,
      pendingdelivery: true,
      goodstobereceived: false,
      tobeevaluated: false,
      status: 2,
      etype: 'pendingdelivery'
    });
    this.getOrderList();
  },

  //待收货订单
  goodstobereceived: function (e) {
    this.setData({
      order: [],
      page: 1,
      all: false,
      pendingpayment: false,
      pendingdelivery: false,
      goodstobereceived: true,
      tobeevaluated: false,
      status: 3,
      etype: 'goodstobereceived'
    });
    this.getOrderList();
  },

  //待评价订单
  tobeevaluated: function (e) {
    this.setData({
      order: [],
      page: 1,
      all: false,
      pendingpayment: false,
      pendingdelivery: false,
      goodstobereceived: false,
      tobeevaluated: true,
      status: 4,
      etype: 'tobeevaluated'
    });
    this.getOrderList();
  },

  //获取订单数据
  getOrderList: function () {
    var page = this;
    var data = {};
    app.db.userToken(function (token) {
      data['page'] = page.data.page;
      data['limit'] = page.data.limit;
      if(page.data.status != 'all'){
        data['status'] = page.data.status;
      }
      app.api.orderList(data, function (res) {
        var orderList = page.dataFormat(res.data.list);
        var c = page.data.order.concat(orderList);
        var p = res.data.page*1+1;
        var allpage = Math.ceil(res.data.count / res.data.limit * 1);
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
          loadingComplete: lc,
          toView: ''
        });
      });
    });
  },

  //数据格式处理
  dataFormat: function (data) {
    for (var i = 0; i < data.length; i++) {
      var countnum = 0
      for (var j = 0; j < data[i].items.length; j++) {
        countnum += data[i].items[j].nums;
      }
      data[i].countnum = countnum;
    }
    //todo:货品市场价、货品图片、货品规格
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

  //刷新页面
  refresh: function () {
    var page = this;
    if (page.data.all) {
      page.all();
    } else if (page.data.pendingpayment) {
      page.pendingpayment();
    } else if (page.data.pendingdelivery) {
      page.pendingdelivery();
    } else if (page.data.goodstobereceived) {
      page.goodstobereceived();
    } else if (page.data.tobeevaluated) {
      page.tobeevaluated();
    }
  },

  //取消订单
  cancelOrder: function (e) {
    var page = this;
    app.db.userToken(function (token) {
      wx.showModal({
        title: '确认取消订单？',
        content: '您确认取消订单：' + e.target.dataset.id + '吗？',
        success: function (ee) {
          if (ee.confirm) {
            var data = {
              order_ids: e.target.dataset.id
            }
            app.api.cancelOrder(data, function (res) {
              wx.showToast({
                title: res.msg,
                duration: 3000,
                complete: function () {
                  page.refresh();
                }
              });
            });
          }
        }
      });
    });
  },

  //确认签收
  confirm: function (e) {
    var page = this;
    var data = {
      order_id: e.target.dataset.id
    }
    app.db.userToken(function (token) {
      app.api.confirm(data, function (res) {
        wx.showModal({
          title: '确认签收成功，现在去评价？',
          content: '确认签收订单：' + res.data + '成功，现在去评价订单？',
          success: function (res) {
            if (res.confirm) {
              wx.navigateTo({
                  url: '../comment/comment?order_id=' + e.target.dataset.id,
              });
            } else if (res.cancel) {
              page.refresh();
            }
          }
        })
      });
    });
  },

  //立即付款
  payment: function (e) {
    var data = {
      order_id: e.target.dataset.id,
      order_amount: e.target.dataset.amount
    }
    wx.navigateTo({
      url: '../../../cart/cashierDesk/cashierDesk?data=' + JSON.stringify(data),
    });
  },

  //查看订单
  showOrder: function (e) {
    var order_id = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: '../orderDetail/orderDetail?order_id=' + order_id
    });
  },

  //申请售后
  aftermarket: function (e) {
    var order_id = e.target.dataset.id;
    wx.navigateTo({
      url: '../aftersales/add?order_id=' + order_id
    });
  },

  //去评价
  evaluation: function (e) {
    var order_id = e.target.dataset.id;
    wx.navigateTo({
      url: '../comment/comment?order_id=' + order_id,
    });
  }
});