/**
 * 发送短信验证码的按钮和倒计时功能
 */
var countdown = 60;

function sms_btn_time(obj) { //发送验证码倒计时
	if (countdown == 0) {
		obj.attr('disabled', false);
		//obj.removeattr("disabled");
		obj.val("获取验证码");
		countdown = 60;
		return;
	} else {
		obj.attr('disabled', true);
		obj.val("重新发送(" + countdown + ")");
		countdown--;
	}
	setTimeout(function() {
		sms_btn_time(obj)
	}, 1000)
}
/**
 * 上传图片
 * TODO 变量名可能冲突
 * 多图不能用本方法
 */
function uploadSImage(id) {
	layui.use('upload', function() {
		var $ = layui.jquery,
			upload = layui.upload;
		//普通图片上传
		var uploadInst = upload.render({
			elem: '#upload_img_' + id,
			url: Jshop_Image,
			before: function(obj) {
				obj.preview(function(index, file, result) {
					$('#image_src_' + id).attr('src', result); //图片链接（base64）
				});
			},
			done: function(res) {
				//如果上传失败
				if (!res.status) {
					return layer.msg(res.msg);
				}
				$("#image_value_" + id).val(res.data.image_id);
				layer.msg("上传成功");
			},
			error: function() {
				var demoText = $('#upload_text_' + id);
				demoText.html('<span style="color: #FF5722;">上传失败</span>');
				/*demoText.find('.img_' + id + '-reload').on('click', function () {
				 uploadInst.upload();
				 });*/
			}
		});
	});
}

function htmlEncodeByRegExp(str) {
	var s = "";
	if (str.length == 0) return "";
	s = str.replace(/&/g, "&amp;");
	s = s.replace(/</g, "&lt;");
	s = s.replace(/>/g, "&gt;");
	s = s.replace(/ /g, "&nbsp;");
	s = s.replace(/\'/g, "&#39;");
	s = s.replace(/\"/g, "&quot;");
	return s;
}
/*2.用正则表达式实现html解码*/
function htmlDecodeByRegExp(str) {
	var s = "";
	if (str.length == 0) return "";
	s = str.replace(/&amp;/g, "&");
	s = s.replace(/&lt;/g, "<");
	s = s.replace(/&gt;/g, ">");
	s = s.replace(/&nbsp;/g, " ");
	s = s.replace(/&#39;/g, "\'");
	s = s.replace(/&quot;/g, "\"");
	return s;
}

function stringToJson(str) {
	return eval('(' + str + ')');
}


// ajax封装
function JsAjax(url, type, dataType, data, success, error, cache, alone, async) {
	var type = type || 'get'; //请求类型
	var dataType = dataType || 'json'; //接收数据类型
	var async = async ||true; //异步请求
	var alone = alone || false; //独立提交（一次有效的提交）
	var cache = cache || false; //浏览器历史缓存
	var loadingIndex = false;
	var success = success || function(data) {
		setTimeout(function() {
			layer.msg(data.msg);
		}, 300);

		if (data.status) {
			setTimeout(function() {
				if (data.url) {
					location.replace(data.url);
				} else {
					location.reload(true);
				}
			}, 600000);
		} else {
			//服务器处理失败
			layer.msg("服务器开小差了，请稍后再试");
		}
	};
	var newSuccess = function(data) {
		if (data.token) {
			$(".Jshop_Token").val(data.token);
		}
		success(data);
	}

	var error = error || function(data) {
		layer.closeAll('loading');
		setTimeout(function() {
			if (data.status == 404) {
				layer.msg('请求失败，请求未找到');
			} else if (data.status == 503) {
				layer.msg('请求失败，服务器内部错误');
			} else {
				layer.msg('请求失败,网络连接超时');
			}
		}, 500);
	};
	$.ajax({
		'url': url,
		'data': data,
		'type': type,
		'dataType': dataType,
		'async': async,
		'success': newSuccess,
		'error': error,
		'jsonpCallback': 'jsonp' + (new Date()).valueOf().toString().substr(-4),
		'beforeSend': function() {
			loadingIndex = layer.msg('加载中', {
				icon: 16,
				shade: 0.01
			}, 300);
		},
		'complete': function(e) {
			layer.close(loadingIndex);
		}
	});
}

// submitAjax(post方式提交)
function submitJsAjax(form, success, cache, alone) {
	cache = cache || true;
	var form = $(form); //form校检
	var url = form.attr('action');
	var data = form.serialize();
	JsAjax(url, 'post', 'json', data, success, false, cache, alone, false);
}
//post提交数据
function JsPost(url, data, success, cache, alone) {
	if (!data.hasOwnProperty('__Jshop_Token__') && $(".Jshop_Token").length > 0) {
		data.__Jshop_Token__ = $(".Jshop_Token:last").val();
		data.validate_form = $("input[name='validate_form']").val();
	}
	JsAjax(url, 'post', 'json', data, success, false, cache, alone, false);
}

// ajax提交(get方式提交)
function JsGet(url, success, cache, alone) {
	JsAjax(url, 'get', 'json', {}, success, false, alone, false);
}

// jsonp跨域请求(get方式提交)
function jsonp(url, success, cache, alone) {
	JsAjax(url, 'get', 'jsonp', {}, success, false, cache, alone, false);
}

function getLabel(labels) {
	var html = '';
	if (labels && labels != null && typeof labels != 'undefined') {
		var label_style = '';
		if (typeof labels == 'object') {
			$.each(labels, function(i, obj) {
				label_style = '';
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
					default:
						label_style = '';
				}
				html += '<span class="layui-badge ' + label_style + '">' + obj.name + '</span>&nbsp;';
			});
		}
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
		content: "<img style='max-width: 350px;max-height: 350px;' src='" + imgUrl + "'>"
	});
}

function Trim(str) {
	return str.replace(/(^\s*)|(\s*$)/g, "");
}


//判断浏览器类型
function getBrowser() {
	var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
	var isOpera = userAgent.indexOf("Opera") > -1;
	if (isOpera) {
		return "Opera"
	}; //判断是否Opera浏览器
	if (userAgent.indexOf("Firefox") > -1) {
		return "FF";
	} //判断是否Firefox浏览器
	if (userAgent.indexOf("Chrome") > -1) {
		return "Chrome";
	}
	if (userAgent.indexOf("Safari") > -1) {
		return "Safari";
	} //判断是否Safari浏览器
	if (userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1 && !isOpera) {
		return "IE";
	}; //判断是否IE浏览器
	if (userAgent.indexOf("Trident") > -1) {
		return "Edge";
	} //判断是否Edge浏览器
}


/**
 *图片下载
 * */
function imgDownLoad(url) {
	if (getBrowser() === "IE" || getBrowser() === "Edge") {
		//IE
		saveAsIE(url)
	} else {
		//!IE
		saveAsOther(url);
	}
}
/**
 * IE下载
 */
function saveAsIE(src) {
	var imgWin = window.open(src, "", "width=1, height=1, top=5000, left=5000");
	for (; imgWin.document.readyState != "complete";) {
		if (imgWin.document.readyState == "complete") break;
	}
	imgWin.document.execCommand("SaveAs");
	imgWin.close();
}

function saveAsOther(src) {
	var $a = document.createElement('a');
	$a.setAttribute("href", src);
	$a.setAttribute("download", "img.png");
	var evObj = document.createEvent('MouseEvents');
	evObj.initMouseEvent('click', true, true, window, 0, 0, 0, 0, 0, false, false, true, false, 0, null);
	$a.dispatchEvent(evObj);
}

$(function() {
	//帮助tips
	$("body").on("click", ".help-tip", function() {
		var msg = $(this).attr('data-tip');
		var node = $(this);
		layer.tips(msg, node, {
			tips: [2, '#FF7159'],
			offset: '100px'
		});
	});

	//页面跳转
	$('body').on('click', '*[lay-href]', function() {
		var othis = $(this),
			href = othis.attr('lay-href'),
			text = othis.attr('lay-text'),
			router = parent.window.layui.router();
		parent.window.layui.admin.tabsPage.elem = othis;
		//执行跳转
		var topLayui = parent === self ? parent.window.layui : top.parent.window.layui;
		topLayui.index.openTabsPage(href, text || othis.text());
	});
});
/**
 * 优化弹窗
 * @param width
 * @param height
 * @returns {*[]}
 */
function setpage(width, height) {
	var pageHeight = $(window).height();
	var newHeight = 0;
	if (pageHeight * 0.7 > height) {
		newHeight = height+ 'px';
	} else {
		newHeight = '70%'
	}
	if ($(window).width() <= 1280) {
		return [width + 'px', newHeight];
	} else {
		return [width + 'px', height + 'px'];
	}
}

/**
 * 获取页面最大index
 */
function getMaxZIndex() {
	var maxZ = Math.max.apply(null,
			$.map($('body *'), function(e,n) {
				if ($(e).css('position') != 'static')
					return parseInt($(e).css('z-index')) || -1;
			}));
	return maxZ;
}