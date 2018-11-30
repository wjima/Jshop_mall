//订单详情
var app = getApp(); //全局APP

Page({
  //页面数据
  data: {
    orderId: '',
    order: {},
    status: 1,
    expressName: '',
    expressId: '',
    lastLogistics: '',
    lastLogisticsTime: '',
    payment_type: {
      'wechatpay': '微信支付',
      'alipay': '支付宝支付',
      'offline': '线下支付'
    }
  },

  //加载执行
  onLoad: function (options) {
    this.setData({
      orderId: options.order_id,
    });
    //this.getOrderData(options.order_id);
  },

  //刷新页面
  onShow: function () {
    this.getOrderData(this.data.orderId);
  },

  //获取数据
  getOrderData: function (order_id) {
    var page = this;
    var data = {
      order_id: order_id
    }
    app.db.userToken(function (token) {
      app.api.orderDetails(data, function (res) {
        res.data.ctime = app.common.timeToDate(res.data.ctime);
        res.data.payment_time = app.common.timeToDate(res.data.payment_time);
        res.data.payment_code = page.data.payment_type[res.data.payment_code];
        for(var k in res.data.items){
          if (res.data.items[k].promotion_list != "[]") {
            res.data.items[k].promotion_list = JSON.parse(res.data.items[k].promotion_list);
          }
        }
        if (res.data.delivery.length > 0) {
          res.data.delivery[0].ctime = app.common.timeToDate(res.data.delivery[0].ctime);
          res.data.delivery[0].confirm_time = app.common.timeToDate(res.data.delivery[0].confirm_time);
        }
        page.setData({
          order: res.data
        });
      });
    });
  },

  //去支付
  payment: function (e) {
    var data = {
      order_id: e.target.dataset.id,
      order_amount: e.target.dataset.amount
    }
    wx.navigateTo({
      url: '../../../cart/cashierDesk/cashierDesk?data=' + JSON.stringify(data),
    });
  },

  //申请售后
  customer: function (e) {
    var order_id = e.target.dataset.id;
    wx.navigateTo({
      url: '../aftersales/add?order_id=' + order_id
    });
  },

  //确认收货
  sign: function (e) {
    var page = this;
    var data = {
      order_id: e.target.dataset.id
    }
    app.db.userToken(function (token) {
      app.api.confirm(data, function (res) {
        wx.showModal({
          title: '确认签收成功，现在去评价？',
          content: '确认签收订单：'+res.data+'成功，现在去评价订单？',
          success: function (res) {
            if (res.confirm) {
              wx.navigateTo({
                  url: '../comment/comment?order_id=' + e.target.dataset.id,
              });
            } else if (res.cancel) {
              page.getOrderData(page.data.orderId);
            }
          }
        })
      });
    });
  },

  //去评价
  evaluate: function (e) {
    var order_id = e.target.dataset.id;
    wx.navigateTo({
      url: '../comment/comment?order_id=' + order_id,
    });
  },

  //返回列表
  returnList: function () {
    wx.navigateTo({
      url: '../orderList/orderList',
    });
  },

  //取消订单
  cancelOrder: function (e) {
    var page = this;
    var data = {
      order_ids: e.target.dataset.id
    }
    app.db.userToken(function (token) {
      app.api.cancelOrder(data, function (res) {
        wx.showToast({
          title: res.msg
        });
        setTimeout(function(){page.getOrderData(page.data.orderId)},1500);
      });
    });
  },

  //前往商品
  showGoods: function (e) {
    let goods_id = e.currentTarget.dataset.goods_id;
    let ins = encodeURIComponent('id=' + goods_id);
    wx.navigateTo({
        url: '../../../goods/detail/detail?scene=' + ins
    });
  },

    //前往物流信息查询
    logistics: function (e) {
        let code = e.currentTarget.dataset.code;
        let no = e.currentTarget.dataset.no;
        wx.navigateTo({
            url: '../logistics/logistics?code=' + code + '&no=' + no
        });
    }
});