//公告
const app = getApp(); //获取全局app.js
var WxParse = require('../../../component/wxParse/wxParse.js'); //html转小程序代码

Page({
  //页面数据
  data: {
    id: 0,
    title: '',
    content: '',
    ctime: ''
  },

  //加载执行
  onLoad: function (options) {
    var id = options.id;
    this.setData({
      id: id
    });
    //店铺
    if (options.scene) {
        app.config.site_token = e.scene;
    }
    //被邀请码
    if (options.invite) {
        wx.setStorage({
            key: "invitecode",
            data: e.invite
        });
    }
    this.getMyShareCode(); //获取我的推荐码
  },

  //获取我的推荐码
  getMyShareCode: function () {
      var token = app.db.get('userToken');
      if (token) {
          app.api.sharecode(function (e) {
              let inviteCode = 0;
              if (e.status) {
                  //获取邀请码成功
                  wx.setStorage({
                      key: "myInviteCode",
                      data: e.data
                  });
              }
          });
      }
  },

  //刷新执行
  onShow: function () {
    this.getNoticeData();
  },

  //获取数据
  getNoticeData: function () {
    var page = this;
    var data = {
      article_id: page.data.id
    }
    app.api.getArticles(data, function (res) {
      if(!res.status){
        app.common.errorToBack(res.msg);
      }
      var content = res.data.content;
      var ctime = app.common.timeToDate(res.data.ctime);
      var title = res.data.title;
      page.setData({
        content: content,
        ctime: ctime,
        title: title
      });
      WxParse.wxParse('content', 'html', content, page, 5);
    });
  },

  //文章分享功能
  onShareAppMessage: function () {
      let page = this;
      let userToken = wx.getStorageSync('userToken');
      if (userToken) {
          let myInviteCode = wx.getStorageSync('myInviteCode');
          if (myInviteCode) {
              let path = '/pages/other/article/article?scene=' + wx.getStorageSync('site_token') + '&id=' + page.data.id + '&invite=' + myInviteCode;
              return {
                  title: page.data.title,
                  path: path
              }
          } else {
              let path = '/pages/other/article/article?scene=' + wx.getStorageSync('site_token') + '&id=' + page.data.id;
              return {
                  title: page.data.title,
                  path: path
              }
          }
      } else {
          let path = '/pages/other/article/article?scene=' + wx.getStorageSync('site_token') + '&id=' + page.data.id;
          return {
              title: page.data.title,
              path: path
          }
      }
  },
});