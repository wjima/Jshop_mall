(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-member-distribution-order"],{"06c5":function(t,e,a){"use strict";a("a630"),a("fb6a"),a("d3b7"),a("25f0"),a("3ca3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=o;var i=n(a("6b75"));function n(t){return t&&t.__esModule?t:{default:t}}function o(t,e){if(t){if("string"===typeof t)return(0,i.default)(t,e);var a=Object.prototype.toString.call(t).slice(8,-1);return"Object"===a&&t.constructor&&(a=t.constructor.name),"Map"===a||"Set"===a?Array.from(t):"Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)?(0,i.default)(t,e):void 0}}},"19d5":function(t,e,a){"use strict";a("c975"),a("b64b"),Object.defineProperty(e,"__esModule",{value:!0}),e.tools=e.checkLogin=e.jumpBackPage=e.goBack=e.goods=e.orders=void 0;var i={mounted:function(){},methods:{orderDetail:function(t){this.$common.navigateTo("/pages/member/order/orderdetail?order_id="+t)},toPay:function(t){this.$common.navigateTo("/pages/goods/payment/index?order_id="+t+"&type=1")},toEvaluate:function(t){this.$common.navigateTo("/pages/member/order/evaluate?order_id="+t)},showExpress:function(t,e){var a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"",i=encodeURIComponent("code="+t+"&no="+e+"&add="+a);this.$common.navigateTo("/pages/member/order/express_delivery?params="+i)}}};e.orders=i;var n={mounted:function(){},methods:{goodsDetail:function(t){this.$common.navigateTo("/pages/goods/index/index?id="+t)},goodsList:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},e="/pages/classify/index";Object.keys(t).length&&(e=this.$common.builderUrlParams(e,t)),this.$common.navigateTo(e)},groupDetail:function(t,e){this.$common.navigateTo("/pages/goods/index/group?id="+t+"&group_id="+e)},pintuanDetail:function(t,e){e?this.$common.navigateTo("/pages/goods/index/pintuan?id="+t+"&team_id="+e):this.$common.navigateTo("/pages/goods/index/pintuan?id="+t)}}};e.goods=n;var o={onBackPress:function(t){if("navigateBack"===t.from)return!1;var e=["/pages/cart/index/index","/pages/member/index/index"],a=this.$store.state.redirectPage;return e.indexOf(a)>-1?(this.$store.commit({type:"redirect",page:""}),uni.switchTab({url:"/pages/index/index"}),!0):void 0},backBtn:function(){var t=getCurrentPages();t.length>1?uni.navigateBack({delta:1}):uni.switchTab({url:"/pages/index/index"})}};e.goBack=o;var r={methods:{handleBack:function(){var t=this.$store.state.redirectPage;console.log(t),this.$store.commit({type:"redirect",page:""});var e=["/pages/index/index","/pages/member/index/index"];e.indexOf(t)>-1?uni.switchTab({url:t}):t?uni.redirectTo({url:t}):uni.switchTab({url:"/pages/index/index"})}}};e.jumpBackPage=r;var d={methods:{checkIsLogin:function(){uni.showToast({title:"请先登录！",icon:"none",duration:800,success:function(t){setTimeout((function(){uni.hideToast(),uni.navigateTo({url:"/pages/login/login/index1"})}),200)}})}}};e.checkLogin=d;var s={methods:{copyData:function(t){var e=this;uni.setClipboardData({data:t,success:function(){e.$common.errorToShow("复制成功")}})}}};e.tools=s},"287c":function(t,e,a){"use strict";a.r(e);var i=a("7723"),n=a("9ade");for(var o in n)"default"!==o&&function(t){a.d(e,t,(function(){return n[t]}))}(o);a("9bb8");var r,d=a("f0c5"),s=Object(d["a"])(n["default"],i["b"],i["c"],!1,null,"537c27a8",null,!1,i["a"],r);e["default"]=s.exports},2909:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=s;var i=d(a("6005")),n=d(a("db90")),o=d(a("06c5")),r=d(a("3427"));function d(t){return t&&t.__esModule?t:{default:t}}function s(t){return(0,i.default)(t)||(0,n.default)(t)||(0,o.default)(t)||(0,r.default)()}},3427:function(t,e,a){"use strict";function i(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}Object.defineProperty(e,"__esModule",{value:!0}),e.default=i},4778:function(t,e,a){var i=a("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\r\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\r\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\r\n/* 颜色变量 */\r\n/* 行为相关颜色 */\r\n/* 文字基本颜色 */\r\n/* 背景颜色 */\r\n/* 边框颜色 */\r\n/* 尺寸变量 */\r\n/* 文字尺寸 */\r\n/* 图片尺寸 */\r\n/* Border Radius */\r\n/* 水平间距 */\r\n/* 垂直间距 */\r\n/* 透明度 */\r\n/* 文章场景相关 */.uni-load-more[data-v-537c27a8]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;height:%?80?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center}.uni-load-more__text[data-v-537c27a8]{font-size:%?26?%;color:#999}.uni-load-more__img[data-v-537c27a8]{height:24px;width:24px;margin-right:10px}.uni-load-more__img > uni-view[data-v-537c27a8]{position:absolute}.uni-load-more__img > uni-view uni-view[data-v-537c27a8]{width:6px;height:2px;-webkit-border-top-left-radius:1px;border-top-left-radius:1px;-webkit-border-bottom-left-radius:1px;border-bottom-left-radius:1px;background:#999;position:absolute;opacity:.2;-webkit-transform-origin:50%;transform-origin:50%;-webkit-animation:load-data-v-537c27a8 1.56s ease infinite;animation:load-data-v-537c27a8 1.56s ease infinite}.uni-load-more__img > uni-view uni-view[data-v-537c27a8]:nth-child(1){-webkit-transform:rotate(90deg);transform:rotate(90deg);top:2px;left:9px}.uni-load-more__img > uni-view uni-view[data-v-537c27a8]:nth-child(2){-webkit-transform:rotate(180deg);transform:rotate(180deg);top:11px;right:0}.uni-load-more__img > uni-view uni-view[data-v-537c27a8]:nth-child(3){-webkit-transform:rotate(270deg);transform:rotate(270deg);bottom:2px;left:9px}.uni-load-more__img > uni-view uni-view[data-v-537c27a8]:nth-child(4){top:11px;left:0}.load1[data-v-537c27a8],\r\n.load2[data-v-537c27a8],\r\n.load3[data-v-537c27a8]{height:24px;width:24px}.load2[data-v-537c27a8]{-webkit-transform:rotate(30deg);transform:rotate(30deg)}.load3[data-v-537c27a8]{-webkit-transform:rotate(60deg);transform:rotate(60deg)}.load1 uni-view[data-v-537c27a8]:nth-child(1){-webkit-animation-delay:0s;animation-delay:0s}.load2 uni-view[data-v-537c27a8]:nth-child(1){-webkit-animation-delay:.13s;animation-delay:.13s}.load3 uni-view[data-v-537c27a8]:nth-child(1){-webkit-animation-delay:.26s;animation-delay:.26s}.load1 uni-view[data-v-537c27a8]:nth-child(2){-webkit-animation-delay:.39s;animation-delay:.39s}.load2 uni-view[data-v-537c27a8]:nth-child(2){-webkit-animation-delay:.52s;animation-delay:.52s}.load3 uni-view[data-v-537c27a8]:nth-child(2){-webkit-animation-delay:.65s;animation-delay:.65s}.load1 uni-view[data-v-537c27a8]:nth-child(3){-webkit-animation-delay:.78s;animation-delay:.78s}.load2 uni-view[data-v-537c27a8]:nth-child(3){-webkit-animation-delay:.91s;animation-delay:.91s}.load3 uni-view[data-v-537c27a8]:nth-child(3){-webkit-animation-delay:1.04s;animation-delay:1.04s}.load1 uni-view[data-v-537c27a8]:nth-child(4){-webkit-animation-delay:1.17s;animation-delay:1.17s}.load2 uni-view[data-v-537c27a8]:nth-child(4){-webkit-animation-delay:1.3s;animation-delay:1.3s}.load3 uni-view[data-v-537c27a8]:nth-child(4){-webkit-animation-delay:1.43s;animation-delay:1.43s}@-webkit-keyframes load-data-v-537c27a8{0%{opacity:1}100%{opacity:.2}}',""]),t.exports=e},6005:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=o;var i=n(a("6b75"));function n(t){return t&&t.__esModule?t:{default:t}}function o(t){if(Array.isArray(t))return(0,i.default)(t)}},6053:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={name:"uni-load-more",props:{status:{type:String,default:"more"},showIcon:{type:Boolean,default:!0},color:{type:String,default:"#999"},contentText:{type:Object,default:function(){return{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"}}}},data:function(){return{}}};e.default=i},"63d6":function(t,e,a){var i=a("4778");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("4f06").default;n("75788625",i,!0,{sourceMap:!1,shadowMode:!1})},"6b75":function(t,e,a){"use strict";function i(t,e){(null==e||e>t.length)&&(e=t.length);for(var a=0,i=new Array(e);a<e;a++)i[a]=t[a];return i}Object.defineProperty(e,"__esModule",{value:!0}),e.default=i},"6fc3":function(t,e,a){var i=a("24fb");e=i(!1),e.push([t.i,".type-c .cell-group[data-v-3c1e6cb2]{padding:%?10?% 0;margin-top:0}.type-c .cell-item[data-v-3c1e6cb2]{border:none;min-height:%?70?%;padding:0 %?26?% 0 0}.type-c .cell-item .red-price[data-v-3c1e6cb2]{font-size:%?50?%}.type-c .cell-item .color-9[data-v-3c1e6cb2]{font-size:%?24?%}.order-none[data-v-3c1e6cb2]{text-align:center;padding:%?200?% 0}.balance-none-img[data-v-3c1e6cb2]{width:%?274?%;height:%?274?%}",""]),t.exports=e},7723:function(t,e,a){"use strict";var i;a.d(e,"b",(function(){return n})),a.d(e,"c",(function(){return o})),a.d(e,"a",(function(){return i}));var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"uni-load-more"},[a("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:"loading"===t.status&&t.showIcon,expression:"status === 'loading' && showIcon"}],staticClass:"uni-load-more__img"},[a("v-uni-view",{staticClass:"load1"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1),a("v-uni-view",{staticClass:"load2"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1),a("v-uni-view",{staticClass:"load3"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1)],1),a("v-uni-text",{staticClass:"uni-load-more__text",style:{color:t.color}},[t._v(t._s("more"===t.status?t.contentText.contentdown:"loading"===t.status?t.contentText.contentrefresh:t.contentText.contentnomore))])],1)},o=[]},"92bd":function(t,e,a){"use strict";a.r(e);var i=a("d70a"),n=a.n(i);for(var o in i)"default"!==o&&function(t){a.d(e,t,(function(){return i[t]}))}(o);e["default"]=n.a},"9ade":function(t,e,a){"use strict";a.r(e);var i=a("6053"),n=a.n(i);for(var o in i)"default"!==o&&function(t){a.d(e,t,(function(){return i[t]}))}(o);e["default"]=n.a},"9bb8":function(t,e,a){"use strict";var i=a("63d6"),n=a.n(i);n.a},cf3b:function(t,e,a){"use strict";a.d(e,"b",(function(){return n})),a.d(e,"c",(function(){return o})),a.d(e,"a",(function(){return i}));var i={uniLoadMore:a("287c").default},n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"content"},[t.list.length?a("v-uni-view",{staticClass:"type-c"},[t._l(t.list,(function(e,i){return a("v-uni-view",{key:i,staticClass:"cell-group margin-cell-group"},[a("v-uni-view",{staticClass:"cell-item"},[a("v-uni-view",{staticClass:"cell-item-hd"},[a("v-uni-view",{staticClass:"cell-hd-title"},[t._v("下单人:"+t._s(e.buy_user))])],1),a("v-uni-view",{staticClass:"cell-item-ft"},[a("v-uni-view",{staticClass:"cell-ft-p color-9"},[t._v(t._s(e.ctime))])],1)],1),a("v-uni-view",{staticClass:"cell-item"},[a("v-uni-view",{staticClass:"cell-item-hd"},[a("v-uni-view",{staticClass:"cell-hd-title color-9"},[t._v("订单号:"+t._s(e.order_id))])],1),a("v-uni-view",{staticClass:"cell-item-ft red-price"},[t._v(t._s(e.amount))])],1)],1)})),a("uni-load-more",{attrs:{status:t.loadStatus}})],2):a("v-uni-view",{staticClass:"order-none"},[a("v-uni-image",{staticClass:"balance-none-img",attrs:{src:"/static/image/order.png",mode:""}})],1)],1)},o=[]},d5ad:function(t,e,a){"use strict";a.r(e);var i=a("cf3b"),n=a("92bd");for(var o in n)"default"!==o&&function(t){a.d(e,t,(function(){return n[t]}))}(o);a("ea08");var r,d=a("f0c5"),s=Object(d["a"])(n["default"],i["b"],i["c"],!1,null,"3c1e6cb2",null,!1,i["a"],r);e["default"]=s.exports},d70a:function(t,e,a){"use strict";var i=a("4ea4");a("99af"),a("4160"),a("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=i(a("2909")),o=i(a("287c")),r=a("19d5"),d={mixins:[r.goods],components:{uniLoadMore:o.default},data:function(){return{startTime:0,screenName:"",page:1,limit:10,list:[],loadStatus:"more"}},onLoad:function(){this.getDistributionOrder()},onShow:function(){uni.getSystemInfoSync()},onReachBottom:function(){"more"===this.loadStatus&&this.getDistributionOrder()},methods:{getDistributionOrder:function(){var t=this,e={page:this.page,limit:this.limit};this.loadStatus="loading",this.$api.getDistributionOrder(e,(function(e){if(e.status){var a=e.data.list;a.forEach((function(e){t.$set(e,"slide_x",0)})),t.list=[].concat((0,n.default)(t.list),(0,n.default)(a)),e.data.count>t.list.length?(t.page++,t.loadStatus="more"):t.loadStatus="noMore"}else t.$common.errorToShow(e.msg)}))}}};e.default=d},db90:function(t,e,a){"use strict";function i(t){if("undefined"!==typeof Symbol&&Symbol.iterator in Object(t))return Array.from(t)}a("a4d3"),a("e01a"),a("d28b"),a("a630"),a("d3b7"),a("3ca3"),a("ddb0"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=i},dd72:function(t,e,a){var i=a("6fc3");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("4f06").default;n("221ceda4",i,!0,{sourceMap:!1,shadowMode:!1})},ea08:function(t,e,a){"use strict";var i=a("dd72"),n=a.n(i);n.a}}]);