//支付成功页面
var app = getApp(); 

Page({
  //页面初始数据
  data: {
    paymentId: '', //支付单号
    orderId: '', //订单号
    status: false, //支付状态
    money: 0, //支付金额
    paymentCode: '', //支付方式
    paymentType: 1, //支付类型
    rel: [] //支付详情
  },

  //加载执行
  onLoad: function (options) {
    var page = this;
    if(options.payment_id == 'null'){
      page.setData({
        orderId: options.order_id,
        status: true,
        money: 0,
        paymentCode: false,
        paymentType: false,
        rel: false
      });
    } else {
      var data = {
        payment_id: options.payment_id
      }
      app.db.userToken(function (token) {
        app.api.paymentInfo(data, function (res) {
          if (res.status) {
            var payName = page.getPaymentCodeName(res.data.payment_code);
            page.setData({
              paymentId: res.data.payment_id,
              status: true,
              money: res.data.money,
              paymentCode: payName,
              paymentType: res.data.type,
              rel: res.data.rel
            });
          } else {
            //支付失败
            page.setData({
              status: false
            });
          }
        });
      });
    }
  },

  //查看订单详情
  orderInfo: function (e) {
    wx.navigateTo({
      url: '../../member/order/orderDetail/orderDetail?order_id=' + e.target.dataset.id,
    });
  },

  //获取支付名称
  getPaymentCodeName: function (payment_code) {
    var name = '';
    switch(payment_code){
      case 'wechatpay':
        name = '微信支付';
        break;
      case 'alipay':
        name = '支付宝支付';
        break;
      case 'balancepay':
        name = '余额支付';
        break;
      default:
        name = '未知支付方式';
        break;
    }
    return name;
  }
});