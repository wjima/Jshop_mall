//订单评价
var app = getApp(); //全局APP

Page({
  //页面数据
  data: {
    orderId: '',
    goodsList: [],
    images: {}, //已上传的图片
    scores: {}, //1~5想星好评
    textarea: {}, //输入框内容
    stars: {},
  },

  //加载执行
  onLoad: function (options) {
    this.setData({
      orderId: options.order_id,
    });
    this.getOrderData(options.order_id);
  },

  //获取数据
  getOrderData: function (order_id) {
    var page = this;
    var data = {
      order_id: order_id
    }
    app.api.orderDetails(data, function (res) {
      if (!res.status) {
        app.common.errorToBack(res.msg);
      }
      if (res.data.text_status !== 'pending_evaluate') {
        app.common.errorToBack('该订单状态有误不能评价');
      }
      var images = {};
      var scores = {};
      var stars = {};
      var textarea = {};
      for (var i = 0; i < res.data.items.length; i++) {
        var key = res.data.items[i].id
        images[key] = [];
        scores[key] = 5;   //默认五星好评
        stars[key] = [true, true, true, true, true];
        textarea[key] = '';
      }
      console.log(res.data.items);
      page.setData({
        images: images,
        goodsList: res.data.items,
        textarea: textarea,
        scores: scores,
        stars: stars,
      });
    });
  },

  //图片上传
  uploadImage: function (e) {
    var page = this;
    var goods_id = e.target.dataset.goodsid;
    var num = app.config.image_max - page.data.images[goods_id].length;
    app.api.uploadImage(num, function(res){
      if(res.status){
        var img = page.data.images;
        var image = {
          url: res.data.url,
          id: res.data.image_id
        }
        img[goods_id].push(image);
        page.setData({
          images: img
        });
      }
    });
  },

  //删除图片
  delImage: function (e) {
    var page = this;
    var img = page.data.images;
    for (var key in img) {
      if (key == e.target.dataset.goodsid){
        img[key].splice(e.target.dataset.index, 1);
      }
    }
    page.setData({
      images: img
    });
  },

  //评分点击
  star: function (e) {
    var newData = [];
    var index = e.target.dataset.index;
    var goods_id = e.target.dataset.goodsid;
    var page = this;
    for (var i = 0; i < 5; i++) {
      if(i <= index){
        newData.push(true);
      }else{
        newData.push(false);
      }
    }
    var stars = this.data.stars;
    var scores = this.data.scores;
    stars[goods_id] = newData;
    scores[goods_id] = i+1;
    page.setData({
      stars: stars,
      scores:scores
    });
  },

  //评价点击
  evaluate: function (e) {
    var types = e.target.dataset.type;
    var goods_id = e.target.dataset.goodsid;
    var page = this;
    var praise = false, secondary = false, difference = false;
    var evaluate = page.data.evaluate;
    if (types == 'praise') {
      praise = true;
    } else if (types == 'secondary') {
      secondary = true;
    } else if (types == 'difference') {
      difference = true;
    }
    evaluate[goods_id] = {
      praise: praise,
      secondary: secondary,
      difference: difference
    }
    page.setData({
      evaluate: evaluate
    });
  },

  //输入框内容
  textarea: function (e) {
    var page = this;
    var goods_id = e.target.dataset.goodsid;
    var content = e.detail.value;
    var textarea = page.data.textarea;
    textarea[goods_id] = content;
    page.setData({
      textarea: textarea
    });
  },

  //提交
  submit: function () {
    var page = this;
    var data = {
      order_id: page.data.orderId,
      items: {},
    };
    for (var key in page.data.images) {
      data.items[key] = {
        images: page.data.images[key],
        score: page.data.scores[key],
        textarea: page.data.textarea[key],
      }
    }
    app.db.userToken(function (token) {
      app.api.orderEvaluate(data, function (res) {
        wx.showToast({
          title: res.msg,
          success: function () {
            wx.navigateBack({
              delta: 1
            });
          }
        });
      });
    });
  }
});