
//把obj对象里的值覆盖到newobj里面
function deepCopy(newobj,obj) {
  if (typeof obj != 'object') {
    return obj;
  }
  for (var attr in obj) {
    var a = {};
    if (newobj[attr]){
      a = newobj[attr];
    }
    newobj[attr] = deepCopy(a,obj[attr]);
  }
  return newobj;
}

//跳转到登陆页面
function jumpToLogin() {
  var now_time = Date.parse(new Date());
  var value = wx.getStorageSync('jump_to_login');
  if(!value){
    value=0;
  }
  if((now_time - value)> 3000 ){
    wx.setStorageSync('jump_to_login', now_time); 
    wx.showToast({
      title: '请登录...',
      icon: 'success',
      duration: 2000,
      success: function (res) {
        wx.navigateTo({
          url: '/pages/member/login/level1/level1'
        });
      }
    })
  }else{
  }
}

//当出错的时候，显示错误信息，并且跳转
function errorToBack(msg = '出错了，请重试',delta=1){
  wx.showToast({
    title: msg,
    icon: 'success',
    duration: 2000,
  });
  if(delta > 0){
    setTimeout(function () {
      wx.navigateBack({
        delta: delta
      })
    }, 1000);
  }
}
//操作成功后，的提示信息
function successToShow(msg='保存成功', callback=function(){}){
  wx.showToast({
    title: msg,
    icon: 'success',
    duration: 2000,
  });
  setTimeout(function () {
    callback();
  }, 1500);
}


//时间戳转时间格式
function timeToDate(date) {
  var date = new Date(date * 1000);//如果date为13位不需要乘1000
  var Y = date.getFullYear() + '-';
  var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
  var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
  var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
  var m = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
  var s = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
  return Y + M + D + h + m + s;
}

//货币格式化
function formatMoney(number, places, symbol, thousand, decimal) {
  number = number || 0;
  places = !isNaN(places = Math.abs(places)) ? places : 2;
  symbol = symbol !== undefined ? symbol : "￥";
  thousand = thousand || ",";
  decimal = decimal || ".";
  var negative = number < 0 ? "-" : "",
    i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
  return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
}
function throttle(fn, context, delay) {
  clearTimeout(fn.timeoutId);
  fn.timeoutId = setTimeout(function () {
    fn.call(context);
  }, delay);
}

module.exports = {
  deepCopy:deepCopy,
  jumpToLogin: jumpToLogin,
  timeToDate: timeToDate,
  formatMoney: formatMoney,
  errorToBack: errorToBack,
  successToShow: successToShow,
  throttle: throttle
}
