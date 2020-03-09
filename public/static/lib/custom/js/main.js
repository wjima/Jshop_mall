var allWidget = {
  "mediaComponents": [{
    "type": "imgSlide",
    "name": "图片轮播",
    "value": {
      "duration": 2500,
      "list": [{
        "image": default_banner,
        "linkType": '',
        "linkValue": ''
      },
      {
        "image": default_banner,
        "linkType": '',
        "linkValue": ''
      }
      ]
    },
    "icon": "icon-lunbo"
  },
  {
    "type": "imgSingle",
    "name": "图片",
    "value": {
      "list": [{
        "image": default_banner,
        "linkType": '',
        "linkValue": '',
        "buttonShow":false,
        "buttonText":'',
        "buttonColor":"#FFFFFF",
        "textColor":"#000000"
      }]
    },
    "icon": "icon-zhaopiantubiao"
  },
  {
    "type": "imgWindow",
    "name": "图片分组",
    "value": {
      "style": 2,  // 0 橱窗  2 两列 3三列 4四列
      "margin":0,
      "list": [
        {
          "image": default_banner,
          "linkType": '',
          "linkValue": ''
        },
        {
          "image": default_banner,
          "linkType": '',
          "linkValue": ''
        }, {
          "image": default_banner,
          "linkType": '',
          "linkValue": ''
        },
        {
          "image": default_banner,
          "linkType": '',
          "linkValue": ''
        }
      ]
    },
    "icon": "icon-zidongchuchuang50"
  },
  {
    "type": "video",
    "name": "视频组",
    "value": {
      "autoplay": "false",
      "list": [{
        "image": default_banner,
        "url": "http://wxsnsdy.tc.qq.com/105/20210/snsdyvideodownload?filekey=30280201010421301f0201690402534804102ca905ce620b1241b726bc41dcff44e00204012882540400",
        "linkType": '',
        "linkValue": ''
      }]
    },
    "icon": "icon-shipin"
  },
  {
    "type": "article",
    "name": "文章组",
    "value": {
      "list": [
        {
          "title": ''
        }
      ]
    },
    "icon": "icon-wenzhang1"
  },
  {
    "type": "articleClassify",
    "name": "文章分类",
    "value": {
      "limit": 3,
      "articleClassifyId": ''
    },
    "icon": "icon-wenzhangfenlei"
  }
  ],
  "storeComponents": [{
    "type": "search",
    "name": "搜索框",
    "value": {
      "keywords": '请输入关键字搜索',
      "style": 'round' // round:圆弧 radius:圆角 square:方形
    },
    "icon": "icon-sousuokuang"
  },
  {
    "type": "notice",
    "name": "公告组",
    "value": {
      "type": 'auto', //choose手动选择， auto 自动获取
      "list": [
        {
          "title": "这里是第一条公告的标题",
          "content": "",
          "id": ''
        }
      ]
    },
    "icon": "icon-gonggao"
  },
  {
    "type": "navBar",
    "name": "导航组",
    "value": {
      "limit": 4,
      "list": [
        {
          "image": default_img,
          "text": "按钮1",
          "linkType": '',
          "linkValue": ''
        },
        {
          "image": default_img,
          "text": "按钮2",
          "linkType": '',
          "linkValue": ''
        },
        {
          "image": default_img,
          "text": "按钮3",
          "linkType": '',
          "linkValue": ''
        },
        {
          "image": default_img,
          "text": "按钮4",
          "linkType": '',
          "linkValue": ''
        }
      ]
    },
    "icon": "icon-daohangliebiao"
  },
  {
    "type": "tabBar",
    "name": "文字导航组",
    "value": {
      "limit": 5,
      "list": [
        {
          "text": "按钮1",
          "linkType": '',
          "linkValue": ''
        },
        {
          "text": "按钮2",
          "linkType": '',
          "linkValue": ''
        },
        {
          "text": "按钮3",
          "linkType": '',
          "linkValue": ''
        },
        {
          "text": "按钮4",
          "linkType": '',
          "linkValue": ''
        },
		{
		  "text": "按钮5",
		  "linkType": '',
		  "linkValue": ''
		}
      ]
    },
    "icon": "icon-daohangliebiao"
  },
  {
    "type": "goods",
    "name": "商品组",
    "icon": "icon-shangpin",
    "value": {
      "title": '商品组名称',
      "lookMore": "true",
      "type": "auto", //auto自动获取  choose 手动选择
      "classifyId": '', //所选分类id
      "brandId": '', //所选品牌id
      "limit": 10,
      "display": "list", //list , slide
      "column": 2, //分裂数量
      "list": [
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        }
      ]
    },
  },
  {
    "type": "groupPurchase",
    "name": "团购秒杀",
    "value": {
      "title": '活动名称',
      "limit": '10',
      "list": [
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
      ]
    },
    "icon": "icon-tuangou"
  },
  {
    "type": "pintuan",
    "name": "拼团",
    "value": {
      "title": '活动名称',
      "limit": '10',
      "list": [
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
      ]
    },
    "icon": "icon-pintuan"
  },
  {
    "type": "coupon",
    "name": "优惠券组",
    "value": {
      "limit": '2'
    },
    "icon": "icon-tubiao-youhuiquan"
  },
  {
    "type": "record",
    "name": "购买记录",
    "value": {
      "style": {
        "top": 20,
        "left": 0
      }
    },
    "icon": "icon-jilu"
  },
  {
    "type": "adpop",
    "name": "弹出广告位",
    "value": {
      "list": [{
        "image": default_banner,
        "linkType": '',
        "linkValue": '',
      }]
    },
    "icon": "icon-zhaopiantubiao"
  },
  ],
  "utilsComponents": [
    {
      "type": "blank",
      "name": "辅助空白",
      "icon": 'icon-kongbai',
      "value": {
        "height": 20,
        "backgroundColor": "#FFFFFF"
      },
    },
    {
      "type": "textarea",
      "name": "文本域",
      "value": '',
      "icon": 'icon-fuwenben',
    }]
    
};

var deepClone = function (obj) {
  let result = Array.isArray(obj) ? [] : {}
  for (let key in obj) {
    if (obj.hasOwnProperty(key)) {
      if (typeof obj[key] === 'object') {
        result[key] = deepClone(obj[key]) //递归复制
      } else {
        result[key] = obj[key]
      }
    }
  }
  return result
}
Vue.prototype.bus = new Vue();

Vue.component('layout', {
  template: '#layout',
  name:'layout',
  data() {
    return {
      pageData: [],
      selectWg: {}
    }
  },
  computed: {
    getNumber(val) {
      return function (val) {
        return Number(val)
      }
    }
  },
  mounted() {
    if (pageConfig.length > 0) {
      for (var i = 0; i < pageConfig.length; i++) {
        var item = pageConfig[i];
        var elKey = Date.now() + '_' + Math.ceil(Math.random() * 1000000)
        item.key = item.type + '_' + elKey
      }
      this.pageData = pageConfig;
    }

  },
  methods: {
    setSelectWg(data) {
      this.selectWg = data
      this.bus.$emit('changeSelectWg', data)
    },
    handleWidgetAdd: function (evt) {
      var newIndex = evt.newIndex;
      var elKey = Date.now() + '_' + Math.ceil(Math.random() * 1000000)
      var newObj = deepClone(this.pageData[newIndex])
      newObj.key = this.pageData[newIndex].type + '_' + elKey
      this.$set(this.pageData, newIndex, newObj)
      this.setSelectWg(this.pageData[newIndex])
    },
    handleClickAdd: function (obj) {
      var elKey = Date.now() + '_' + Math.ceil(Math.random() * 1000000)
      var newObj = deepClone(obj)
      newObj.key = obj.type + '_' + elKey;
      var newIndex = this.pageData.length || 0;
      this.$set(this.pageData, newIndex, newObj)
      this.setSelectWg(this.pageData[newIndex])
    },
    handleSelectWidget(index) {
      this.setSelectWg(this.pageData[index])
    },
    handleSelectRecord(index) {
      this.setSelectWg(this.pageData[index])
    },
    deleteWidget(index) {
      if (this.pageData.length - 1 === index) {
        if (index === 0) {
          this.setSelectWg([])
        } else {
          this.setSelectWg(this.pageData[index - 1])
        }
      } else {
        this.setSelectWg(this.pageData[index + 1])
      }
      this.$nextTick(() => {
        this.pageData.splice(index, 1)
      })
    },
    handleWidgetDelete(deleteIndex) {
      var that = this;
      layer.open({
        title: '提示',
        content: '确定要删除吗？',
        btn: ['确定', '取消'],
        yes: function (index, layero) {
          that.deleteWidget(deleteIndex);
          layer.close(index)
        },
        btn2: function () {
          return
        }
      });

    },
    handleWidgetClone(index) {
      let cloneData = deepClone(this.pageData[index])
      cloneData.key =
        this.pageData[index].type +
        '_' +
        Date.now() +
        '_' +
        Math.ceil(Math.random() * 1000000)
      this.pageData.splice(index, 0, cloneData)
      this.$nextTick(() => {
        this.setSelectWg(this.pageData[index + 1])
      })
    },
    handleDragRemove:function(evt){
      this.setSelectWg({});
    },
    datadragEnd:function(evt){
      
    }
  }
})
Vue.component('upload-img', {
  template: "#upload-img",
  data: function () {
    return {}
  },
  props: ['index', "item"],
  methods: {
    upload: function () {
      this.$emit('upload-img')
    }
  }
})
Vue.component('select-link', {
  template: '#select-link',
  props: ['type', 'id'],
  data: function () {
    return {
      linkType: linkType,
      linkUrl: this.id || '',
      selectType: this.type?''+this.type:Object.keys(linkType)[0]
    }
  },
  watch: {
    type(newVal, oldVal) {
      this.selectType = newVal;
      if (newVal == 1) {
        this.linkUrl = this.id
      }
    }
  },
  mounted(){
    if(!this.type){
      this.$emit('update:type', Object.keys(linkType)[0])
    }
  },
  methods: {
    selectLink: function () {
      this.$emit('choose-link')
    },
    changeSelect: function () {
      this.$emit('update:type', this.selectType)
      this.$emit("update:id", '')
    },
    updateLinkValue: function () {
      this.$emit("update:id", this.linkUrl)
    },
    updateSelect: function (){
      this.$emit("update:id",this.id)
    }
  }
})
Vue.component('layout-config', {
  template: '#layout-config',
  name:'LayoutConfig',
  data: function () {
    return {
      selectWg: {},
      _editocoverr: null,
      maxSelectGoods: 10, //选择商品最大数量
      maxNoticeNums: 5, //选择公告最多数量
      catList: catList,
      brandList: brandList,
      hasChooseGoods: [],
      hasChooseGroupGoods: [],
      linkType: linkType,
      linkName: '',
      currentItemIndex: '',
      editor: null,
      defaultGoods: [
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        },
        {
          "image": default_banner,
          "name": '',
          "price": ''
        }
      ],
      imgWindowStyle:[
        {
          "title":'1行2个',
          "value":2,
          "image":imgWindowArr[0]
        },
        {
          "title":'1行3个',
          "value":3,
          "image":imgWindowArr[1]
        },
        {
          "title":'1行4个',
          "value":4,
          "image":imgWindowArr[2]
        },
        {
          "title":'1左3右',
          "value":0,
          "image":imgWindowArr[3]
        },
      ]
    }
  },
  watch: {
    selectWg(newVal, oldVal) {
      if (newVal.type == 'textarea') {
        var that = this;
        this.$nextTick(function () {
          if (!this.editor) {
            this.editor = UE.getEditor('container')
          }
          this.editor.ready(function () {
            that.editor.setContent(that.selectWg.value)
            that.editor.addListener("contentChange", function () {
              // var wordCount = that.editor.getContentLength(true)
              var content = that.editor.getContent()
              that.selectWg.value = content;
            }.bind(that))
          }.bind(this))
        })
      } else {
        if (this.editor) {
          this.editor.destroy()
          this.editor = null;
        }
      }
    }
  },
  computed: {
    getSelectWgName: function (type) {
      return function (type) {
        switch (type) {
          case 'imgSlide':
            return '图片轮播'
            break;
          case 'imgSingle':
            return '图片'
            break;
          case 'imgWindow':
            return '图片分组'
            break;
          case 'video':
            return '视频组'
            break;
          case 'article':
            return '文章组'
            break;
          case 'articleClassify':
            return '文章分类'
            break;
          case 'search':
            return '搜索框'
            break;
          case 'notice':
            return '公告组'
            break;
          case 'navBar':
            return '导航组'
            break;
          case 'tabBar':
            return '文字导航组'
            break;
          case 'goods':
            return '商品组'
            break;
          case 'groupPurchase':
            return '团购秒杀'
            break;
          case 'pintuan':
            return '拼团'
            break;
          case 'coupon':
            return '优惠券组'
            break;
          case 'record':
            return '购买记录'
            break;
          case 'blank':
            return '辅助空白'
            break;
          case 'textarea':
            return '文本域'
            break;
          case 'adpop':
            return '弹出广告位'
            break;
          default:
            return '';
            break;
        }
      }
    }
  },
  mounted() {
    var that = this;
    this.bus.$on('changeSelectWg', function (data) {
      that.selectWg = data
    })

    that.$nextTick(function () {
      var _editocoverr = UE.getEditor("edit_cover", {
        initialFrameWidth: 800,
        initialFrameHeight: 300,
        zIndex: 19891026,
        single: false
      });
      that._editocoverr = _editocoverr;
      that._editocoverr.ready(function () {
        that._editocoverr.hide();
        that._editocoverr.addListener('beforeInsertImage', function (t, arg) {
          var obj = that._editocoverr.queryCommandValue("serverparam");
          that.$set(that.selectWg.value.list[obj.index], 'image', arg[0].src)
        }.bind(that));
      })
    })
    layui.use(['table'], function () {
      var table = layui.table;
      //监听文章列表页工具条
      table.on('tool(articleTable)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'selectArticle') { //选择
          if (that.selectWg.type == 'article') {
            that.$set(that.selectWg.value.list, 0, data)
          } else {
            that.$set(that.selectWg.value.list[that.currentItemIndex], 'linkValue', data.id)
          }
          layer.close(window.box);
        }
      });

      //监听商品列表页工具条
      table.on('tool(goodsTable)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象

        if (layEvent === 'selectGoods') { //选择
          that.$set(that.selectWg.value.list[that.currentItemIndex], 'linkValue', data.id)
          layer.close(window.box);
        }
      });

      // 监听文章分类列表页工具条
      table.on('tool(articleTypeTable)', function (obj) {
        var data = obj.data;
        var layEvent = obj.event;
        var tr = obj.tr;

        if (layEvent === 'selectType') { //选择
          if (that.selectWg.type == 'articleClassify') {
            that.selectWg.value.articleClassifyId = data.id
          } else {
            that.$set(that.selectWg.value.list[that.currentItemIndex], 'linkValue', data.id)
          }

          layer.close(window.box);
        }
      });

      // 监听表单列表页工具条
      table.on('tool(formTable)', function (obj) {
        var data = obj.data;
        var layEvent = obj.event;
        var tr = obj.tr;
        if (layEvent === 'selectform') { //选择
          that.$set(that.selectWg.value.list[that.currentItemIndex], 'linkValue', data.id)
          layer.close(window.box);
        }
      })
    })
  },
  methods: {
    slectTplStyle:function(item){
      this.selectWg.value.style=item.value;
    },
    chooseLink: function (index, type) {
      this.currentItemIndex = index;
      this.$set(this.selectWg.value.list[index], 'linkType', type)
      switch (+type) {
        case 2:
          this.goods_list()
          break;
        case 3:
          this.article_list()
          break;
        case 4:
          this.article_type_list()
          break;
        case 5:
          this.form_list()
          break;
        default:
          break;
      }
    },
    form_list: function () {
      JsGet("/manage/carousel/getform.html", function (e) {
        window.box = layer.open({
          type: 1,
          content: e,
          area: ['700px', '450px'],
          title: '表单列表'
        });
      });
    },

    goods_list: function () {
      JsGet("/manage/carousel/getgoods.html", function (e) {
        window.box = layer.open({
          type: 1,
          content: e,
          area: ['700px', '450px'],
          title: '商品列表'
        });
      });
    },
    article_type_list: function () {
      JsGet("/manage/carousel/getarticletype.html", function (e) {
        window.box = layer.open({
          type: 1,
          content: e,
          area: ['700px', '450px'],
          title: '文章分类列表'
        })
      })
    },

    resetColor: function () {
      this.selectWg.value.backgroundColor = '#FFFFFF'
    },
    handleSlideRemove: function (index) {
      this.selectWg.value.list.splice(index, 1)
    },
    handleAddSlide: function () {
      this.selectWg.value.list.push({
        url: '',
        image: default_banner
      })
    },
    handleAddNav: function () {
      this.selectWg.value.list.push({
        url: '',
        image: default_img,
        text: '按钮文字'
      })
    },
    upImage: function (index, item) {
      var _this = this;
      var obj = _this._editocoverr.queryCommandValue("serverparam");
      obj.index = index;
      obj.item = item;
      var myImagcovere = _this._editocoverr.getDialog("insertimage");

      myImagcovere.open();
    },
    article_list: function () {
      JsGet("/manage/carousel/getarticle.html", function (e) {
        window.box = layer.open({
          type: 1,
          content: e,
          area: ['800px', '450px'],
          title: '文章列表'
        });
      });
    },
    changeGoodsType: function (val) {
      if (val == 'auto') {
        this.hasChooseGoods=this.selectWg.value.list;
        this.selectWg.value.list = this.defaultGoods
      } else {
        this.selectWg.value.list = this.hasChooseGoods.length > 0 ? this.hasChooseGoods : this.defaultGoods
      }
    },
    handleDeleteNotice: function (index) {
      this.selectWg.value.list.splice(index, 1)
    },
    handleDeleteGoods: function (index) {
      this.selectWg.value.list.splice(index, 1)
    },
    selectNotice: function () {
      var that = this;
      layui.use(['form', 'table'], function () {
        $.ajax({
          type: "get",
          url: "/manage/index/tagselectnotice/type/show.html",
          data: "",
          success: function (e) {
            layui.layer.open({
              type: 1,
              content: e,
              area: ["800px", "600px"],
              title: "选择公告",
              btn: ["完成", "取消"],
              yes: function (index, layero) {
                //判断个数是否满足
                if (Object.getOwnPropertyNames(ids).length > that.maxNoticeNums) {
                  layer.msg("最多只能选择" + that.maxNoticeNums + "个");
                  return false;
                }
                var arr = []
                for (let i in ids) {
                  arr.push(ids[i]);
                }
                that.$set(that.selectWg.value, 'list', arr)
                layer.close(index);
              }
            });
          }
        });
      });
    },
    selectGroupGoods: function () {
      var that = this;
      layui.use(['form', 'table'], function () {
        $.ajax({
          type: "get",
          url: "/manage/index/tagselectgroup/type/show.html",
          data: "",
          success: function (e) {
            layui.layer.open({
              type: 1,
              content: e,
              area: ["800px", "635px"],
              title: "选择商品",
              btn: ["完成", "取消"],
              yes: function (index, layero) {
                //判断个数是否满足
                if (Object.getOwnPropertyNames(ids).length > that.maxSelectGoods) {
                  layer.msg("最多只能选择" + that.maxSelectGoods + "个");
                  return false;
                }
                var arr = []
                for (let i in ids) {
                  arr.push(ids[i]);
                }
                that.$set(that.selectWg.value, 'list', arr)

                layer.close(index);
              }
            });
          }
        });
      });
    },
    selectPintuanGoods:function(){
      var that = this;
      layui.use(['form', 'table'], function () {
        $.ajax({
          type: "get",
          url: "/manage/index/tagpintuan/type/show.html",
          data: "",
          success: function (e) {
            layui.layer.open({
              type: 1,
              content: e,
              area: ["800px", "635px"],
              title: "选择商品",
              btn: ["完成", "取消"],
              yes: function (index, layero) {
                //判断个数是否满足
                if (Object.getOwnPropertyNames(ids).length > that.maxSelectGoods) {
                  layer.msg("最多只能选择" + that.maxSelectGoods + "个");
                  return false;
                }
                var arr = []
                for (let i in ids) {
                  arr.push(ids[i]);
                }
                console.log(arr);
                that.$set(that.selectWg.value, 'list', arr)

                layer.close(index);
              }
            });
          }
        });
      });
    },
    selectGoods: function () {
      var that = this;
      layui.use(['form', 'table'], function () {
        $.ajax({
          type: "get",
          url: "/manage/index/tagselectgoods/type/show.html",
          data: "",
          success: function (e) {
            layui.layer.open({
              type: 1,
              content: e,
              area: ["800px", "600px"],
              title: "选择商品",
              btn: ["完成", "取消"],
              yes: function (index, layero) {
                //判断个数是否满足
                if (Object.getOwnPropertyNames(ids).length > that.maxSelectGoods) {
                  layer.msg("最多只能选择" + that.maxSelectGoods + "个");
                  return false;
                }
                var arr = []
                for (let i in ids) {
                  arr.push(ids[i]);
                }
                that.hasChooseGoods = arr;
                that.$set(that.selectWg.value, 'list', arr)

                layer.close(index);
              }
            });
          }
        });
      });
    }

  }
})
new Vue({
  el: '#app',
  data: {

  },
  components: {
    "home": {
      template: "#home",
      data() {
        return {
          storeComponents: allWidget.storeComponents,
          utilsComponents: allWidget.utilsComponents,
          mediaComponents: allWidget.mediaComponents,
          saveUrl:saveUrl
        }
      },
      methods: {
        savePage: function () {
          var data = {
            pageCode: pageCode,
            data: this.$refs.layout.pageData
          }
          JsPost(this.saveUrl, data, function (res) {
            if (res.status) {
              layer.msg(res.msg, { time: 1300 }, function () {
              });
            } else {
              layer.msg(res.msg);
            }
          })
        },
        selectWidget: function (type) {
          for (var key in allWidget) {
            for (var index = 0; index < allWidget[key].length; index++) {
              var element = allWidget[key][index];
              if (element.type == type) {
                this.$refs.layout.handleClickAdd(element)
              }
            }
          }
        }
      },
      mounted() {
        var that = this;
        layui.use(['form', 'laytpl'], function () {
          form = layui.form;
        });
      },
    }
  }
});