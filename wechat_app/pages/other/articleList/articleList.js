// pages/other/articleList/articleList.js
const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    id:0,
    list:[],
    mode: 'aspectFill'

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if (options.id) {
      //设置全局商品ID
      this.setData({
        id: options.id
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
    var data = {
      type_id: this.data.id,
      limit:100
    }
    var page = this;
    app.api.getarticleList(data, function (res) {
      for (var j = 0; j < res.data.list.length; j++) {
        res.data.list[j].utime = app.common.timeToDate(res.data.list[j].utime)
      }
      page.setData({
        list:res.data.list
      });
    });


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
  detail: function (e) {
    wx.navigateTo({
      url: '../article/article?id=' + e.target.dataset.id
    });
  }
})