//全部分类
const app = getApp(); //全局app

Page({
  //页面数据
  data: {
    top_class: [],
    class_list: [],
    on_class: 0,
    content_height: 0,
    cate_style: 3 //新增分类样式
  },

  //加载执行
  onLoad: function (options) {
    var page = this;
    app.api.getClassTop(function (res) {
      if (res.status) {
        page.setData({
          top_class: res.data,
          cate_style: res.cate_style, //新增分类样式
          on_class: res.data[0].id
        });
        
        if (res.cate_style == 3){
          page.getClassList(res.data[0].id);
        }
      }
    });  

    // 设置content高度
    wx.getSystemInfo({
      success: function (res) {
        page.setData({
          // second部分高度 = 窗口可使用高度 - 头部底部部分高度
          content_height: res.windowHeight - res.windowWidth / 750 * 120
        })
      }
    })       
  },

  //获取一级分类下的子分类列表
  getClassList: function (parent_id) {
    var page = this;
    var data = {
      parent_id: parent_id
    }
    app.api.getClassChild(data, function (res) {
      if (res.status) {
        page.setData({
          class_list: res.data
        });
      }
    });
  },

  //切换分类
  topclass: function (e) {
    var page = this;
    page.getClassList(e.currentTarget.dataset.id);
    //设置当前样式
    page.setData({
      on_class: e.currentTarget.dataset.id
    })
  },

  //前往商品分类列表
  goodsList: function (event) {
    wx.navigateTo({
      url: '../itemList/itemList?id=' + event.currentTarget.dataset.id
    });
  },
  
  //客服回调
  customerService: function (e) {},

  //搜索
  // search: function (e) {
  //   var key = e.detail.value;
  //   wx.navigateTo({
  //     url: '../itemList/itemList?key=' + key
  //   });
  // },

  //跳转搜索
  searchNav: function () {
    wx.navigateTo({
      url: '../search/search'
    });
  },

  //页面隐藏执行
  onHide: function () {
    this.setData({
      key: ''
    });
  }
})