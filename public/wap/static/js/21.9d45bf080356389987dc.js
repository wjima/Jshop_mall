webpackJsonp([21],{"48D1":function(t,e){},PmIv:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s={data:function(){return{pid:"",mobile:"",pwd:"",code:"",countDown:!1}},created:function(){var t=this;this.GLOBAL.getStorage("user_token")&&this.$dialog.toast({mes:"你已经登录!",timeout:2e3,callback:function(){t.$router.go(-1)}}),this.$route.query.pid&&(this.GLOBAL.setStorage("pid",this.$route.query.pid),this.GLOBAL.setStorage("time",(new Date).getTime()+86400)),this.pid=this.GLOBAL.getStorage("pid")||0},computed:{checkMobile:function(){var t={};return this.mobile?/^1[345678]{1}\d{9}$/gi.test(this.mobile)?t.status=!0:(t.status=!1,t.msg="手机号格式不正确"):(t.status=!1,t.msg="请输入手机号"),t}},methods:{sendCode:function(){var t=this;this.checkMobile.status?(this.$dialog.loading.open("发送中..."),setTimeout(function(){t.$dialog.loading.close(),t.$api.sms({mobile:t.mobile,code:"reg"},function(e){e.status&&(t.countDown=!0,t.$dialog.toast({mes:e.msg,icon:"success",timeout:1300}))})},1e3)):this.$dialog.toast({mes:this.checkMobile.msg,timeout:1300})},reg:function(){var t=this;if(this.checkMobile.status)if(this.code)if(this.pwd){var e={mobile:this.mobile,code:this.code,password:this.pwd};this.pid&&(e.pid=this.pid),this.$api.reg(e,function(e){e.status&&(t.GLOBAL.setStorage("user_token",e.data),t.$dialog.toast({mes:"注册成功!",timeout:1e3,icon:"success",callback:function(){t.$router.replace("/index")}}))})}else this.$dialog.toast({mes:"请输入登录密码",timeout:1300});else this.$dialog.toast({mes:"请输入短信验证码",timeout:1300});else this.$dialog.toast({mes:this.checkMobile.msg,timeout:1300})},toLogin:function(){this.$router.replace({path:"/login"})}}},o={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"reg"},[t._m(0),t._v(" "),i("yd-cell-group",[i("yd-cell-item",[i("span",{attrs:{slot:"left"},slot:"left"},[t._v("手机号：")]),t._v(" "),i("yd-input",{ref:"tel",attrs:{slot:"right",type:"text",required:"","show-success-icon":!1,regex:"mobile",placeholder:"请输入手机号码"},slot:"right",model:{value:t.mobile,callback:function(e){t.mobile=e},expression:"mobile"}}),t._v(" "),i("yd-sendcode",{attrs:{slot:"right","storage-key":"register","init-str":"获取验证码",type:"primary"},nativeOn:{click:function(e){return t.sendCode(e)}},slot:"right",model:{value:t.countDown,callback:function(e){t.countDown=e},expression:"countDown"}})],1),t._v(" "),i("yd-cell-item",[i("span",{attrs:{slot:"left"},slot:"left"},[t._v("验证码：")]),t._v(" "),i("yd-input",{attrs:{slot:"right",placeholder:"请输入短信验证码"},slot:"right",model:{value:t.code,callback:function(e){t.code=e},expression:"code"}})],1),t._v(" "),i("yd-cell-item",[i("span",{attrs:{slot:"left"},slot:"left"},[t._v("密码：")]),t._v(" "),i("yd-input",{attrs:{slot:"right",type:"password",placeholder:"请输入密码"},slot:"right",model:{value:t.pwd,callback:function(e){t.pwd=e},expression:"pwd"}})],1)],1),t._v(" "),i("yd-button",{attrs:{size:"large",type:"danger"},nativeOn:{click:function(e){return t.reg(e)}}},[t._v("注册")]),t._v("\n    已有账号? "),i("span",{staticStyle:{color:"#10aeff"},on:{click:t.toLogin}},[t._v(" 立即登录")])],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"reg-img"},[e("img",{attrs:{src:i("vKgw")}})])}]};var l=i("VU/8")(s,o,!1,function(t){i("48D1")},null,null);e.default=l.exports}});
//# sourceMappingURL=21.9d45bf080356389987dc.js.map