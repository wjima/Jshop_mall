/**
 * 发送短信验证码的按钮和倒计时功能
 */
var countdown = 60;
function sms_btn_time(obj) { //发送验证码倒计时
    if (countdown == 0) {
        obj.attr('disabled',false);
        //obj.removeattr("disabled");
        obj.val("获取验证码");
        countdown = 60;
        return;
    } else {
        obj.attr('disabled',true);
        obj.val("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function() {
            sms_btn_time(obj) }
        ,1000)
}
/**
 * 上传图片
 * TODO 变量名可能冲突
 * 多图不能用本方法
 */
function uploadSImage(id) {
    layui.use('upload', function () {
        var $ = layui.jquery
            , upload = layui.upload;
        //普通图片上传
        var uploadInst = upload.render({
            elem: '#upload_img_' + id
            , url: Jshop_Image
            , before: function (obj) {
                obj.preview(function (index, file, result) {
                    $('#image_src_' + id).attr('src', result); //图片链接（base64）
                });
            }
            , done: function (res) {
                //如果上传失败
                if (!res.status) {
                    return layer.msg(res.msg);
                }
                $("#image_value_"+id).val(res.data.image_id);
                layer.msg("上传成功");
            }
            , error: function () {
                var demoText = $('#upload_text_' + id);
                demoText.html('<span style="color: #FF5722;">上传失败</span>');
                /*demoText.find('.img_' + id + '-reload').on('click', function () {
                 uploadInst.upload();
                 });*/
            }
        });
    });
}

function htmlEncodeByRegExp(str){
        var s = "";
        if(str.length == 0) return "";
        s = str.replace(/&/g,"&amp;");
        s = s.replace(/</g,"&lt;");
        s = s.replace(/>/g,"&gt;");
        s = s.replace(/ /g,"&nbsp;");
        s = s.replace(/\'/g,"&#39;");
        s = s.replace(/\"/g,"&quot;");
        return s;
    }
/*2.用正则表达式实现html解码*/
function htmlDecodeByRegExp(str){
    var s = "";
    if(str.length == 0) return "";
    s = str.replace(/&amp;/g,"&");
    s = s.replace(/&lt;/g,"<");
    s = s.replace(/&gt;/g,">");
    s = s.replace(/&nbsp;/g," ");
    s = s.replace(/&#39;/g,"\'");
    s = s.replace(/&quot;/g,"\"");
    return s;
}
function stringToJson(str){
    return eval('(' + str + ')');
}


// ajax封装
function JsAjax(url,type,dataType,data, success, error,cache, alone, async) {
    var type = type || 'get';//请求类型
    var dataType = dataType || 'json';//接收数据类型
    var async = async || true;//异步请求
    var alone = alone || false;//独立提交（一次有效的提交）
    var cache = cache || false;//浏览器历史缓存
    var loadingIndex = false;
    var success = success || function (data) {
            setTimeout(function () {
                layer.msg(data.msg);
            },300);
            if(data.status){
                setTimeout(function () {
                    if(data.url){
                        location.replace(data.url);
                    }else{
                        location.reload(true);
                    }
                },600000);
            }else{
                //服务器处理失败
                layer.msg("服务器开小差了，请稍后再试");
            }
        };
    var error = error || function (data) {
            layer.closeAll('loading');
            setTimeout(function () {
                if(data.status == 404){
                    layer.msg('请求失败，请求未找到');
                }else if(data.status == 503){
                    layer.msg('请求失败，服务器内部错误');
                }else {
                    layer.msg('请求失败,网络连接超时');
                }
            },500);
        };
    $.ajax({
        'url': url,
        'data': data,
        'type': type,
        'dataType': dataType,
        'async': async,
        'success': success,
        'error': error,
        'jsonpCallback': 'jsonp' + (new Date()).valueOf().toString().substr(-4),
        'beforeSend': function () {
            loadingIndex = layer.msg('加载中', {
                icon: 16,
                shade: 0.01
            },300);
        },
        'complete':function (e) {
            layer.close(loadingIndex);
        }
    });
}

// submitAjax(post方式提交)
function submitJsAjax(form, success, cache, alone) {
    cache = cache || true;
    var form = $(form);//form校检
    var url = form.attr('action');
    var data = form.serialize();
    JsAjax(url,'post','json', data, success,false,cache, alone, false);
}
//post提交数据
function JsPost(url, data, success, cache, alone) {
    JsAjax(url,'post','json',data, success,false, cache, alone, false);
}

// ajax提交(get方式提交)
function JsGet(url, success, cache, alone) {
    JsAjax(url,'get','json', {}, success,false, alone, false);
}

// jsonp跨域请求(get方式提交)
function jsonp(url, success, cache, alone) {
    JsAjax(url, 'get','jsonp',{}, success,false,cache, alone, false);
}

function getLabel(labels) {
    var html='';
    if(labels&&labels!=null&&typeof labels!='undefined' ){
        var label_style='';
        $.each(labels,function (i,obj) {
            label_style='';
            switch (obj.style) {
                case 'red':
                    label_style = "";
                    break;
                case 'green':
                    label_style = "layui-bg-green";
                    break;
                case 'orange':
                    label_style = "layui-bg-orange";
                    break;
                case 'blue':
                    label_style = "layui-bg-blue";
                    break;
                default :
                    label_style = '';
            }
            html+='<span class="layui-badge '+label_style+'">'+obj.name+'</span>&nbsp;';
        });
    }
    return html;
}

function viewImage(imgUrl) {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        scrollbar: false,
        skin: 'layui-layer-nobg', //没有背景色
        shadeClose: true,
        content: "<img style='max-width: 350px;max-height: 350px;' src='"+imgUrl + "'>"
    });
}

function Trim(str)
{
    return str.replace(/(^\s*)|(\s*$)/g, "");
}