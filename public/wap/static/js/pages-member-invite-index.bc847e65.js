(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-member-invite-index"],{"25e1":function(t,i,e){var a=e("24fb");i=a(!1),i.push([t.i,".invite[data-v-3a14b077]{width:100%;height:100%;background:-webkit-linear-gradient(left,#4c21d2,#4864f8);background:linear-gradient(90deg,#4c21d2,#4864f8)}.invite-bg[data-v-3a14b077]{position:absolute;width:%?750?%;height:%?683?%;z-index:66}.invite-c[data-v-3a14b077]{position:relative;z-index:67;width:%?750?%;padding:0 %?30?%;top:%?488?%;background:-webkit-linear-gradient(left,#4c21d2,#4864f8);background:linear-gradient(90deg,#4c21d2,#4864f8)}.invite-w[data-v-3a14b077]{background-color:#fff;width:%?690?%;text-align:center;padding:%?40?% %?100?%;box-sizing:border-box;border-radius:%?30?%;margin-bottom:%?70?%;position:relative;top:%?-148?%}.invite-w-t[data-v-3a14b077]{width:70%;margin:0 auto;color:#fff;border-radius:%?50?%;font-size:%?30?%;box-sizing:border-box;padding:%?10?%;display:block;background:-webkit-linear-gradient(left,#5f2ef6,#b945dd);background:linear-gradient(90deg,#5f2ef6,#b945dd)}.invite-w-num[data-v-3a14b077]{color:#5f2ef6;display:block;font-size:%?36?%;margin-top:%?20?%}.invite-w-detail[data-v-3a14b077]{color:#666;font-size:%?24?%;line-height:1.5;margin-top:%?20?%}.invite-w-bot[data-v-3a14b077]{margin:%?20?% 0 %?50?%}.invite-w-bot>uni-view[data-v-3a14b077]{width:49%;display:inline-block}.invite-w-bot-ic[data-v-3a14b077]{width:%?48?%;height:%?48?%}.invite-w-bot-red[data-v-3a14b077]{font-size:%?24?%;color:#ca0400;display:block}.invite-w-bot-gray[data-v-3a14b077]{font-size:%?24?%;color:#acacac;display:block}.invite-w-t-blue[data-v-3a14b077]{color:#348dfc;font-size:%?30?%;margin-bottom:%?50?%;display:block}.invite-w-input[data-v-3a14b077]{font-size:%?30?%;border-bottom:1px solid #dadada;margin-bottom:%?50?%;color:#999}.invite-w-btn[data-v-3a14b077]{background:-webkit-linear-gradient(left,#4a6af9,#28c4ff);background:linear-gradient(90deg,#4a6af9,#28c4ff);color:#fff;width:50%;margin:0 auto;border-radius:%?50?%;font-size:%?30?%;padding:%?10?% 0}.invite-btn[data-v-3a14b077]{position:relative;top:%?-150?%;text-align:center;width:%?690?%}.share[data-v-3a14b077]{background-color:none;position:relative;width:%?98?%;height:%?98?%;display:inline-block;border-radius:50%;padding:0;margin:0 %?40?% %?40?%}.invite-btn uni-image[data-v-3a14b077]{width:%?98?%;height:%?98?%}",""]),t.exports=i},"60f0":function(t,i,e){"use strict";var a=e("ecb4"),n=e.n(a);n.a},7185:function(t,i,e){"use strict";e.r(i);var a=e("7e99"),n=e.n(a);for(var o in a)"default"!==o&&function(t){e.d(i,t,(function(){return a[t]}))}(o);i["default"]=n.a},"7e99":function(t,i,e){"use strict";Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var a=e("a9e0"),n={data:function(){return{code:"",money:0,number:0,is_superior:!1,inviteKey:"",imageUrl:"/static/image/share_image.png",shareUrl:"/pages/share/jump"}},computed:{appTitle:function(){return this.$store.state.config.shop_name}},onShow:function(){this.getInviteData(),this.ifwxl()},methods:{ifwxl:function(){this.ifwx=this.$common.isWeiXinBrowser()},getInviteData:function(){var t=this;this.$api.myInvite((function(i){t.code=i.data.code,t.money=i.data.money,t.number=i.data.number,t.is_superior=i.data.is_superior}))},toMoney:function(){this.$common.navigateTo("../balance/details?status=5")},toList:function(){this.$common.navigateTo("./list")},setMyInvite:function(){var t=this,i={code:this.inviteKey};this.$api.setMyInvite(i,(function(i){i.status?(t.$common.successToShow("邀请码填写成功"),t.is_superior=!0):t.$common.errorToShow(i.msg)}))},createPoster:function(){var t=this,i={page:1,type:3},e=this.$db.get("userToken");e&&(i.token=e),i.client=1,i.url=a.h5Url+"pages/share/jump",this.$api.share(i,(function(i){i.status?t.$common.navigateTo("/pages/share?poster="+encodeURIComponent(i.data)):t.$common.errorToShow(i.msg)}))},copyUrl:function(){var t={page:1,type:1},i=this.$db.get("userToken");i&&(t.token=i),t.client=1,t.url=a.h5Url+"pages/share/jump";var e=this;this.$api.share(t,(function(t){t.status?uni.setClipboardData({data:t.data,success:function(t){e.$common.successToShow("复制成功")},fail:function(t){e.$common.errorToShow("复制分享URL失败")}}):e.$common.errorToShow("复制分享URL失败")}))},getShareUrl:function(){var t=this,i={client:2,url:"/pages/share/jump",type:1,page:1},e=this.$db.get("userToken");e&&""!=e&&(i["token"]=e),this.$api.share(i,(function(i){t.shareUrl=i.data}))}},onShareAppMessage:function(){return{title:this.$store.state.config.share_title,imageUrl:this.$store.state.config.share_image,path:this.shareUrl}}};i.default=n},c26c:function(t,i,e){"use strict";e.r(i);var a=e("e3b8"),n=e("7185");for(var o in n)"default"!==o&&function(t){e.d(i,t,(function(){return n[t]}))}(o);e("60f0");var s,r=e("f0c5"),c=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,"3a14b077",null,!1,a["a"],s);i["default"]=c.exports},e3b8:function(t,i,e){"use strict";var a,n=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("v-uni-view",{staticClass:"content"},[e("v-uni-image",{staticClass:"invite-bg",attrs:{src:"/static/image/invite-bg.png",mode:""}}),e("v-uni-view",{staticClass:"invite-c"},[e("v-uni-view",{staticClass:"invite-w"},[e("v-uni-view",{staticClass:"invite-w-t"},[t._v("我的专属邀请码")]),e("v-uni-text",{staticClass:"invite-w-num"},[t._v(t._s(t.code))]),e("v-uni-view",{staticClass:"invite-w-detail"},[t._v("快去分享您的邀请码吧，让更多的好友加入到【"+t._s(t.appTitle)+"】，您也可以获得丰厚的奖励！")]),e("v-uni-view",{staticClass:"invite-w-bot"},[e("v-uni-view",{attrs:{bindtap:"commission"},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.toMoney.apply(void 0,arguments)}}},[e("v-uni-image",{staticClass:"invite-w-bot-ic",attrs:{src:"/static/image/ic-earnings.png"}}),e("v-uni-text",{staticClass:"invite-w-bot-red"},[t._v("￥"+t._s(t.money)+"元")]),e("v-uni-text",{staticClass:"invite-w-bot-gray"},[t._v("邀请收益")])],1),e("v-uni-view",{attrs:{bindtap:"recommendlist"},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.toList.apply(void 0,arguments)}}},[e("v-uni-image",{staticClass:"invite-w-bot-ic",attrs:{src:"/static/image/ic-number.png"}}),e("v-uni-text",{staticClass:"invite-w-bot-red"},[t._v(t._s(t.number)+"人")]),e("v-uni-text",{staticClass:"invite-w-bot-gray"},[t._v("邀请人数")])],1)],1)],1),t.is_superior?t._e():e("v-uni-view",{staticClass:"invite-w"},[e("v-uni-text",{staticClass:"invite-w-t-blue"},[t._v("谁推荐你的？")]),e("v-uni-input",{staticClass:"invite-w-input",attrs:{placeholder:"请输入推荐人邀请码"},model:{value:t.inviteKey,callback:function(i){t.inviteKey=i},expression:"inviteKey"}}),e("v-uni-view",{staticClass:"invite-w-btn",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.setMyInvite.apply(void 0,arguments)}}},[t._v("提交")])],1),e("v-uni-view",{staticClass:"invite-btn"},[e("v-uni-button",{directives:[{name:"show",rawName:"v-show",value:!t.ifwx,expression:"!ifwx"}],staticClass:"share btn",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.copyUrl()}}},[e("v-uni-image",{attrs:{src:"/static/image/ic-link.png"}})],1),e("v-uni-button",{staticClass:"share btn",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.createPoster()}}},[e("v-uni-image",{attrs:{src:"/static/image/ic-img.png"}})],1)],1)],1)],1)},o=[];e.d(i,"b",(function(){return n})),e.d(i,"c",(function(){return o})),e.d(i,"a",(function(){return a}))},ecb4:function(t,i,e){var a=e("25e1");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=e("4f06").default;n("576f6613",a,!0,{sourceMap:!1,shadowMode:!1})}}]);