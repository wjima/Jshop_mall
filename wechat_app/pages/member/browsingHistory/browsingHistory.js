//我的足迹
const app = getApp(); //获取全局app.js

Page({
  //页面初始数据
  data: {
    startX: 0, //开始坐标
    startY: 0,
    goodsList: [], //商品列表
    page: 1, //当前页
    limit: 10, //每页显示几条
    ajaxStatus: true,
    loading: true,
    loadingComplete: false,
    toView: "",
    nodata: false
  },
  
  //加载执行
  onLoad: function (options) {
    this.getDataList();
  },

  //获取数据
  getDataList: function () {
    var page = this;
    var data = {}
    data['page'] = page.data.page;
    data['limit'] = page.data.limit;
    app.db.userToken(function (token) {
      app.api.getBrowsingHistory(data, function (res) {
        for(var i=0; i<res.data.list.length; i++){
          res.data.list[i].ctime = app.common.timeToDate(res.data.list[i].ctime);
        }
        var c = page.data.goodsList.concat(res.data.list);
        var p = res.data.page * 1 + 1;
        var allpage = Math.ceil(res.data.count / res.data.limit * 1);
        var lc = false;
        var lo = true;
        if (allpage < p) {
          lc = true;
        }
        if(lc == true) {
          lo = false;
        }
        var nodata = false;
        if (c.length < 1) {
          nodata = true;
          lc = false;
        }
        page.setData({
          goodsList: c,
          page: p,
          ajaxStatus: true,
          loading: lo,
          nodata: nodata,
          loadingComplete: lc,
          toView: ''
        });
      });
    });
  },

  //上拉加载
  lower: function () {
    var page = this;
    page.setData({
      toView: 'loading'
    });
    if (page.data.ajaxStatus && !page.data.loadingComplete) {
      page.setData({
        ajaxStatus: false,
      });
      setTimeout(function () {
        page.getDataList();
      }, 1000);
    }
  },

  //前往商品
  goods: function (e) {
    let ins = encodeURIComponent('id=' + e.currentTarget.dataset.id);
    wx.navigateTo({
        url: '../../goods/detail/detail?scene=' + ins
    });
  },

  //手指触摸动作开始 记录起点X坐标
  touchstart: function (e) {
    //开始触摸时 重置所有删除
    this.data.goodsList.forEach(function (v, i) {
      if (v.isTouchMove)//只操作为true的
        v.isTouchMove = false;
    })
    this.setData({
      startX: e.changedTouches[0].clientX,
      startY: e.changedTouches[0].clientY,
      goodsList: this.data.goodsList
    })
  },

  //滑动事件处理
  touchmove: function (e) {
    var that = this,
      index = e.currentTarget.dataset.index,//当前索引
      startX = that.data.startX,//开始X坐标
      startY = that.data.startY,//开始Y坐标
      touchMoveX = e.changedTouches[0].clientX,//滑动变化坐标
      touchMoveY = e.changedTouches[0].clientY,//滑动变化坐标
      //获取滑动角度
      angle = that.angle({ X: startX, Y: startY }, { X: touchMoveX, Y: touchMoveY });
    that.data.goodsList.forEach(function (v, i) {
      v.isTouchMove = false
      //滑动超过30度角 return
      if (Math.abs(angle) > 30) return;
      if (i == index) {
        if (touchMoveX > startX) //右滑
          v.isTouchMove = false
        else //左滑
          v.isTouchMove = true
      }
    })
    //更新数据
    that.setData({
      goodsList: that.data.goodsList
    })
  },

  //计算滑动角度
  angle: function (start, end) {
    var _X = end.X - start.X,
      _Y = end.Y - start.Y
    //返回角度 /Math.atan()返回数字的反正切值
    return 360 * Math.atan(_Y / _X) / (2 * Math.PI);
  },

  //删除事件
  del: function (e) {
    var page = this;
    app.db.userToken(function (token) {
      //移除渲染
      page.data.goodsList.splice(e.currentTarget.dataset.index, 1);
      var nodata = false;
      if(page.data.goodsList.length < 1){
        nodata = true;
      }
      page.setData({
        nodata: nodata,
        goodsList: page.data.goodsList
      });
      //移除数据库
      var data = {
        goods_ids: e.currentTarget.dataset.goodsid
      }
      app.api.delGoodsBrowsing(data, function (res) {
        if (res.status) {
          wx.showToast({
            title: res.msg
          });
        }
      });
    });
  },

  //收藏
  collection: function (e) {
    var page = this;
    app.db.userToken(function (token) {
      var data = {
        goods_id: e.currentTarget.dataset.goodsid
      }
      app.api.goodsCollection(data, function (res) {
        for (var k in page.data.goodsList) {
          if (page.data.goodsList[k].goods_id == e.currentTarget.dataset.goodsid) {
            if (res.msg == '收藏成功') {
              page.data.goodsList[k].isCollection = true;
            } else {
              page.data.goodsList[k].isCollection = false;
            }
          }
        }
        wx.showToast({
          title: res.msg
        });
      });
    });
  },
});