const app = getApp(); //获取全局app.js
var date = new Date();
Page({
  data: {
    nickname: '' ,
    birthday: "选择出生日期",
    sex: 0,
    endtime: date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() ,
    objectSex:[
      {
        id:3,
        name:'未知'
      },
      {
        id: 1,
        name: '男'
      },
      {
        id: 2,
        name: '女'
      },
    ],
    
  },
  //页面加载处理
  onLoad: function () {
    var page = this;
    app.api.userInfo(function (res) {
      if (res.status) {
        var the_sex = 0;
        if(res.data.sex == 3){
          the_sex = 0;
        }else{
          the_sex = res.data.sex;
        }
        if(res.data.birthday == null){
          res.data.birthday = '请选择';
        }
        page.setData({
          nickname : res.data.nickname,
          sex : the_sex,
          birthday: res.data.birthday
        });
      }else{
        //报错了

      }
    });
  },
  //提交按钮
  showTopTips: function () {
    if(this.data.nickname == ''){
      wx.showToast({
        title: '请输入昵称',
        icon: 'success',
        duration: 2000
      })
      return false;
    }
    var the_sex = this.data.sex;
    if (the_sex== 0) {
      the_sex = 3;
    }
    var data = {
      sex:the_sex,
      birthday:this.data.birthday,
      nickname: this.data.nickname
    };
    app.api.userEditInfo(data,function (res) {
      if (res.status) {
        app.common.successToShow();
      } else {
        app.common.errorToBack(res.msg,0);
      }
    });

  },
  nicknameChange: function (e) {
    this.setData({
      nickname: e.detail.value
    })
  },
  //生日选择后的触发事件
  bindDateChange: function (e) {
    this.setData({
      birthday: e.detail.value
    })
  },
  //性别选择后的触发事件
  bindSexChange: function (e) {
    this.setData({
      sex: e.detail.value,
    })
  },
  
});