// pages/member/remainingSum/withdrawCash/recharge.js
const app = getApp(); //获取全局app.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    balance: '0.00',
    recharge_money:0,//充值金额
    userInfo: [],//用户信息

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getUserInfo();
    this.setData({
      balance: options.money
    });
  },

  //获取用户信息
  getUserInfo: function () {
    var page = this;
    app.api.userInfo(function (res) {
      page.userInfo = res.data;
    });
  },

  //改变充值的钱
  change_money:function(e){
    this.setData({
      recharge_money: e.detail.value
    });
  },

  //微信支付
  showTopTips:function(){
    var page = this;
    if (page.data.recharge_money == '' || page.data.recharge_money<=0){
      app.common.errorToShow("请输入正确的充值金额");
      return false;
    }

    var data = {
      ids: page.userInfo.id,
      payment_code: 'wechatpay',
      payment_type: 2,
      params: { money:page.data.recharge_money}//充值金额
    };
    app.api.pay(data, function (res) {
      if (res.status) {
        wx.requestPayment({
          'timeStamp': '' + res.data.timeStamp,
          'nonceStr': res.data.nonceStr,
          'package': res.data.package,
          'signType': res.data.signType,
          'paySign': res.data.paySign,
          'success': function (e) {
            if (e.errMsg == "requestPayment:ok") {
              app.common.errorToBack('支付成功');
            } else if (res.errMsg == 'requestPayment:cancel') {
              app.common.errorToShow('支付已取消');
            }
          },
          'fail': function (e) {
            app.common.errorToShow('支付失败请重新支付');
          }
        });
      } else {
        app.common.errorToShow('支付订单出现问题，请返回重新操作');
      }
    });
  }
})