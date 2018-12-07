
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


//操作失败的提示信息
function errorToShow(msg = '操作失败', callback = function () { }) {
  wx.showToast({
    title: msg,
    icon: 'none',
    duration: 1500,
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
//团购/秒杀列表倒计时
function groupCountDown(that) {
  var group = that.data.group;
    var nowTime = new Date().getTime();
    for (var i = 0; i < group.length; i++){
      var startTime = group[i].stime * 1000 || [];
      if (startTime - nowTime>0){
        group[i].lasttime = '即将开始';
      }else{
        var endTime = group[i].etime * 1000 || [];
        var total_micro_second = endTime - nowTime || [];
        if (total_micro_second <= 0) {
          group[i].lasttime = '已经结束';
        } else {
          group[i].lasttime = dateformat(total_micro_second)
        }
      }
    }
    
    // 渲染倒计时时钟
    that.setData({
      group: group
    });
    setTimeout(function () {
      total_micro_second -= 1000;
      groupCountDown(that);
    }, 1000)
  }

//秒杀列表倒计时
function seckillCountDown(that) {
  var seckill = that.data.seckill;
  var nowTime = new Date().getTime();
  for (var i = 0; i < seckill.length; i++) {
    var endTime = seckill[i].etime * 1000 || [];
    var total_micro_second = endTime - nowTime || [];
    if (total_micro_second <= 0) {
      seckill[i].lasttime = '已经结束';
    } else {
      seckill[i].lasttime = dateformat(total_micro_second)
    }
  }
  // 渲染倒计时时钟
  that.setData({
    seckill: seckill
  });
  setTimeout(function () {
    total_micro_second -= 1000;
    seckillCountDown(that);
  }, 1000)
}

//团购/秒杀详情倒计时
function groupDetailCountDown(that) {
  var goodsInfo = that.data.goodsInfo;
  var nowTime = new Date().getTime();
  var endTime = goodsInfo.etime * 1000 || [];
    var total_micro_second = endTime - nowTime || [];
    if (total_micro_second <= 0) {
      goodsInfo.lasttime = '已经结束';
    } else {
      goodsInfo.lasttime = dateformat(total_micro_second)
    }
  
  // 渲染倒计时时钟
  that.setData({
    goodsInfo: goodsInfo
  });
  setTimeout(function () {
    total_micro_second -= 1000;
    groupDetailCountDown(that);
  }, 1000)
}
 // 时间格式化输出，如11:03 25:19 每1s都会调用一次
 function dateformat(micro_second) {
    var time = {};
    // 总秒数
    var second = Math.floor(micro_second / 1000);
    // 天数
    time.day = PrefixInteger(Math.floor(second / 3600 / 24),2);
    // 小时
    time.hour = PrefixInteger(Math.floor(second / 3600 % 24),2);
    // 分钟
    time.minute = PrefixInteger(Math.floor(second / 60 % 60),2);
    // 秒
    time.second = PrefixInteger(Math.floor(second % 60),2);
    return time;
 }

//不足位数前面补0
function PrefixInteger(num, length) {
  return (Array(length).join('0') + num).slice(-length); 
}
module.exports = {
  deepCopy:deepCopy,
  jumpToLogin: jumpToLogin,
  timeToDate: timeToDate,
  formatMoney: formatMoney,
  errorToBack: errorToBack,
  successToShow: successToShow,
  throttle: throttle,
  groupCountDown: groupCountDown,
  groupDetailCountDown: groupDetailCountDown,
  seckillCountDown: seckillCountDown,
  errorToShow: errorToShow
}
