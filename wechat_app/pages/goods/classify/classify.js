//全部分类
const app = getApp(); //全局app

Page({
    //页面数据
    data: {
        class_list: [],
        all_cat: [],
        on_class: 0,
        content_height: 0,
        cate_style: app.config.cate_style,
        kefupara: [],//客服信息
    },

    //加载执行
    onShow: function (options) {
      this.serviceInfo();
        var page = this;

        //获取全部数据
        let all_cat = app.db.get('all_cat');
        if (all_cat) {
            //缓存有值
            let on_class = 0;
            if (all_cat[0].id) {
                on_class = all_cat[0].id;
            }
            let class_list = [];
            if (all_cat[0].child) {
                class_list = all_cat[0].child;
            }
            page.setData({
                all_cat: all_cat,
                on_class: on_class,
                class_list: class_list,
                cate_style: app.config.cate_style
            });
        } else {
            //缓存无值
            app.api.getAllCat(function (res) {
                if (res.status) {
                    let on_class = 0;
                    if (res.data[0].id) {
                        on_class = res.data[0].id;
                    }
                    let class_list = [];
                    if (res.data[0].child) {
                        class_list = res.data[0].child;
                    }
                    page.setData({
                        all_cat: res.data,
                        on_class: on_class,
                        class_list: class_list,
                        cate_style: app.config.cate_style
                    });

                    //存储缓存
                    app.db.set('all_cat', res.data);
                }
            });
        }

        // 设置content高度
        wx.getSystemInfo({
            success: function (res) {
                page.setData({
                    // second部分高度 = 窗口可使用高度 - 头部底部部分高度
                    content_height: res.windowHeight - res.windowWidth / 750 * 120
                });
            }
        });
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
        let page = this;
        let o_id = e.currentTarget.dataset.id;
        let all_cat = page.data.all_cat;
        let class_list = [];
        //page.getClassList(e.currentTarget.dataset.id);
        for (let i = 0; i < all_cat.length; i++) {
            if (all_cat[i].id == o_id) {
                if (all_cat[i].child) {
                    class_list = all_cat[i].child;
                } else {
                    class_list = [];
                }
            }
        }

        //设置当前样式
        page.setData({
            on_class: o_id,
            class_list: class_list
        });
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
    //     var key = e.detail.value;
    //     wx.navigateTo({
    //         url: '../itemList/itemList?key=' + key
    //     });
    // },

    //跳转搜索
    searchNav: function () {
        wx.navigateTo({
            url: '../search/search'
        });
    },
  //客服信息
  serviceInfo: function () {
    var that = this;
    wx.getUserInfo({
      success(res) {
        var userInfo = res.userInfo;
        userInfo.source = 'jshop云商';
        that.setData({
          kefupara: JSON.stringify(userInfo)
        });
      },
      fail: function (res) {
        var userInfo ={};
        userInfo['source'] = "jshop云商";
        that.setData({
          kefupara: JSON.stringify(userInfo)
        });
      }
    })
  },
    //页面隐藏执行
    onHide: function () {
        this.setData({
            key: ''
        });
    }
});