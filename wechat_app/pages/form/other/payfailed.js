// pages/other/pay/payfailed.js
const app = getApp(); //全局app
Page({

  /**
   * 页面的初始数据
   */
  data: {
    errorMsg:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    //todo 邀请人ID
    var type = options.type;
    if(type == '1'){
      this.data.errorMsg = '表单已过期';
    }
    //设置title名称
    wx.setNavigationBarTitle({
      title: this.data.errorMsg
    })
    this.setData({
      errorMsg: this.data.errorMsg 
    });

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
  //点击确定按钮跳转
  failedButton:function(){
    wx.switchTab({
      url: '../../index/index'
    })
  }
})