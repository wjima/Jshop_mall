webpackJsonp([34],{"A+qx":function(e,t){},qhXZ:function(e,t,o){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n={data:function(){return{QCode:"",size:240}},mounted:function(){this.getShareCode()},methods:{getShareCode:function(){var e=this;this.$api.shareCode({},function(t){var o=t.data;e.QCodeUrl(o)})},QCodeUrl:function(e){var t=window.location.protocol,o=window.location.host;this.QCode=t+"//"+o+"/#/register?pid="+e}}},i={render:function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"share-index"},[o("qriously",{attrs:{value:e.QCode,size:e.size}}),e._v(" "),o("p",[e._v("长按二维码分享")]),e._v(" "),o("p",{attrs:{href:""}},[e._v(e._s(e.QCode))])],1)},staticRenderFns:[]};var r=o("VU/8")(n,i,!1,function(e){o("A+qx")},null,null);t.default=r.exports}});
//# sourceMappingURL=34.0cd728566996d131c5ed.js.map