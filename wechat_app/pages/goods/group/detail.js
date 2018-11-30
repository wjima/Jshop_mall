//商品详情页
const app = getApp(); //获取全局app.js
var WxParse = require('../../../component/wxParse/wxParse.js'); //html转小程序代码

//当前页面
Page({
  //页面使用的数据
  data: {
    goodsId: '', //商品ID
    productId: '', //货品ID
    groupId: '', //货品ID
    nums: 1, //购买数量
    goodsImg: [], //商品图片
    indicatorDots: true, //商品轮播图底部圆点
    autoplay: true, //商品轮播图自动播放
    interval: 3000, //商品轮播图切换间隔
    duration: 1000, //商品轮播图切换动画时间
    selected: true, //图文详情切换
    selected1: false, //产品参数切换
    selected2: false, //买家评价切换
    goodsInfo: [], //商品信息
    goodsAllParameter: [], //商品产品参数
    goodsSpesDesc: [],
    minStatus: 'normal',
    maxStatus: 'normal',
    status: true, //是否可以购买
    isfav: false, //是否收藏
    commentList: [], //商品评价信息
    commentPage: 1, //评价第几页
    commentLimit: 2, //评价每页显示几条
    commentCount: 0, //评论总数
    commentAjaxStatus: true, //状态
    commentLoading: false, //显示加载中
    commentLoadingComplete: false, //加载全部
    commentNodata: false,
    mode: 'aspectFit',
  },

  //商品减一
  bindMinus: function () {
    var nums = this.data.nums;
    if (nums > 1) {
      nums--;
    }
    this.setData({
      nums: nums
    });
    this.setNumsData();
  },

  //商品加一
  bindPlus: function () {
    var nums = this.data.nums;
    nums++;
    this.setData({
      nums: nums
    });
    this.setNumsData();
  },

  //输入框改变
  bindManual: function (e) {
    var nums = e.detail.value;
    this.setData({
      nums: nums
    });
    this.setNumsData();
  },

  //数量改变设置
  setNumsData: function () {
    var nums = this.data.nums;
    var stock = this.data.goodsInfo.product.stock;
    var minStatus = 'normal';
    var maxStatus = 'normal';
    
    if (nums <= 1) {
      nums = 1;
      minStatus = 'disabled';
    }
    if (nums > stock) {
      nums = stock;
      maxStatus = 'disabled';
    }

    this.setData({
      nums: nums,
      minStatus: minStatus,
      maxStatus: maxStatus
    })
  },

  //图文详情切换
  selected: function (e) {
    this.setData({
      selected1: false,
      selected2: false,
      selected: true
    });
  },

  //产品参数切换
  selected1: function (e) {
    this.setData({
      selected2: false,
      selected: false,
      selected1: true
    });
  },

  //买家评价切换
  selected2: function (e) {
    this.setData({
      selected: false,
      selected1: false,
      selected2: true
    });
  },

  //页面加载处理
  onLoad: function (options) {
    //判断访问页面时候带的商品ID
    if (options.id && options.groupid) {
      //设置全局商品ID
      this.setData({
        goodsId: options.id,
        groupId: options.groupid
      });
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
    },

    //刷新页面
    onShow: function () {
        let userToken = app.db.get('userToken');
        let myInviteCode = wx.getStorageSync('myInviteCode');
        if (userToken && !myInviteCode) {
            app.api.sharecode(function (e) {
                if (e.status) {
                    //获取邀请码成功
                    wx.setStorage({
                        key: "myInviteCode",
                        data: e.data
                    });
                }
            });
        }
        this.getGoodsInfo();
        this.getGoodsComment();
    },

  //页面首次渲染完成执行
  onReady: function () {
    this.goodsHistory(); //添加商品浏览记录
  },

  //通过接口查询商品信息
  getGoodsInfo: function () {
    var token = app.db.get('userToken');
    var data = {
      id: this.data.goodsId,
      group_id: this.data.groupId,
      token: token
    }
    var page = this;
    app.api.groupInfo(data, function (res) {
      if (res.status) {
        if (res.data.length < 1){
          wx.showModal({
            title: '提示',
            content: '该商品不存在，请返回重新选择商品。',
            showCancel: false,
            complete: function () {
              wx.navigateBack(1);
            }
          });
        } else if (res.data.marketable != 1){
          wx.showModal({
            title: '提示',
            content: '该商品已下架，请返回重新选择商品。',
            showCancel: false,
            complete: function () {
              wx.navigateBack(1);
            }
          });
        } else {
          var st = true;
          if (res.data.product.stock < 1) {
            st = false;
          }
          var isfav = true;
          if (res.data.isfav == 'false') {
            isfav = false;
          }
          var spes_desc = page.getSpes(res.data.product);
          page.setData({
            goodsImg: res.data.album,
            goodsInfo: res.data,
            productId: res.data.product.id,
            goodsSpesDesc: spes_desc,
            status: st,
            isfav: isfav
          });
          WxParse.wxParse('detial', 'html', res.data.intro, page, 5); //解析商品图文详情
          page.goodsAllParameter(); //获取产品参数
          page.setNumsData(res.data);
          //刷新时间
          app.common.groupDetailCountDown(page);

        }
      }
    });
  },

  //继续加载
  commentLoading: function () {
    var page = this;
    if (page.data.commentAjaxStatus) {
      page.setData({
        commentAjaxStatus: false
      });
      page.getGoodsComment();
    }
  },

  //获取商品评价
  getGoodsComment: function () {
    var page = this;
    var data = {
      page: page.data.commentPage,
      limit: page.data.commentLimit,
      goods_id: page.data.goodsId
    };
    app.api.getGoodsComment(data, function (res) {
      for (var i = 0; i < res.data.list.length; i++) {
        res.data.list[i].ctime = app.common.timeToDate(res.data.list[i].ctime);
      }
      var c = page.data.commentList.concat(res.data.list);
      var p = res.data.page * 1 + 1;
      var allpage = Math.ceil(res.data.count / res.data.limit * 1);
      var lc = false;
      var lo = true;
      if (allpage < p) {
        lc = true;
      }
      if (lc == true) {
        lo = false;
      }
      var nodata = false;
      if (c.length < 1) {
        nodata = true;
        lc = false;
      }
      if (res.data.count > 999)
      {
        res.data.count = '999+';
      }
      page.setData({
        commentList: c,
        commentPage: p,
        commentCount: res.data.count,
        commentAjaxStatus: true,
        commentNodata: nodata,
        commentLoading: lo,
        commentLoadingComplete: lc,
      });
    });
  },

  //获取商品参数
  goodsAllParameter: function () {
    var data = {
      id: this.data.goodsId
    }
    var page = this;
    app.api.goodsParameter(data, function (res) {
      if (res.status) {
        page.setData({
          goodsAllParameter: res.data
        });
      }
    });
  },

  //商品分享功能
  onShareAppMessage: function () {
    let page = this;
    let userToken = wx.getStorageSync('userToken');
    if (userToken) {
        let myInviteCode = wx.getStorageSync('myInviteCode');
        if (myInviteCode) {
            let ins = encodeURIComponent('id=' + page.data.goodsInfo.id + '&invite=' + myInviteCode);
            let path = '/pages/goods/detail/detail?scene=' + ins;
            return {
                title: page.data.goodsInfo.name,
                imageUrl: page.data.goodsImg[0],
                path: path
            }
        } else {
            let ins = encodeURIComponent('id=' + page.data.goodsInfo.id);
            let path = '/pages/goods/detail/detail?scene=' + ins;
            return {
                title: page.data.goodsInfo.name,
                imageUrl: page.data.goodsImg[0],
                path: path
            }
        }
    } else {
        let ins = encodeURIComponent('id=' + page.data.goodsInfo.id);
        let path = '/pages/goods/detail/detail?scene=' + ins;
        return {
            title: page.data.goodsInfo.name,
            imageUrl: page.data.goodsImg[0],
            path: path
        }
    }
  },

  //添加商品浏览记录
  goodsHistory: function () {
    var data = {
      goods_id: this.data.goodsId
    }
    var page = this;
    // app.api.goodsHistory(data, function (res) {
    //   //浏览记录添加成功
    // });
  },

  //收藏和取消收藏商品
  goodsCollection: function () {
    var page = this;
    app.db.userToken(function (token) {
      var data = {
        goods_id: page.data.goodsId
      }
      app.api.goodsCollection(data, function (res) {
        var isfav = false;
        if (res.msg == "收藏成功") {
          isfav = true;
        }
        page.setData({
          isfav: isfav
        });
        wx.showToast({
          title: res.msg
        });
      });
    });
  },

  //前往购物车
  goCartPage: function () {
    wx.switchTab({
      url: '/pages/cart/cartNothing/cart'
    });
  },

  //加入购物车
  goodsAddCart: function () {
    var page = this;
    app.db.userToken(function (token) {
      //page.getNowProduct(); //获取当前选中的货品信息
      var data = {
        product_id: page.data.productId,
        nums: page.data.nums
      }
      app.api.goodsAddCart(data, function (res) {
        wx.showToast({
          title: res.msg
        });
      });
    });
  },

  //获取当前选中的货品
  // getNowProduct: function () {
  //   if (this.data.goodsSpesDesc) {
  //     var now = '';
  //     var data = this.data.goodsSpesDesc;
  //     for (var key in data) {
  //       for (var key2 in data[key].sku_value) {
  //         if (data[key].sku_value[key2].is_defalut == 1) {
  //           now = now + ',' + data[key].sku_name + ':' + data[key].sku_value[key2].name
  //         }
  //       }
  //     }
  //     now = now.substring(1);
  //     if (this.data.goodsAllProducts[now].id) {
  //       this.setData({
  //         productId: this.data.goodsAllProducts[now].id
  //       });
  //       return this.data.goodsAllProducts[now];
  //     } else {
  //       wx.showToast({
  //         title: '该货品不存在，请重新选择规格'
  //       });
  //       return false;
  //     }
  //   } else {
  //     //没有开启多规格不需要设置ProductID
  //     return false;
  //   }
  // },

  //立即购买
  buyNow: function () {
    var page = this;
    app.db.userToken(function (token) {
      //page.getNowProduct(); //获取当前选中的货品信息
      var data = {
        product_id: page.data.productId,
        nums: page.data.nums,
        type: 2,
      }
      app.api.goodsAddCart(data, function (res) {
        wx.navigateTo({
          url: '../../cart/firmOrder/firmOrder?data=' + JSON.stringify(res.data),
        });
      });
    });
  },
  
  //规格选择
  selectSku: function (obj) {
    var id = obj.target.dataset.key;
    if(id == "" || id == "0"){
      app.common.errorToBack("出错了",0);
      return false;
    }

    var data = {
      id: id,
    }
    var page = this;
    app.api.productInfo(data, function (res) {
      if(res.status){
        var st = true;
        if (res.data.stock < 1) {
          st = false;
        }
        var goodsInfo = page.data.goodsInfo;
        goodsInfo.product = res.data;
        page.setData({
          goodsSpesDesc: page.getSpes(res.data),
          productId: res.data.id,
          goodsInfo: goodsInfo,
          status: st,
          nums:1
        });
      }
    });
    //重新请求接口，获得接口信息
    // this.setData({
    //   goodsSpesDesc : spes_desc
    // });

    //获取当前规格渲染价格和库存
    // var s = this.getNowProduct();
    // if(s) {
    //   var goodsInfo = this.data.goodsInfo;
    //   goodsInfo.price = s.price;
    //   goodsInfo.mktprice = s.mktprice;
    //   goodsInfo.product.stock = s.stock;
    //   var st = true;
    //   var nu = 1;
    //   if (goodsInfo.product.stock < 1){
    //     st = false;
    //     nu = 0;
    //   }
    //   this.setData({
    //     goodsInfo: goodsInfo,
    //     status: st,
    //     nums: nu
    //   });
    // }
  },
  getSpes: function (product){
    if(!product.default_spes_desc){
      return [];
    }
    return product.default_spes_desc;
  },
  
  //客服功能
  customerService: function (e) {}
});