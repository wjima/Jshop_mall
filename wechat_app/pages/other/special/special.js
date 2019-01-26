const app = getApp(); //获取全局app.js

// pages/other/special/special.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    url:'',
  },
  //加载执行
  onLoad: function (options) {
    var url = decodeURIComponent(options.url);
    this.setData({
      url: url
    });
  }
})