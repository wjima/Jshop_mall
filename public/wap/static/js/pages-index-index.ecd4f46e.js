(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-index-index"],{"00ef":function(t,e,i){"use strict";var n=i("e7a2"),a=i.n(n);a.a},1720:function(t,e,i){var n=i("d49e");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("45277522",n,!0,{sourceMap:!1,shadowMode:!1})},"1bb3":function(t,e,i){"use strict";i.r(e);var n=i("9f66"),a=i("1bce");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("00ef");var s,r=i("f0c5"),c=Object(r["a"])(a["default"],n["b"],n["c"],!1,null,"2ef7babe",null,!1,n["a"],s);e["default"]=c.exports},"1bce":function(t,e,i){"use strict";i.r(e);var n=i("4caa"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},"4caa":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n={name:"redBag",components:{},props:{},data:function(){return{redBagShow:!0}},watch:{},computed:{},methods:{handleClose:function(){this.redBagShow=!1},handleBtn:function(){this.$emit("click"),this.redBagShow=!1}},created:function(){},mounted:function(){}};e.default=n},7047:function(t,e,i){"use strict";i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}));var n={jshop:i("cfd9").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"content",staticStyle:{"padding-top":"0upx"}},[i("jshop",{attrs:{jdata:t.pageData}}),t.copy?i("jihaiCopyright"):t._e(),i("v-uni-view",{staticClass:"service",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.showChat()}}},[i("v-uni-image",{staticClass:"icon",attrs:{src:"/static/image/seller-content.png",mode:""}})],1),t.redBagShow?i("red-bag",{on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.handleGet.apply(void 0,arguments)}}}):t._e()],1)},o=[]},"71a0":function(t,e,i){"use strict";i.r(e);var n=i("e7b0"),a=i.n(n);for(var o in n)"default"!==o&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e["default"]=a.a},"9f66":function(t,e,i){"use strict";var n;i.d(e,"b",(function(){return a})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}));var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:t.redBagShow,expression:"redBagShow"}],staticClass:"wrapper"},[i("v-uni-view",{staticClass:"modal-bg"}),i("v-uni-view",{staticClass:"rb-wrapper"},[i("v-uni-view",{staticClass:"rb-content",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.handleBtn.apply(void 0,arguments)}}}),i("v-uni-view",{staticClass:"close",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.handleClose.apply(void 0,arguments)}}},[i("v-uni-image",{staticClass:"img",attrs:{src:"/static/image/close.png"}})],1)],1)],1)},o=[]},b88a:function(t,e,i){"use strict";i.r(e);var n=i("7047"),a=i("71a0");for(var o in a)"default"!==o&&function(t){i.d(e,t,(function(){return a[t]}))}(o);i("d227");var s,r=i("f0c5"),c=Object(r["a"])(a["default"],n["b"],n["c"],!1,null,"018eb448",null,!1,n["a"],s);e["default"]=c.exports},c585:function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.modal-bg[data-v-2ef7babe]{position:absolute;width:100%;height:100%;top:0;left:0;background:rgba(0,0,0,.4)}.rb-wrapper[data-v-2ef7babe]{position:absolute;top:50%;left:50%;width:60%;height:%?600?%;-webkit-transform:translate3d(-50%,-50%,0);transform:translate3d(-50%,-50%,0);background:red;padding:%?40?%}.rb-wrapper .rb-content[data-v-2ef7babe]{height:100%}.rb-wrapper .close[data-v-2ef7babe]{position:absolute;bottom:%?-120?%;left:50%;margin-left:%?-30?%;width:%?60?%;height:%?60?%;-webkit-border-radius:50%;border-radius:50%;background:#ddd}.rb-wrapper .close .img[data-v-2ef7babe]{width:100%;height:100%}',""]),t.exports=e},d227:function(t,e,i){"use strict";var n=i("1720"),a=i.n(n);a.a},d49e:function(t,e,i){var n=i("24fb");e=n(!1),e.push([t.i,".cell-item[data-v-018eb448]{border:none}.cell-ft-text[data-v-018eb448]{font-size:%?22?%;color:#999}.service[data-v-018eb448]{width:%?80?%;height:%?80?%;background-color:#fff;-webkit-border-radius:50%;border-radius:50%;position:fixed;right:%?30?%;bottom:%?120?%;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-shadow:0 0 %?10?% #ccc;box-shadow:0 0 %?10?% #ccc;padding:0;z-index:996}.service .icon[data-v-018eb448]{width:%?60?%;height:%?60?%}.subscription-notice[data-v-018eb448]{background-color:#fff;-webkit-box-shadow:0 %?2?% %?18?% #eee;box-shadow:0 %?2?% %?18?% #eee;position:relative;z-index:99;height:%?66?%;line-height:%?66?%;padding:0 %?20?%;font-size:%?28?%;color:#333;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.subscription-notice>uni-view[data-v-018eb448]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.subscription-notice .icon[data-v-018eb448]{width:%?32?%;height:%?32?%;margin-right:%?10?%}.subscription-notice-btn[data-v-018eb448]{color:#4285f4}.subscription-notice-btn-close[data-v-018eb448]{color:#888;padding-left:10px;font-size:%?30?%}",""]),t.exports=e},e7a2:function(t,e,i){var n=i("c585");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=i("4f06").default;a("6f2d3af8",n,!0,{sourceMap:!1,shadowMode:!1})},e7b0:function(t,e,i){"use strict";var n=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a=n(i("cfd9")),o=n(i("174c")),s=n(i("b8c2")),r=n(i("1bb3")),c=i("19d5"),u={mixins:[c.goods],components:{jihaiCopyright:o.default,jshop:a.default,uniCountdown:s.default,redBag:r.default},data:function(){return{imageUrl:"/static/image/share_image.png",pageData:[],pageCode:"mobile_home",pintuan:[],redBagShow:!1,config:"",userInfo:{},kefupara:"",copy:!1,suTipStatus:!1,shareUrl:"/pages/share/jump"}},updated:function(){this.copy=!0},computed:{appTitle:function(){return this.$store.state.config.shop_name},shopMobile:function(){return this.$store.state.config.shop_mobile||0},suTip:function(){return this.suTipStatus}},onLoad:function(t){this.initData(),this.$store.state.config.shop_name&&uni.setNavigationBarTitle({title:this.$store.state.config.shop_name||""}),this.$common.isWeiXinBrowser()&&this.shareAll()},onShow:function(){},methods:{handleGet:function(){},destroyed:function(){window.removeEventListener("scroll",this.handleScroll)},goSearch:function(){uni.navigateTo({url:"./search"})},initData:function(){var t=this;this.$api.getPageConfig({code:this.pageCode},(function(e){1==e.status&&(t.pageData=e.data.items,setTimeout((function(){t.showLoad=!1}),600))})),this.$api.shopConfig((function(e){t.config=e}));var e=this;this.$db.get("userToken")&&this.$api.userInfo({},(function(t){t.status&&(e.userInfo=t.data)})),this.getShareUrl()},showChat:function(){var t=this;window._AIHECONG("ini",{entId:this.config.ent_id,button:!1,appearance:{panelMobile:{tone:"#FF7159",sideMargin:30,ratio:"part",headHeight:50}}}),window._AIHECONG("customer",{head:t.userInfo.avatar,"名称":t.userInfo.nickname,"手机":t.userInfo.mobile}),window._AIHECONG("showChat")},toSubscription:function(){this.$common.navigateTo("/pages/member/setting/subscription/index")},userIsSubscription:function(){var t=this,e=this.$db.get("userToken");e&&""!=e?this.$api.subscriptionIsTip((function(e){e.status?e.data?t.suTipStatus=!0:t.suTipStatus=!1:t.suTipStatus=!0})):this.suTipStatus=!0},toClose:function(){var t=this,e=this.$db.get("userToken");e&&""!=e?this.$api.subscriptionCloseTip((function(e){t.suTipStatus=!1})):this.suTipStatus=!1},getShareUrl:function(){var t=this,e={client:2,url:"/pages/share/jump",type:1,page:1},i=this.$db.get("userToken");i&&""!=i&&(e["token"]=i),this.$api.share(e,(function(e){t.shareUrl=e.data}))},shareAll:function(){var t={url:window.location.href},e=this;this.$api.getShareInfo(t,(function(t){t.status&&(e.$wx.config({debug:!1,appId:t.data.appId,timestamp:t.data.timestamp,nonceStr:t.data.nonceStr,signature:t.data.signature,jsApiList:["updateAppMessageShareData","updateTimelineShareData"]}),e.$wx.ready((function(){var t={title:"首页",desc:"首页",imgUrl:e.config.shop_default_image};e.$wx.updateAppMessageShareData(t),e.$wx.updateTimelineShareData(t)})))}))}},onPullDownRefresh:function(){this.initData(),uni.stopPullDownRefresh()},onShareAppMessage:function(){return{title:this.$store.state.config.share_title,imageUrl:this.$store.state.config.share_image,path:this.shareUrl}}};e.default=u}}]);