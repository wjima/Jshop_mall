/** layui-v1.0.3 LGPL license By www.layui.com */ ;
layui.define("jquery", function(e) {
    "use strict";
    var o = layui.jquery,
        a = layui.hint(),
        r = "layui-tree-enter",
        i = function(e) {
            //options就是传递进来的数据
            this.options = e
        },
        t = {
            arrow: ["&#xe623;", "&#xe625;"],
            checkbox: ["&#xe626;", "&#xe627;"],
            radio: ["&#xe62b;", "&#xe62a;"],
            branch: ["&#xe622;", "&#xe624;"],
            leaf: "&#xe621;"
        },
        num = 1;
    i.prototype.init = function(e) {
        var o = this;
        e.addClass("layui-box layui-tree"),
            //如果o.options.skin存在则执行后面的方法
        o.options.skin && e.addClass("layui-tree-skin-" + o.options.skin),
            o.tree2(e),
            o.on(e)
    },
        i.prototype.tree2 = function(e, a) {//生成树
            var r = this,
                i = r.options,
                n = a || i.nodes;
            layui.each(n, function(a, n) {
                var id = r.uuid();
                n.id = id;//给每一个节点都设置id
                if(n.children){//如果当前节点有子节点，则将子节点的pid设置成当前节点的id
                    layui.each(n.children, function(index,item) {
                        item.pid = n.id;
                    });
                }
                var l = n.children && n.children.length > 0,
                    //生成ul
                    c = o('<ul class="' + (n.spread ? "layui-show" : "") + '"></ul>'),
                    //生成节点
                    s = o(
                        [
                            "<li " + (n.spread ? 'data-spread="' + n.spread + '"' : "") + ">",
                            function() {
                                return l ? '<i class="layui-icon layui-tree-spread">' + (n.spread ? t.arrow[1] : t.arrow[0]) + '</i>' : '<i style="paddling-left: 28px;"></i>';
                            }(),
                            function() {
                                //如果用户选择的风格是checkbox则生成checkbox
                                var eleStr = i.check&&i.check=="checkbox" ? '<input type="checkbox" name="'+i.checkboxName+'" '+((n.checked&&n.checked==true) ? 'checked="checked"' : "")+(n.checkboxValue ? ('value="' + n.checkboxValue + '"') : "") + 'data-parent-id="' + n.pid +'"' + 'data-name="' + n.name +'"'+ 'data-parent-value="' + n.parent_id +'"'+ 'id="' + n.id + '"' +(i.checkboxStyle ? (' style="' + i.checkboxStyle + '"') : "" ) :  '';
                                if(eleStr.length > 0){
                                    //给元素添加data-xxx属性
                                    if(n.data && Object.prototype.toString.call(n.data) == "[object Object]"){
                                        for(var attr in n.data){
                                            eleStr += ' data-' + attr + '=' + n.data[attr];
                                        }
                                    }
                                    eleStr += ' />';
                                }

                                return eleStr;
                                //return i.check&&i.check=="checkbox" ? '<input type="checkbox" name="'+i.checkboxName+'" '+((n.checked&&n.checked==true) ? 'checked="checked"' : "")+(n.checkboxValue ? ('value="' + n.checkboxValue + '"') : "") + 'data-parent-id="' + n.pid +'"' + 'id="' + n.id + '"' +(i.checkboxStyle ? ('style="' + i.checkboxStyle + '"') : "" )+' />' : "";
                            }(),
                            function() {
                                //a链接是否可点击
                                return '<a href="' + (n.href || "javascript:;") + '" ' + (i.target && n.href ? 'target="' + i.target + '"' : "") + ">" + ('<i class="layui-icon layui-tree-' + (l ? "branch" : "leaf") + '">' + (l ? n.spread ? t.branch[1] : t.branch[0] : t.leaf) + "</i>") + ("<cite>" + (n.name || "未命名") + "</cite></a>")
                            }(),
                            "</li>"
                        ].join("")
                    );
                l && (s.append(c), r.tree2(c, n.children)),
                    e.append(s),
                "function" == typeof i.click && r.click(s, n),
                    r.spread(s, n),
                i.drag && r.drag(s, n)
                r.changed(s,n)
            })
        },
        i.prototype.changed = function (e,o){
            //当checkbox的值改变后所处理动作
            var r = this;

            //最顶层的input点击后，全选该节点下的所有input
            if(o.pid == undefined || o.pid == null){
                e.children("input").on("change",function (){
                    var childUl = e.children("ul"),
                        checked = this.checked;
                    childUl.find("input").prop("checked",checked);
                    try{r.options.onchange.call(this);}catch(e){}
                });
            }else{
                e.children("input").on("change",function (){
                    //选中子checkbox后将该checkbox的所有父checkbox都选中
                    var that = this;
                    if(!this.checked){
                        childCheckboxCheckOrNot.call(this);

                        r.cancelParentsCheckboxCheck(that);
                    }else{
                        r.parentsChecked(this,this.checked);

                        childCheckboxCheckOrNot.call(this);
                    }
                    try{r.options.onchange.call(this);}catch(e){}
                });
            }
            /*如果当前checkbox下还有子checkbox的话，则将子checkbox全部选中或取消选中*/
            function childCheckboxCheckOrNot(){
                if(o.children && o.children.length > 0){
                    //如果该checkbox下还有其他子checkbox，则将子checkbox全部选中
                    var childUl = e.children("ul"),
                        checked = this.checked;
                    childUl.find("input").prop("checked",checked);
                }
            }
        },
        i.prototype.cancelParentsCheckboxCheck = function (ele){
            //当当前的checkbox为未选中时，判断是否取消父级checkbox的选中
            if(!ele){
                return;
            }
            var r = this,
                siblingInputs = r.siblingInputs(ele),//获取当前checkbox的兄弟级checkbox
                parentId = ele.getAttribute("data-parent-id"),
                parentInput = null,
                bool = true,
                childrendInputs = null,
                hasOneChildrenInputCheck = false;

            if(parentId != 'undefined'){
                //获取当前checkbox的父级checkbox
                parentInput = document.getElementById(parentId);
                childrendInputs = r.currentChildrenInputs(parentInput);
            }

            for(var i = 0,len = siblingInputs.length; i < len; i ++){
                if(siblingInputs[i].checked){
                    bool = false;
                    break;
                }
            }
            if(!childrendInputs || childrendInputs.length == 0){
                hasOneChildrenInputCheck = false;
            }else{
                for(var j = 0,len2 = childrendInputs.length; j < len2; j ++){
                    if(childrendInputs[j].getAttribute("data-parent-id") != "undefined"){

                        if(childrendInputs[j].checked){
                            hasOneChildrenInputCheck = true;
                            break;
                        }
                    }
                }
            }
            //以下代码注释掉是当本节点取消的时候，父节点不变，这是和tree的根本区别 add by wht
            //if(bool && !hasOneChildrenInputCheck){
            //    r.inputChecked(parentInput,false);
            //}

            //this.cancelParentsCheckboxCheck(parentInput);
        },
        i.prototype.siblingInputs = function (ele){
            //选择指定checkbox的同级兄弟checkbox
            var that = this;
            if(ele){
                var parent = ele.parentElement,
                    parents = parent.parentElement,
                    childrens = parents.children,
                    siblingInputs = [];
            }else{
                return null;
            }

            for(var i = 0,len = childrens.length; i < len; i ++){
                if(childrens[i] != parent){
                    if(childrens[i].children[0].nodeName == "INPUT"){
                        siblingInputs.push(childrens[i].children[0]);
                    }
                    if(childrens[i].children[1].nodeName == "INPUT"){
                        siblingInputs.push(childrens[i].children[1]);
                    }
                }
            }
            parent = null;parents = null;childrens = null;
            return siblingInputs;
        },
        i.prototype.currentChildrenInputs = function (ele){
            //找到当前checkbox对应的所有子级checkbox
            var parent = ele.parentElement,
                childrenInputs = [];
            if(parent.getElementsByTagName("ul").length > 0){
                var uls = parent.getElementsByTagName("ul");
                for(var i = 0,len = uls.length; i < len; i ++){
                    var inputs = uls[i].getElementsByTagName("input");
                    for(var j = 0,len2 = inputs.length; j < len2; j ++){
                        childrenInputs.push(inputs[j]);
                    }
                }
            }
            return childrenInputs;
        },
        i.prototype.inputChecked = function (ele,checked){
            //checkbox选中或不选中
            //input.setAttribute("checked","checked");/*使用这种方式在chrome中会有显示不出来勾选的问题*/
            ele && (ele.checked = checked);
        },
        i.prototype.parentsChecked = function (e,checked){
            //子checkbox选中后，它的所有父checkbox都选中
            var r = this,
                i = r.options,
                selector = i.elem,
                currentInput = e;
            //console.log(checked)
            if(currentInput && (currentInput.nodeName == "INPUT")){
                var parentId = currentInput.getAttribute("data-parent-id"),
                    parentInput = null;

                setTimeout(function (){
                    r.inputChecked(currentInput,checked);

                    if(parentId){
                        r.parentsChecked(document.getElementById(parentId),checked);
                    }
                },50);
            }
        },
        i.prototype.findParents = function (ele,selector){
            //查找元素的指定父元素
            var parent = ele.parentElement,
                that = this;
            if(selector.substr(0,1) == "#"){
                if(parent){
                    if(parent.id != selector.substr(1)){
                        that.findParents(parent,selector);
                    }else{
                        return parent;
                    }
                }
            }else if(selector.substr(0,1) == "."){
                if(parent){
                    var classnameArr = parent.className.split(" "),
                        len = classnameArr.length,
                        selectt = selector.substr(1),
                        hasSelector = false;

                    if(len > 0){
                        for(var i = 0; i < len; i ++){
                            if(classnameArr[i] == selectt){
                                hasSelector = true;
                                break;
                            }
                        }
                    }

                    if(!hasSelector){
                        that.findParents(parent,selector);
                    }else if(hasSelector){
                        return parent;
                    }
                }
            }
        },
        i.prototype.uuid = function (){
            //生成唯一的字符串
            var that = this,
                randomStr = ['l','a','y','e','r','n','i'],
                randomNum = Math.floor(Math.random()*6);
            return function (){
                var str = "";
                for(var i = 0; i <= randomNum; i++){
                    str += randomStr[Math.floor(Math.random()*6)];
                }
                return "lyn_" + new Date().getTime() + "_" + (num++) + "_" + (++num) + "_" + str;
            }();
        },
        i.prototype.click = function(e, o) {
            var a = this,
                r = a.options;
            e.children("a").on("click", function(e) {
                layui.stope(e),
                    r.click(o)
            })
        },
        i.prototype.spread = function(e, o) {
            //这个方法的功能是展开子节点
            var a = this,
                //r就是小箭头
                r = (a.options, e.children(".layui-tree-spread")),
                i = e.children("ul"),
                n = e.children("a"),
                l = function() {
                    //e.data("spread")为true，表示当前节点是展开的
                    e.data("spread") ? (e.data("spread", null), i.removeClass("layui-show"), r.html(t.arrow[0]), n.find(".layui-icon").html(t.branch[0])) : (e.data("spread", !0), i.addClass("layui-show"), r.html(t.arrow[1]), n.find(".layui-icon").html(t.branch[1]))
                };
            //单击小箭头或双击整个节点都会展开会折起
            i[0] && (r.on("click", l), n.on("dblclick", l))
        },
        i.prototype.on = function(e) {
            var a = this,
                i = a.options,
                t = "layui-tree-drag";
            e.find("i").on("selectstart", function(e) {
                return !1
            }),
            i.drag && o(document).on("mousemove", function(e) {
                var r = a.move;
                if(r.from) {
                    var i = (r.to, o('<div class="layui-box ' + t + '"></div>'));
                    e.preventDefault(),
                    o("." + t)[0] || o("body").append(i);
                    var n = o("." + t)[0] ? o("." + t) : i;
                    n.addClass("layui-show").html(r.from.elem.children("a").html()),
                        n.css({
                            left: e.pageX + 10,
                            top: e.pageY + 10
                        })
                }
            }).on("mouseup", function() {
                var e = a.move;
                e.from && (e.from.elem.children("a").removeClass(r), e.to && e.to.elem.children("a").removeClass(r), a.move = {}, o("." + t).remove())
            })
        },
        i.prototype.move = {},
        i.prototype.drag = function(e, a) {
            var i = this,
                t = (i.options, e.children("a")),
                n = function() {
                    var t = o(this),
                        n = i.move;
                    n.from && (n.to = {
                        item: a,
                        elem: e
                    }, t.addClass(r))
                };
            t.on("mousedown", function() {
                var o = i.move;
                o.from = {
                    item: a,
                    elem: e
                }
            }),
                t.on("mouseenter", n).on("mousemove", n).on("mouseleave", function() {
                    var e = o(this),
                        a = i.move;
                    a.from && (delete a.to, e.removeClass(r))
                })
        },
        e("tree2", function(e) {
            var r = new i(e = e || {}),
                t = o(e.elem);
            return t[0] ? void r.init(t) : a.error("layui.tree 没有找到" + e.elem + "元素");
        })
})