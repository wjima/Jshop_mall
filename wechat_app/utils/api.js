var config = require('config.js');
var common = require('common.js');
//需要token才能访问的数组
var methodToken = ['user.info', 'user.editinfo', 'cart.getlist', 'user.goodscollection', 'cart.add', 'cart.del', 'cart.setnums', 'user.saveusership', 'order.create', 'user.goodsbrowsing', 'user.pay', 'payments.getinfo', 'order.getorderlist', 'order.cancel', 'order.getorderstatusnum', 'user.delgoodsbrowsing', 'user.goodscollectionlist', 'coupon.getcoupon', 'coupon.usercoupon', 'order.details', 'order.confirm', 'user.orderevaluate', 'order.aftersalesstatus', 'order.addaftersales', 'order.aftersalesinfo', 'order.aftersaleslist', 'order.sendreship', 'order.iscomment', 'user.getuserdefaultship', 'user.changeavatar', 'user.issign', 'user.sign', 'user.pointlog', 'user.getdefaultbankcard', 'user.getbankcardlist', 'user.getbankcardinfo', 'user.cash', 'user.setdefaultbankcard', 'user.removebankcard', 'user.addbankcard', 'user.cashlist', 'user.balancelist', 'user.recommend', 'user.sharecode', 'user.getusership', 'user.vuesaveusership', 'user.removeship', 'user.setdefship', 'user.getshipdetail', 'user.editship', 'user.getuserpoint', 'store.isclerk', 'store.storeladinglist', 'store.getdefaultstore', 'store.ladingdel', 'store.ladinginfo', 'store.lading', 'coupon.getcouponkey', 'user.myinvite', 'user.activationinvite'];

//接口统一封装
function api(method,data,callback,show = true){
  //如果是需要登陆的，增加token
  if (methodToken.indexOf(method)>= 0){
    var userToken = wx.getStorageSync('userToken');
    if (!userToken){
      common.jumpToLogin();
    }else{
      data.token = userToken;
      data.method = method;
      post(data, callback, show);
    }
  }else{
    data.method = method;
    post(data, callback, show);
  }
  
}

//post请求
function post(data,callback,show){
  if(show){
    wx.showLoading({
      title: '载入中...'
    });
  }
  wx.request({
    url: config.api_url +'api.html',
    data: data,
    method: 'POST',
    success: function (res) {
      if(show){
        wx.hideLoading();
      }
      //这里做判断，如果不报错就返回，如果报错，就做错误处理
      if (res.data.status) {
        callback(res.data);
      } else {
        error(res.data, callback,data);
      }
    },
    fail: function (res) {
      if (show) {
        wx.hideLoading();
      }
      return {
        status: false,
        data: res.data,
        msg: '接口调用失败',
      };
    },
    complete: function(res) {
      
    }
  });
}

//post直接请求
function post2(path, data, callback, show = true) {
  if (show) {
    wx.showLoading({
      title: '载入中...'
    });
  }
  wx.request({
    url: config.api_url + 'api/' + path,
    data: data,
    method: 'POST',
    success: function (res) {
      if (show) {
        wx.hideLoading();
      }
      //这里做判断，如果不报错就返回，如果报错，就做错误处理
      if (res.data.status) {
        callback(res.data);
      } else {
        error(res.data, callback);
      }
    },
    fail: function (res) {
      if (show) {
        wx.hideLoading();
      }
      return {
        status: false,
        data: res.data,
        msg: '接口调用失败',
      };
    },
    complete: function (res) {

    }
  });
}

//接口错误信息处理
function error(res,callback,postData){
  switch (res.data) {
    case 14007:   //token验证失败，需要从新登录
      //判断是否是需要登陆的接口
      if (methodToken.indexOf(postData.method) >= 0) {
        common.jumpToLogin();
      }
      break;
    case 2:
      ;
      break;
    default:
      callback(res);
  }
}
// function jumpToLogin(){
//   var value = wx.getStorageSync('jump_to_login');
//   if (!value) {
//     wx.setStorageSync('jump_to_login', true);   //因为可能多个接口同时调用，所以这里要设置个状态位，保证登陆页面只显示一次，此值在登陆页面出去的时候，必须清空
//     wx.showToast({
//       title: '请登录...',
//       icon: 'success',
//       duration: 2000,
//       success: function (res) {
//         wx.navigateTo({
//           url: '/pages/member/login/login'
//         });
//       }
//     })
//   } 
// }

/**
 * 新的微信登陆方法
 * data的值报错
 *   mobile:手机号码
 *   code:手机短信验证码，可为空
 *  wx_code: 
 *   wx_iv: 
 *   wx_edata: 
 * 
 */
function login1(data,callback) {
  api('user.wxapplogin1', data, function (res2) {
    //res2.wxUserInfo = res1.userInfo;    //微信的用户信息保存起来
    callback(res2);
  });
}
function login2(data, callback) {
    //加入邀请码
    let pid = wx.getStorageSync('invitecode');
    if (pid) {
        data['pid'] = pid;
    }
    api('user.wxapplogin2', data, function (res2) {
        //res2.wxUserInfo = res1.userInfo;    //微信的用户信息保存起来
        callback(res2);
    });
}



//手机号码短信登陆
function smsLogin(data, callback) {
  data.platform = 2;        //手机号码必须制定登陆平台，否则，取到的token可能不是微信小程序平台的
  api('user.smslogin', data, function (res2) {
    callback(res2);
  });
}

//发送手机短信验证码
function sms(mobile,code, callback) {
  //code = 2是登陆，等1是注册,默认等于2
  var data = {
    mobile: mobile,
    code: code
  };
  api('user.sms', data, function (res2) {
    callback(res2);
  });
}


//登陆
//function login(callback){
  // wx.login({
  //   success: function (res) {
  //     if (res.code) {
  //       //这里取到了code，那么就调用wx.getUserInfo取用户数据
  //       wx.getUserInfo({
  //         withCredentials: true,
  //         success: function (res1) {
  //           //到这一步了，所有数据都取到了，去服务器端取token
  //           var data = {
  //             code: res.code,
  //             iv: res1.iv,
  //             edata: res1.encryptedData
  //           };
  //           api('user.wxapplogin', data, function(res2){
  //             res2.wxUserInfo = res1.userInfo;    //微信的用户信息保存起来
  //             callback(res2);
  //           });
  //         },
  //         fail: function (res) {
  //           //如果没有获取到用户信息，打开设置页面
  //           wx.openSetting({
  //             success: function(res) {
  //             }
  //           })
  //         }
  //       })
  //     } else {
  //       //wx.login成功，但是没有取到code
  //       wx.showToast({
  //         title: '用户授权失败wx.login',
  //         icon: 'warn',
  //         duration: 2000
  //       })
  //     }
  //   },
  //   fail: function (res) {
  //     //wx.login的fail
  //     wx.showToast({
  //       title: '用户授权失败wx.login',
  //       icon: 'warn',
  //       duration: 2000
  //     })
  //   }
  // });
//}
//用户信息接口
function userInfo(callback) {
  api('user.info', {}, function (res) {
    callback(res);
  });
}
//用户信息修改接口,nickname,sex,birthday
function userEditInfo(data,callback) {
  api('user.editinfo', data, function (res) {
    callback(res);
  });
}
//广告接口
function adList(code,callback) {
  var data = {
    code: code
  };
  api('advert.getAdvertList', data, function (res) {
    callback(res);
  });
}
//公告接口
function noticeList(callback) {
  api('notice.noticeList', {}, function (res) {
    callback(res);
  });
}
//获取公告内容
function noticeInfo(data, callback) {
  api('notice.noticeInfo', data, function (res) {
    callback(res);
  });
}
//优惠券接口
function couponList(callback) {
  var data = {
        limit: 3
  };
  api('coupon.couponlist', data, function (res) {
    callback(res);
  });
}
//商品列表查询接口
function goodsList(data,callback) {
  var newData = {};
  newData = common.deepCopy(newData,data);
  //把data里的where换成json
  if(data.where){
    newData.where = JSON.stringify(data.where);
  }
  //把排序换成字符串
  if(data.order){
    var sort = 'desc';
    if(data.order.sort){
      sort = data.order.sort;
    }
    newData.order = data.order.key+ ' ' + sort;
  }
  api('goods.getlist', newData, function (res) {
    callback(res);
  });
}
//购物车列表接口
function cartList(data, callback) {
  api('cart.getlist', data, function (res) {
    callback(res);
  });
}
//商品详情获取接口
function goodsInfo(data, callback) {
  api('goods.getdetial', data, function (res) {
    callback(res);
  });
}
//货品详情获取接口
function productInfo(data, callback) {
  api('goods.getproductinfo', data, function (res) {
    callback(res);
  });
}
//商品参数获取接口
function goodsParameter(data, callback) {
  api('goods.getgoodsparams', data, function (res) {
    callback(res);
  });
}
//商品浏览记录添加接口，此接口有点特殊，登陆状态在这里判断，而不是在基类里判断
function goodsHistory(data, callback) {
  //如果本地有token，那么就去增加浏览记录，否则，就不增加，极个别情况，登陆状态失效了，可能会跳转到登陆页面，此种情况不考虑
  var userToken = wx.getStorageSync('userToken');
  if (userToken) {
    data.token = userToken;
    api('user.addgoodsbrowsing', data, function (res) {
      callback(res);
    }, false);
  }
  
}
//商品收藏添加和移除接口
function goodsCollection(data, callback) {
  api('user.goodscollection', data, function (res) {
    callback(res);
  });
}
//添加购物车
function goodsAddCart(data, callback) {
  api('cart.add', data, function (res) {
    callback(res);
  });
}
//移除购物车
function goodsDelCart(data, callback) {
  api('cart.del', data, function (res) {
    callback(res);
  });
}
//获取区域ID
function getAreaId(data, callback) {
  api('user.getareaid', data, function (res) {
    callback(res);
  }, false);
}
//设置购物车数量
function setCartNum(data, callback) {
  api('cart.setnums', data, function (res) {
    callback(res);
  }, false);
}
//存储用户收货信息
function saveUserShip(data, callback) {
  api('user.saveusership', data, function (res) {
    callback(res);
  });
}
//创建订单
function createOrder(data, callback) {
  data.source = 3;
  api('order.create', data, function (res) {
    callback(res);
  });
}
//获取我的足迹
function getBrowsingHistory(data, callback) {
  api('user.goodsbrowsing', data, function (res) {
    callback(res);
  });
}
//去支付
function pay(data, callback) {
  api('user.pay', data, function (res) {
    callback(res);
  });
}
//获取支付类型
function getPaymentType(callback) {
  api('payments.getlist', {}, function (res) {
    callback(res);
  });
}
//支付单信息查询
function paymentInfo(data, callback) {
  api('payments.getinfo', data, function (res) {
    callback(res);
  });
}
//订单列表
function orderList(data, callback) {
  api('order.getorderlist', data, function (res) {
    callback(res);
  });
}
//取消订单
function cancelOrder(data, callback) {
  api('order.cancel', data, function (res) {
    callback(res);
  });
}
//获取不同订单的数量
function getOrderStatusNum(data, callback) {
  var newData = {
    ids: data
  }
  api('order.getorderstatusnum', newData, function (res) {
    callback(res);
  });
}
//删除足迹
function delGoodsBrowsing(data, callback) {
  api('user.delgoodsbrowsing', data, function (res) {
    callback(res);
  });
}
//获取关注列表
function goodsCollectionList(data, callback) {
  api('user.goodscollectionlist', data, function (res) {
    callback(res);
  });
}
//领取优惠券
function getCoupon(data, callback) {
  api('coupon.getcoupon', data, function (res) {
    callback(res);
  });
}
//获取我的优惠券列表
function myCouponList(data, callback) {
  api('coupon.usercoupon', data, function (res) {
    callback(res);
  });
}
//订单详情获取
function orderDetails(data, callback) {
  api('order.details', data, function (res) {
    callback(res);
  });
}
//确认收货
function confirm(data, callback) {
  api('order.confirm', data, function (res) {
    callback(res);
  });
}
//上传图片
function uploadImage(num,callback){
  wx.chooseImage({
    count:num,
    success: function (res) {
      var tempFilePaths = res.tempFilePaths
      for (var i = 0; i < tempFilePaths.length; i++) {
        wx.uploadFile({
          url: config.api_url + 'api.html',
          filePath: tempFilePaths[i],
          name: 'upfile',
          formData: {
            method: 'images.upload'
          },
          success: function (res) {
            var obj = JSON.parse(res.data);
            callback(obj);
          }
        });
      }
    }
  });
}
//提交订单评价
function orderEvaluate (data, callback) {
  api('user.orderevaluate', data, function (res) {
    callback(res);
  });
}
//获取商品评价信息
function getGoodsComment (data, callback) {
  api('goods.getgoodscomment', data, function (res) {
    callback(res);
  });
}

//获取订单的详细信息，包括售后信息
function aftersalesStatus (order_id, callback) {
  var data = {
    order_id:order_id,
  };
  api('order.aftersalesstatus', data, function (res) {
    callback(res);
  });
}

//添加售后单，发起一个售后信息
function addAftersales(data,callback){
  api('order.addaftersales', data, function (res) {
    callback(res);
  });
}

//售后单查看
function aftersalesInfo(id,callback){
  var data = {
    aftersales_id: id,
  };
  api('order.aftersalesinfo',data,function(res){
    callback(res);
  })
}
//获取售后列表
function getAftersalesList(data, callback) {
  api('order.aftersaleslist', data, function (res) {
    callback(res);
  });
}
//退货单，发送退货物流信息
function sendReship(data, callback) {
  api('order.sendreship', data, function (res) {
    callback(res);
  });
}

//判断能否评价
function isComment(data, callback) {
  api('order.iscomment', data, function (res) {
    callback(res);
  });
}

//获取文章
function getArticles(data, callback) {
  api('articles.getArticleDetail', data, function (res) {
    callback(res);
  });
}

//获取默认收货地址
function getDefaultShip(callback) {
  api('user.getuserdefaultship', {}, function (res) {
    callback(res);
  });
}

//获取分类列表
function getClassTop(callback) {
  api('categories.gettopcat', {}, function (res) {
    callback(res);
  });
}

//获取分类子类
function getClassChild(data, callback) {
  api('categories.getchildcat', data, function (res) {
    callback(res);
  });
}
//更换用户头像
function changeAvatar(avatar, callback) {
  var data = {
    avatar: avatar,
  };
  api('user.changeavatar', data, function (res) {
    callback(res);
  });
}
//判断是否签到
function isSign(callback) {
  api('user.issign', {}, function (res) {
      callback(res);
  });
}
//签到操作
function sign(callback) {
  api('user.sign', {}, function (res) {
      callback(res);
  });
}
//我的积分记录
function pointLog(callback) {
  api('user.pointlog', {}, function (res) {
      callback(res);
  });
}
//获取共享店铺列表
function getStoreByToken(data, callback) {
  post2('Common/getStoreInfo', {}, function(res) {
    callback(res);
  });
}
//获取店铺名称
function getStoreName(callback) {
  api('user.getstorename', {}, function (res) {
    callback(res);
  });
}
//获取店铺配置信息
function getSellerSetting(the_key,callback) {
  var data = {
    key: the_key,
  };
  api('user.getsellersetting', data, function (res) {
    callback(res);
  });
}
// 获取默认银行卡
function getUserDefaultBankCard(callback) {
    api('user.getdefaultbankcard', {}, function (res) {
        callback(res);
    });
}
//获取提现记录列表
function getCashList(data, callback) {
    api('user.cashlist', data, function (res) {
        callback(res);
    });
}
//获取银行卡列表
function getBankCardList(callback) {
    api('user.getbankcardlist', {}, function (res) {
        callback(res);
    });
}
//获取银行卡信息
function getCardInfo(id, callback) {
    let data = {
        id: id,
    };
    api('user.getbankcardinfo', data, function (res) {
        callback(res);
    });
}
//余额提现
function userCash(data, callback) {
    api('user.cash', data, function (res) {
        callback(res);
    });
}
//设置默认银行卡
function setDefaultBankCard(cardId, callback) {
    let data = {
        id: cardId
    };
    api('user.setdefaultbankcard', data, function (res) {
        callback(res);
    });
}
//删除银行卡
function delCard(cardId, callback) {
    let data = {
        id: cardId
    };
    api('user.removebankcard', data, function (res) {
        callback(res);
    });
}
//获取银行卡数据
function getBankCardOrganization(cardCode, callback) {
    let data = {
        card_code: cardCode
    }
    api('user.getbankcardorganization', data, function (res) {
        callback(res);
    }, false);
}
//添加银行卡
function addBankCard(data, callback) {
    api('user.addbankcard', data, function (res) {
        callback(res);
    });
}
//用户明细列表
function userBalance(data, callback) {
    api('user.balancelist', data, function (res) {
        callback(res);
    });
}
//用户推荐列表
function recommendList(data, callback) {
    api('user.recommend', data, function (res) {
        callback(res);
    });
}
//获取用户邀请码
function sharecode(callback) {
  var userToken = wx.getStorageSync('userToken');
  if (userToken) {
    var data = {
        token: userToken
    };
    api('user.sharecode', data, function (res) {
        callback(res);
    }); 
  }
}
//获取文章列表
function getarticleList(data,callback){
  api('articles.getArticleList', data, function (res) {
    callback(res);
  });
}
//获取收货地址列表
function getUserShip(callback) {
    api('user.getusership', {}, function (res) {
        callback(res);
    });
}
//存储收货地址
function addSaveAddress(data, callback) {
    api('user.vuesaveusership', data, function (res) {
        callback(res);
    });
}
//删除收货地址
function removeship(data, callback) {
    api('user.removeship', data, function (res) {
        callback(res);
    });
}
//设置默认收货地址
function setdefship(data, callback) {
    api('user.setdefship', data, function (res) {
        callback(res);
    });
}
//获取收货地址详情
function getshipdetail(data, callback) {
    api('user.getshipdetail', data, function (res) {
        callback(res);
    });
}
//编辑收货地址
function editship(data, callback) {
    api('user.editship', data, function (res) {
        callback(res);
    });
}
//获取用户积分
function getUserPoint(data, callback) {
    api('user.getuserpoint', data, function(res){
        callback(res);
    });
}
//判断是否是店员
function isClerk(callback) {
    api('store.isclerk', {}, function(res){
        callback(res);
    });
}
//店铺提货单列表
function ladingList(callback) {
    api('store.storeladinglist', {}, function(res){
        callback(res);
    });
}
//团购数据列表
function getGroup(data,callback) {
  api('group.getlist', data, function (res) {
      callback(res);
  });
}
//团购秒杀详情获取接口
function groupInfo(data, callback) {
  api('group.getgoodsdetial', data, function (res) {
    callback(res);
  });
}
//获取默认门店
function getDefaultStore(callback){
    api('store.getdefaultstore', {}, function (res){
        callback(res);
    });
}
//是否开启门店自提
function getStoreSwitch(callback){
    api('store.getstoreswitch', {}, function (res) {
        callback(res);
    });
}
//获取店铺列表
function getStoreList(data, callback){
    api('store.getstorelist', data, function (res){
        callback(res);
    });
}
//删除提货单
function ladingDel (data, callback) {
    api('store.ladingdel', data, function (res){
        callback(res);
    });
}
//获取提货单详情
function ladingInfo (data, callback) {
    api('store.ladinginfo', data, function(res){
        callback(res);
    });
}
//提货单核销
function lading(data, callback) {
    api('store.lading', data, function (res){
        callback(res);
    });
}
//是否开启积分
function isPoint(callback){
    api('user.ispoint', {}, function(res){
        callback(res);
    });
}
//输入优惠券码领取
function getCouponKey(data, callback){
    api('coupon.getcouponkey', data, function(res){
        callback(res);
    });
}
//获取我的邀请信息
function myInvite(callback){
    api('user.myinvite', {}, function(res){
        callback(res);
    });
}
//设置我的上级邀请人
function setMyInvite(data, callback){
    api('user.activationinvite', data, function (res){
        callback(res);
    });
}
//获取带参数的小程序二维码
function getQRCode(data, callback){
    api('store.getinviteqrcode', data, function(res){
        callback(res);
    });
}
//获取物流详情
function getLogisticsData(data, callback){
    api('order.logisticbyapi', data, function(res){
        callback(res);
    });
}

module.exports = {
  login1: login1,
  login2: login2,
  smsLogin:smsLogin,
  sms:sms,
  userInfo: userInfo,
  userEditInfo: userEditInfo,
  adList:adList,
  noticeList:noticeList,
  couponList: couponList,
  goodsList: goodsList,
  cartList: cartList,
  goodsInfo: goodsInfo,
  productInfo: productInfo,
  goodsParameter: goodsParameter,
  goodsHistory: goodsHistory,
  goodsCollection: goodsCollection,
  goodsAddCart: goodsAddCart,
  goodsDelCart: goodsDelCart,
  getAreaId: getAreaId,
  setCartNum: setCartNum,
  saveUserShip: saveUserShip,
  createOrder: createOrder,
  getBrowsingHistory: getBrowsingHistory,
  pay: pay,
  paymentInfo: paymentInfo,
  orderList: orderList,
  cancelOrder: cancelOrder,
  getOrderStatusNum: getOrderStatusNum,
  delGoodsBrowsing: delGoodsBrowsing,
  goodsCollectionList: goodsCollectionList,
  getCoupon: getCoupon,
  myCouponList: myCouponList,
  orderDetails: orderDetails,
  confirm: confirm,
  uploadImage: uploadImage,
  orderEvaluate: orderEvaluate,
  getGoodsComment: getGoodsComment,
  aftersalesStatus: aftersalesStatus,
  addAftersales: addAftersales,
  aftersalesInfo: aftersalesInfo,
  sendReship: sendReship,
  isComment: isComment,
  noticeInfo: noticeInfo,
  getPaymentType: getPaymentType,
  getArticles: getArticles,
  getDefaultShip: getDefaultShip,
  getClassTop: getClassTop,
  getClassChild: getClassChild,
  getAftersalesList: getAftersalesList,
  changeAvatar: changeAvatar,
  isSign: isSign,
  sign: sign,
  pointLog: pointLog,
  getStoreByToken: getStoreByToken,
  getStoreName: getStoreName,
  getSellerSetting: getSellerSetting,
  getCashList: getCashList,
  getUserDefaultBankCard: getUserDefaultBankCard,
  getBankCardList: getBankCardList,
  getCardInfo: getCardInfo,
  userCash: userCash,
  setDefaultBankCard: setDefaultBankCard,
  delCard: delCard,
  getBankCardOrganization: getBankCardOrganization,
  addBankCard: addBankCard,
  userBalance: userBalance,
  recommendList: recommendList,
  sharecode: sharecode,
  getarticleList: getarticleList,
  getUserShip: getUserShip,
  addSaveAddress: addSaveAddress,
  removeship: removeship,
  setdefship: setdefship,
  getshipdetail: getshipdetail,
  editship: editship,
  getUserPoint: getUserPoint,
  isClerk: isClerk,
  ladingList: ladingList,
  getGroup: getGroup,
  groupInfo: groupInfo,
  getDefaultStore: getDefaultStore,
  getStoreSwitch: getStoreSwitch,
  getStoreList: getStoreList,
  ladingDel: ladingDel,
  ladingInfo: ladingInfo,
  lading: lading,
  isPoint: isPoint,
  getCouponKey: getCouponKey,
  myInvite: myInvite,
  setMyInvite: setMyInvite,
  getQRCode: getQRCode,
  getLogisticsData: getLogisticsData
}