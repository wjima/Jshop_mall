 //主页
const app = getApp(); //获取全局app.js

//当前页面
Page({
  //页面使用的数据
  data: {
    appTitle: '', //小程序标题
    imageUrl: '/static/images/share.png', //分享封面图
    indicatorDots: true, //商品轮播图底部圆点
    autoplay: true, //商品轮播图自动播放
    interval: 3000, //商品轮播图切换间隔
    duration: 500, //商品轮播图切换动画时间
    slideImg: [], //幻灯片广告数据
    notice: [], //公告数据
    coupon: [], //优惠券数据
    recommend: [], //店家推荐数据
    hotGoods: [], //热卖推荐商品数据
    scrollTop: [],
    group:[],//团购数据
    seckill:[],//秒杀数据
    hotPage: 1,
    hotLimit: 8,
    ajaxStatus: true,
    loadingComplete: false
  },

  //页面加载处理
  onLoad: function (e) {
    //记录被邀请
    let scene = decodeURIComponent(e.scene);
    let arr1 = scene.split('&');
    let invite = '';
    for (var i = 0; i < arr1.length; i++) {
        let key = arr1[i].split("=")[0];
        if (key == 'invite'){
            invite = arr1[i].split("=")[1];
        }
    }
    if (invite != '') {
        wx.setStorageSync("invitecode", invite);
    }

    this.slideImg(); //获取幻灯片广告数据
    this.notice(); //获取公告数据
    this.coupon(); //获取优惠券数据
    this.recommend(); //获取店家推荐数据
    this.hotGoods(); //获取热卖推荐数据
    this.getMyShareCode(); //获取我的推荐码
    this.groupList(); //获取精品团购数据
    this.seckillList(); //获取限时秒杀数据
  },

  //获取我的推荐码
  getMyShareCode: function () {
    app.api.sharecode(function (e) {
        if (e.status) {
            //获取邀请码成功
            wx.setStorageSync("myInviteCode", e.data);
        }
    });
  },

  //页面显示
  onShow: function () {
      let userToken = wx.getStorageSync('userToken');
      let myInviteCode = wx.getStorageSync('myInviteCode');
      if (userToken && !myInviteCode) {
          app.api.sharecode(function (e) {
              if (e.status) {
                  //获取邀请码成功
                  wx.setStorageSync("myInviteCode", e.data);
              }
          });
      }
  },

  //顶部搜索栏阴影效果
  scroll: function (e) {
    var that = this, scrollTop = that.data.scrollTop;
    that.setData({
      scrollTop: e.detail.scrollTop
    });
  },

  //获取幻灯片广告数据
  slideImg: function () {
    var page = this;
    //异步获取幻灯片广告数据
    app.api.adList('tpl1_slider', function (res) {
      page.setData({
        slideImg: res.data.list
      });
    });
  },

  //获取公告数据
  notice: function () {
    var page = this;
    //异步获取公告数据，因为公告要求有实时性，所以不缓存
    app.api.noticeList(function (res) {
      if(res.status){
        if (res.data){
          page.setData({
            notice: res.data
          });
        }
      }
    });
  },

  //跳转到公告详情
  goNotice: function (e) {
    wx.navigateTo({
      url: '../other/notice/notice?id=' + e.target.dataset.id
    });
  },
  
  //获取优惠券数据
  coupon: function () {
    var page = this;
    //异步获取公告数据，因为优惠券要求有实时性，所以不缓存
    app.api.couponList(function (res) {
      if (res.status) {
        page.setData({
          coupon: res.data
        });
      }
    });
  },

  //获取店家推荐数据
  recommend: function () {
    var page = this;
    var data = {
      where:{
        recommend:1
      },
      limit:6,
    };
    app.api.goodsList(data, function (res) {
      page.setData({
        recommend: res.data.list
      });
    });
  },

  //热卖推荐获取
  hotGoods: function (flag = false) {
    var page = this;
    page.setData({
      ajaxStatus: false
    });
    if(flag){
      page.setData({
        hotPage: 1,
        hotGoods: []
      });
    }
    var data = {
      where: {hot: 1},
      page: page.data.hotPage,
      limit: page.data.hotLimit
    };
    app.api.goodsList(data, function (res) {
      if(res.status){
        let hotPage = page.data.hotPage+1;
        let hotGoods = page.data.hotGoods.concat(res.data.list);

        let loadingComplete = false;
        if (res.data.list.length < page.data.hotLimit) {
          loadingComplete = true;
        }
        page.setData({
          hotPage: hotPage,
          hotGoods: hotGoods,
          loadingComplete: loadingComplete,
          ajaxStatus: true
        });
      }
    });
  },

  //获取团购数据 此处不分页
  groupList: function (flag = false) {
    var page = this;
    page.setData({
      ajaxStatus: false
    });
    if (flag) {
      page.setData({
        group: []
      });
    }
    var data = {
      type: 3
    };

    app.api.getGroup(data, function (res) {
      if (res.status) {
        let group = res.data;
        page.setData({
          group: group,
          ajaxStatus: true
        });
      }
      app.common.groupCountDown(page);
    });
  },
  //获取秒杀数据
  seckillList: function (flag = false) {
    var page = this;
    page.setData({
      ajaxStatus: false
    });
    if (flag) {
      page.setData({
        seckill: []
      });
    }
    var data = {
      type: 4
    };

    app.api.getGroup(data, function (res) {
      if (res.status) {
        let seckill = res.data;
        page.setData({
          seckill: seckill,
          ajaxStatus: true
        });
      }
      app.common.seckillCountDown(page);
    });
  },
  
  //跳转到团购/秒杀详情页面
  GroupDetail: function (e) {
    wx.navigateTo({
      url: '../goods/group/detail?id=' + e.currentTarget.dataset.id + '&groupid=' + e.currentTarget.dataset.groupid
    });
  },

    //跳转到商品详情页面
    goodsDetail: function (e) {
        let ins = encodeURIComponent('id=' + e.currentTarget.dataset.id);
        wx.navigateTo({
            url: '../goods/detail/detail?scene=' + ins
        });
    },
  
  //广告跳转
  slideDetail: function (e) {
    var types = e.target.dataset.type;
    var val = e.target.dataset.val;
    if (types == 1) {
      //URL

    } else if (types == 2) {
        //商品
        let ins = encodeURIComponent('id=' + val);
        wx.navigateTo({
            url: '../goods/detail/detail?scene=' + ins,
        });
    } else if (types == 3) {
      //文章
        wx.navigateTo({
            url: '../other/article/article?id=' + val,
        });
    } else if (types == 4) {
        wx.navigateTo({
            url: '../other/articleList/articleList?id=' + val,
        });
    }
  },

  //客服回调
  customerService: function (e) {},

  //领取优惠券
  getCoupon: function (e) {
    app.db.userToken(function (token) {
      var data = {
        promotion_id: e.currentTarget.dataset.id
      }
      app.api.getCoupon(data, function (res) {
        wx.showToast({
          title: res.msg,
        });
      });
    });
  },

  //前往全部分类
  allCategories: function () {
    // wx.navigateTo({
    //   url: '/pages/other/articleList/articleList?id=10003',
    // });
    wx.switchTab({
      url: '/pages/goods/classify/classify'
    });
  },

  //前往热门商品
  hotSale: function () {
    wx.navigateTo({
      url: '../goods/itemList/itemList?type=hot'
    });
  },

  //前往余额提现
  balance: function () {},

  //前往我的资料
  myInformation: function () {
    wx.navigateTo({
      url: '/pages/member/info/info'
    });
  },

  //前往我的订单
  myOrder: function () {
    wx.navigateTo({
      url: '/pages/member/order/orderList/orderList'
    });
  },

  //前往我的关注收藏
  myCollection: function () {
    wx.navigateTo({
      url: '/pages/member/collect/collect'
    });
  },

  //前往余额明细
  fineBalance: function () {},

  //前往会员充值
  recharge: function () {},

  // //搜索
  // search: function (e) {
  //   var key = e.detail.value;
  //   wx.navigateTo({
  //     url: '../goods/itemList/itemList?key=' + key
  //   });
  // },

  //页面隐藏执行
  // onHide: function () {
  //   this.setData({
  //     key: ''
  //   });
  // },

  //跳转搜索
  searchNav: function () {
    wx.navigateTo({
      url: '../goods/search/search'
    });
  },

  //下拉刷新
  onPullDownRefresh: function () {
    this.slideImg(); //获取幻灯片广告数据
    this.notice(); //获取公告数据
    this.coupon(); //获取优惠券数据
    this.recommend(); //获取店家推荐数据
    this.hotGoods(true); //获取热卖推荐数据
    wx.stopPullDownRefresh();
    this.groupList(); //获取精品团购数据
    this.seckillList(); //获取限时秒杀数据
  },
  
    //转发分享
    onShareAppMessage: function () {
        let page = this;
        let userToken = wx.getStorageSync('userToken');
        if (userToken) {
            let myInviteCode = wx.getStorageSync('myInviteCode');
            if (myInviteCode) {
                //缓存里面有邀请码
                let ins = encodeURIComponent('invite='+myInviteCode);
                let path = '/pages/index/index?scene='+ins;
                return {
                    title: page.data.appTitle,
                    path: path,
                    imageUrl: page.data.imageUrl
                }
            } else {
                let path = '/pages/index/index';
                return {
                    title: page.data.appTitle,
                    path: path,
                    imageUrl: page.data.imageUrl
                }
            }
        } else {
            //用户没有登录
            let path = '/pages/index/index';
            return {
                title: page.data.appTitle,
                path: path,
                imageUrl: page.data.imageUrl
            }
        }
    },

  //上拉加载
  onReachBottom: function () {
    if (!this.data.loadingComplete && this.data.ajaxStatus) {
      this.hotGoods();
    }
  },

  //返回列表
  storeList: function () {
    wx.removeStorageSync('site_token');
    wx.redirectTo({
      url: '../other/storeList/storeList'
    });
  }
});