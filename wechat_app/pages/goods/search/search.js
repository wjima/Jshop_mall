//商品列表
var app = getApp(); //全局APP

Page({
  //页面的初始数据
  data: {
    key: '',
    keys: [],
    navType: 'toNav'
  },

  //加载执行
  onShow: function () {
    var search_key = wx.getStorageSync('search_key');
    this.setData({
      key: '',
      keys: search_key
    });
  },

  //搜索
  search: function () {
    var keys = this.data.key;
    var search_key = wx.getStorageSync('search_key');
    if (!search_key) {
      search_key = [];
    }
    var flag = true;
    for(var key in search_key){
      if (search_key[key] == keys) {
        flag = false;
      }
    }
    if (flag) {
      search_key.unshift(keys);
    }
    wx.setStorageSync('search_key', search_key);
    wx.navigateTo({
      url: '../itemList/itemList?key=' + keys
    });
  },

  //关键字设置
  addkey: function (e) {
    this.setData({
      key: e.detail.value
    });
  },

  //删除操作
  delKey: function (e) {
    var key = e.target.dataset.key;
    var search_key = wx.getStorageSync('search_key');
    var index = search_key.indexOf(key);
    search_key.splice(index, 1);
    this.setData({
      keys: search_key
    });
    wx.setStorageSync('search_key', search_key);
  },

  //跳转操作
  toNav: function (e) {
    var key = e.target.dataset.key;
    wx.navigateTo({
      url: '../itemList/itemList?key=' + key
    });
  }
});