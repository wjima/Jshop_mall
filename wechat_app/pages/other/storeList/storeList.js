//店铺列表
const app = getApp(); //获取全局app.js

Page({
  //页面初始数据
  data: {
    store_list: [], //店铺列表
  },

  //页面加载
  onShow: function (options) {
    this.getAllStore();
  },

  //获取全部店铺
  getAllStore: function () {
    let page = this;
    //查询全部店铺
    app.api.getStoreByToken({}, function (res) {
      if (res.mode == '7881f454af469aa8') {
        wx.setStorageSync('site_token', 'C8B359');
        app.config.site_token = 'C8B359';
        wx.switchTab({
          url: '/pages/index/index'
        });
      } else {
        page.setData({
          store_list: res.data
        });
      }
    });
  },

  //店铺跳转
  gotoStore: function (e) {
    wx.setStorageSync('site_token', e.target.dataset.id);
    app.config.site_token = e.target.dataset.id;
    wx.switchTab({
      url: '/pages/index/index'
    });
  },
  
  //下拉刷新
  onPullDownRefresh: function () {
    this.getAllStore();
    wx.stopPullDownRefresh();
  },

  //上拉操作
  onReachBottom: function () {
  
  }
});