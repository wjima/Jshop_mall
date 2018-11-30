//商品详情页
const app = getApp(); //获取全局app.js
var WxParse = require('../../../component/wxParse/wxParse.js'); //html转小程序代码

//当前页面
Page({
  //页面使用的数据
  data: {
    goodsId: '', //商品ID
    productId: '', //货品ID
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
    code: 0, //邀请码
    qrcode: '', //邀请二维码
    qrcodeErrorMsg: '小程序二维码生成失败，无法生成海报，请稍候尝试', //邀请二维码生成失败提示
    pop: false, //海报界面
    shareImage: '', //海报图片
    painting: {}, //海报生成参数
    share: false,
    nickname: '',
    avatar: ''
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
    onLoad: function (e) {
        //获取传入的数据
        let scene = decodeURIComponent(e.scene);
        let arr1 = scene.split('&');
        let invite = '';
        let id = '';
        for (var i = 0; i < arr1.length; i++) {
            let key = arr1[i].split("=")[0];
            if (key == 'invite') {
                invite = arr1[i].split("=")[1];
            }
            if (key == 'id') {
                id = arr1[i].split("=")[1];
            }
        }

        //记录被邀请
        if (invite != '') {
            wx.setStorageSync("invitecode", invite);
        }

        //获取商品ID
        if (id != '') {
            this.setData({
                goodsId: id
            });
        }
        this.getMyShareCode(); //获取我的推荐码
        this.getUserInfo(); //获取个人信息
    },

    //获取我的推荐码
    getMyShareCode: function () {
        let page = this;
        let userToken = wx.getStorageSync('userToken');
        if (userToken) {
            app.api.sharecode(function (e) {
                if (e.status) {
                    //获取邀请码成功
                    wx.setStorageSync("myInviteCode", e.data);
                    page.setData({
                        code: e.data
                    });
                }
                page.getQRCode();
            });
        }else{
            page.getQRCode();
        }
    },

    //获取用户信息
    getUserInfo: function () {
        let page = this;
        let userToken = wx.getStorageSync('userToken');
        if (userToken) {
            app.api.userInfo(function (res) {
                page.setData({
                    nickname: res.data.nickname,
                    avatar: res.data.avatar
                });
            });
        }
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
      token: token
    }
    var page = this;
    app.api.goodsInfo(data, function (res) {
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
            let path = '/pages/goods/detail/detail?scene=id=' + ins;
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

    //分享
    share: function () {
        this.setData({
            share: true
        });
    },

    //关闭分享
    shareclose:function(){
        this.setData({
            share: false
        });
    },

    //生成海报
    sharePop: function () {
        this.setData({
            share: false,
            pop: true
        });
        if (this.data.shareImage == '') {
            wx.showLoading({
                title: '生成中'
            });
            this.eventDraw();
        }
    },

    //获取邀请二维码
    getQRCode: function () {
        let page = this;
        let data = {
            'type': 'goods',
            'invite': page.data.code,
            'goods': page.data.goodsId
        }
        app.api.getQRCode(data, function (e) {
            if (e.status) {
                let url = app.config.api_url + e.data;
                page.setData({
                    qrcode: url
                });
            } else {
                page.setData({
                    qrcodeErrorMsg: e.msg
                });
            }
        });
    },

    //生成海报
    eventDraw: function () {
        //todo::头像和用户昵称需要授权获取
        let page = this;
        let avatar = page.data.avatar;
        let nickname = page.data.nickname;
        if (avatar == ''){
            avatar = '/static/images/default.png';
        }
        if (page.data.qrcode == '') {
            wx.showToast({
                title: page.data.qrcodeErrorMsg,
                icon: 'none',
                duration: 2000
            });
            page.clone();
            return false;
        }else{
            let qrcode = page.data.qrcode;
            this.setData({
                painting: {
                    width: 560,
                    height: 900,
                    clear: true,
                    views: [
                        {
                            type: 'image',
                            url: '/static/images/goods.png',
                            top: 0,
                            left: 0,
                            width: 560,
                            height: 900
                        },
                        {
                            type: 'image',
                            url: avatar,
                            top: 50,
                            left: 55,
                            width: 80,
                            height: 80
                        },
                        {
                            type: 'text',
                            content: '【' + nickname + '】',
                            fontSize: 24,
                            color: '#222222',
                            textAlign: 'left',
                            top: 60,
                            left: 140
                        },
                        {
                            type: 'text',
                            content: '分享给你一个商品',
                            fontSize: 24,
                            color: '#222222',
                            textAlign: 'left',
                            top: 95,
                            left: 150
                        },
                        {
                            type: 'image',
                            url: page.data.goodsImg[0],
                            top: 160,
                            left: 60,
                            width: 440,
                            height: 400
                        },
                        {
                            type: 'text',
                            content: '￥' + page.data.goodsInfo.product.price,
                            fontSize: 30,
                            lineHeight: 30,
                            color: '#ea1919',
                            textAlign: 'left',
                            top: 570,
                            left: 60,
                            bolder: true
                        },
                        {
                            type: 'text',
                            content: page.data.goodsInfo.name,
                            fontSize: 26,
                            lineHeight: 30,
                            color: '#222222',
                            textAlign: 'left',
                            top: 610,
                            left: 60,
                            bolder: true,
                            MaxLineNumber: 2,
                            width: 420,
                            breakWord: true,
                        },
                        {
                            type: 'text',
                            content: page.data.goodsInfo.brief,
                            fontSize: 24,
                            lineHeight: 28,
                            color: '#333333',
                            textAlign: 'left',
                            top: 680,
                            left: 60,
                            MaxLineNumber: 2,
                            width: 420,
                            breakWord: true,
                        },
                        {
                            type: 'image',
                            url: qrcode,
                            top: 785,
                            left: 120,
                            width: 100,
                            height: 100
                        },
                        {
                            type: 'text',
                            content: '长按识别小程序访问',
                            fontSize: 22,
                            color: '#333333',
                            textAlign: 'left',
                            top: 820,
                            left: 235
                        }
                    ]
                }
            });
        }
    },

    //获取图片
    eventGetImage: function (event) {
        wx.hideLoading()
        const { tempFilePath, errMsg } = event.detail
        if (errMsg === 'canvasdrawer:ok') {
            this.setData({
                shareImage: tempFilePath
            })
        }
    },

    //保存图片
    eventSave: function () {
        wx.saveImageToPhotosAlbum({
            filePath: this.data.shareImage,
            success(res) {
                wx.showToast({
                    title: '保存图片成功',
                    icon: 'success',
                    duration: 2000
                })
            }
        })
    },

    //关闭海报层
    clone: function () {
        this.setData({
            pop: false
        });
    },

    move: function () { },

  //添加商品浏览记录
  goodsHistory: function () {
    var data = {
      goods_id: this.data.goodsId
    }
    var page = this;
    app.api.goodsHistory(data, function (res) {
      //浏览记录添加成功
    });
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
        type: 2
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