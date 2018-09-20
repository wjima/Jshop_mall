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
  },

  //刷新执行
  onShow: function () {
    this.getNoticeData();
  },

  //获取数据
  getNoticeData: function () {
    var page = this;
    var data = {
      id: page.data.id
    }
    app.api.noticeInfo(data, function (res) {
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
  }  
});