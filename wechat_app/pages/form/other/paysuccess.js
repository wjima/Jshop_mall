// pages/other/pay/paysuccess.js
const app = getApp(); //全局app
Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    //设置title名称
    wx.setNavigationBarTitle({
      title: '支付成功'
    })

    var page = this;
    if (options.payment_id == 'null') {
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
    }
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  },

  //获取支付名称
  getPaymentCodeName: function (payment_code) {
    var name = '';
    switch (payment_code) {
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
  },
  //todo 跳转详情页
  successButton: function() {
    wx.switchTab({
      url: '../../index/index'
    })
  }
})