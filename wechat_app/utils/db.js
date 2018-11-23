var config = require('config.js');
var api = require('api.js');
var common = require('common.js');
/**
 * wxUserInfo     微信的用户信息，只有登录了之后，才有
 * userToken      用户token，用于判断是否登录
 * indexSlider    首页幻灯片数据
 * indexRecommend 首页店铺推荐
 * indexHotGoods  首页热卖商品
 */
//取值
function get(key) {
  try {
    return wx.getStorageSync(key);
  } catch (e) {
    return false;
  }
}
//赋值
function set(key,value,sync=false) {
  try {
    if (sync) {
      return wx.setStorageSync(key, value);
    } else {
      wx.setStorage({
        key: key,
        data: value
      })
    }
  } catch (e) {

  }
  
  
}

//获取用户token，如果缓存有，直接返回，如果没有，就先微信登陆，然后服务器登陆，最后返回token
function userToken(callback) {
  var token = get('userToken');
  if (token){
    callback(token);
  }else{
    //如果没有登陆，就去登陆
    common.jumpToLogin();
    //跳转到登陆页面
    // userLogin(function(res){
    //   if(res.status){
    //     callback(res.data);
    //   }
    // });
  }
}

//异步取得用户token,并设置,判断微信登录态  私有方法
// function userLogin(callback) {
//   api.login(function (res) {
//     if (res.status) {
//       set('userToken', res.data,true);
//       set('wxUserInfo', res.wxUserInfo, true);
//     }
//     callback(res);
//   });
// }

//异步取得幻灯片数据
// function indexSliderAsyn(callback) {
//   api.adList('tpl1_slider', function (res) {
//     if(res.status){
//       set('tpl1_slider', res.data.list);
//       callback(res.data);
//     }
//   });
// }
//首页店铺推荐数据
// function indexRecommendAsyn(callback) {
//   var data = {
//     where:{
//       recommend:1
//     },
//     limit:6,
//   };
//   api.goodsList(data, function (res) {
//     if (res.status) {
//       set('indexRecommend', res.data.list);
//       callback(res.data);
//     }
//   });
// }
//首页热卖商品数据
// function indexHotGoodsAsyn(callback) {
//   var data = {
//     where: {
//       hot: 1
//     },
//     limit: 6,
//   };
//   api.goodsList(data, function (res) {
//     if (res.status) {
//       set('indexHotGoods', res.data.list);
//       callback(res.data);
//     }
//   });
// }

module.exports = {
  get:get,
  set:set,
  userToken: userToken,
  // indexSliderAsyn: indexSliderAsyn,
  // indexRecommendAsyn: indexRecommendAsyn,
  // indexHotGoodsAsyn: indexHotGoodsAsyn,
}
