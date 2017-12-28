/**
 * lemon 的核心帮助类
 * @class lemon.util
 */
define(function(require, exports) {
	var $      = require('jquery'),
	    layer  = require('jquery.layer'),
	    toastr = require('jquery.toastr');
	require('jquery.form');
	require('poppy/plugin');

	/**
	 * 返回浏览器的版本和ie的判定
	 * @returns {{version: *, safari: boolean, opera: boolean, msie: boolean, mozilla: boolean, is_ie8: boolean, is_ie9: boolean, is_ie10: boolean, is_rtl: boolean}}
	 */
	exports.browser = function() {
		var userAgent = navigator.userAgent.toLowerCase();
		return {
			version : (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [0, '0'])[1],
			safari  : /webkit/.test(userAgent),
			opera   : /opera/.test(userAgent),
			msie    : /msie/.test(userAgent) && !/opera/.test(userAgent),
			mozilla : /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent),
			is_ie8  : !!userAgent.match(/msie 8.0/),
			is_ie9  : !!userAgent.match(/msie 9.0/),
			is_ie10 : !!userAgent.match(/msie 10.0/),
			is_rtl  : $('body').css('direction') === 'rtl'
		}
	};

	exports.validate_config = function(rules, is_ajax, error_place) {
		var config = {
			ignore : '.ignore'
		};
		if (is_ajax) {
			config.submitHandler = function(form) {
				$(form).ajaxSubmit({
					success : exports.splash
				});
			};
		}
		if (error_place === 'bt3') {
			config.highlight = function(element) {
				$(element).closest('.form-group').addClass('has-error');
			};
			config.unhighlight = function(element) {
				$(element).closest('.form-group').removeClass('has-error');
			};
			config.errorElement = 'span';
			config.errorClass = 'help-block';
			config.errorPlacement = function(error, element) {
				if (element.parent('.form-group').length) {
					error.insertAfter(element.parent());
				}
				else {
					error.insertAfter(element);
				}
			}
		}
		if (error_place === 'bt3_poshytip') {
			config.highlight = function(element) {
				$(element).closest('.form-group').addClass('has-error');
			};
			config.unhighlight = function(element) {
				$(element).closest('.form-group').removeClass('has-error');
			};
			config.errorElement = 'span';
			config.errorClass = 'help-block';
			config.errorPlacement = function(error, element) {
				$(element).plugin_validate_tip(error.text());
			}
		}
		if (error_place === 'table') {
			config.highlight = function(element) {
				$(element).closest('tr').addClass('has-error');
			};
			config.unhighlight = function(element) {
				$(element).closest('tr').removeClass('has-error');
			};
			config.errorElement = 'span';
			config.errorClass = 'help-block';
			config.errorPlacement = function(error, element) {
				error.insertAfter(element);
			}
		}
		return $.extend(config, rules);
	};

	/**
	 * 点击加入收藏
	 * @param id
	 */
	exports.add_fav = function(id) {
		$(id).click(function() {
			if (document.all) {
				try {
					window.external.addFavorite(window.location.href, document.title);
				} catch (e) {
					alert("加入收藏失败，请使用Ctrl+D进行添加");
				}
			}
			else if (window.sidebar) {
				window.sidebar.addPanel(document.title, window.location.href, "");
			}
			else {
				alert("加入收藏失败，请使用Ctrl+D进行添加");
			}
		})
	};

	/**
	 * 滚动显示
	 * @param _sel
	 * @param sep_top
	 */
	exports.scroll_show = function(_sel, sep_top) {
		var $_sel = $(_sel);
		sep_top = typeof sep_top != 'undefined' ? sep_top : 0;
		$(window).scroll(function() {
			if ((document.documentElement.scrollTop + document.body.scrollTop) > sep_top) {
				$_sel.show();
			}
			else {
				$_sel.hide();
			}
		})
	};

	/**
	 * cbpAnimatedHeader.min.js
	 * 滚动的时候对样式进行监听
	 * http://www.codrops.com
	 *
	 * Licensed under the MIT license.
	 * http://www.opensource.org/licenses/mit-license.php
	 *
	 * @param selector
	 * @param sep_top
	 * @param class_name
	 */
	exports.scroll_switch = function(selector, sep_top, class_name) {
		var doc_element = document.documentElement, query_selector = document.querySelector(selector),
		    markable                                               = false, offset_location = sep_top;

		function go() {
			window.addEventListener("scroll", function(h) {
				if (!markable) {
					markable = true;
					setTimeout(do_listen, 250)
				}
			}, false)
		}

		function do_listen() {
			var height = offset_y();
			if (height >= offset_location) {
				exports.classie().add(query_selector, class_name)
			}
			else {
				exports.classie().remove(query_selector, class_name)
			}
			markable = false
		}

		function offset_y() {
			return window.pageYOffset || doc_element.scrollTop
		}

		go();
	};

	/**
	 * 根据后缀判定给定的地址是否是图片
	 * @param url
	 * @returns {boolean}
	 */
	exports.is_image = function(url) {
		var sTemp;
		var b = false;
		var opt = "jpg|gif|png|bmp|jpeg";
		var s = opt.toUpperCase().split("|");
		for (var i = 0; i < s.length; i++) {
			sTemp = url.substr(url.length - s[i].length - 1);
			sTemp = sTemp.toUpperCase();
			s[i] = "." + s[i];
			if (s[i] == sTemp) {
				b = true;
				break;
			}
		}
		return b;
	};

	/**
	 * 判定是否是邮箱
	 * @param str
	 * @returns {boolean}
	 */
	exports.is_email = function(str) {
		var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
		return reg.test(str);
	};

	/**
	 * 判定是否为手机号码
	 * @param str
	 * @returns {boolean|Array|{index: number, input: string}}
	 */
	exports.is_mobile = function(str) {
		var phone_number = str.replace(/\(|\)|\s+|-/g, "");
		return phone_number.length > 9 && phone_number.match(/^1[3|4|5|8|7][0-9]\d{4,8}$/);
	};

	/**
	 * 判定地址是否是 swf 文件
	 * @param url
	 * @returns {boolean}
	 */
	exports.is_swf = function(url) {
		var sTemp;
		var b = false;
		var opt = "swf";
		var s = opt.toUpperCase().split("|");
		for (var i = 0; i < s.length; i++) {
			sTemp = url.substr(url.length - s[i].length - 1);
			sTemp = sTemp.toUpperCase();
			s[i] = "." + s[i];
			if (s[i] == sTemp) {
				b = true;
				break;
			}
		}
		return b;
	};

	/**
	 * 对图片进行缩放 util.scale_image($('.J_img_scale'), 50);
	 * @param _sel
	 * @param w
	 * @param h
	 */
	exports.scale_image = function(_sel, w, h) {
		if (typeof h == 'undefined') {
			h = w;
		}
		$(_sel).each(function(i, ele) {
			if ($(ele).width() > $(ele).height()) {
				$(ele).css('width', w + 'px');
				$(ele).css('height', 'auto');
			}
			else {
				$(ele).css('height', h + 'px');
				$(ele).css('width', 'auto');
			}
		})
	};

	/**
	 * 计算图片的大小
	 * @param sUrl
	 * @param fCallback
	 */
	exports.image_size = function(sUrl, fCallback) {
		var img = new Image();
		img.src = sUrl + '?t=' + Math.random();    //IE下，ajax会缓存，导致onreadystatechange函数没有被触发，所以需要加一个随机数
		if (exports.browser.msie) {
			img.onreadystatechange = function() {
				if (this.readyState == "loaded" || this.readyState == "complete") {
					fCallback({width : img.width, height : img.height, url : sUrl});
				}
			};
		}
		else if (exports.browser().mozilla || exports.browser().safari || exports.browser().opera) {
			img.onload = function() {
				fCallback({width : img.width, height : img.height, url : sUrl});
			};
		}
		else {
			fCallback({width : img.width, height : img.height, url : sUrl});
		}
	};

	/**
	 * 图片头数据加载就绪事件 - 更快获取图片尺寸
	 * @version 2011.05.27
	 * @author  TangBin
	 * @see   http://www.planeart.cn/?p=1121
	 * @param {String}  图片路径
	 * @param {Function}  尺寸就绪
	 * @param {Function}  加载完毕 (可选)
	 * @param {Function}  加载错误 (可选)
	 * @example util.image_size('http://www.google.com.hk/intl/zh-CN/images/logo_cn.png', function () {
          alert('size ready: width=' + this.width + '; height=' + this.height);
       });
	 */
	exports.return_image_size = function() {
		var list = [], intervalId = null,

		    // 用来执行队列
		    tick                  = function() {
			    var i = 0;
			    for (; i < list.length; i++) {
				    list[i].end ? list.splice(i--, 1) : list[i]();
			    }
			    ;
			    !list.length && stop();
		    },

		    // 停止所有定时器队列
		    stop                  = function() {
			    clearInterval(intervalId);
			    intervalId = null;
		    };

		return function(url, ready, load, error) {
			var onready, width, height, newWidth, newHeight,
			    img = new Image();

			img.src = url;

			// 如果图片被缓存，则直接返回缓存数据
			if (img.complete) {
				ready.call(img);
				load && load.call(img);
				return;
			}
			;

			width = img.width;
			height = img.height;

			// 加载错误后的事件
			img.onerror = function() {
				error && error.call(img);
				onready.end = true;
				img = img.onload = img.onerror = null;
			};

			// 图片尺寸就绪
			onready = function() {
				newWidth = img.width;
				newHeight = img.height;
				if (newWidth !== width || newHeight !== height ||
					// 如果图片已经在其他地方加载可使用面积检测
					newWidth * newHeight > 1024
				) {
					ready.call(img);
					onready.end = true;
				}
				;
			};
			onready();

			// 完全加载完毕的事件
			img.onload = function() {
				// onload在定时器时间差范围内可能比onready快
				// 这里进行检查并保证onready优先执行
				!onready.end && onready();

				load && load.call(img);

				// IE gif动画会循环执行onload，置空onload即可
				img = img.onload = img.onerror = null;
			};

			// 加入队列中定期执行
			if (!onready.end) {
				list.push(onready);
				// 无论何时只允许出现一个定时器，减少浏览器性能损耗
				if (intervalId === null) intervalId = setInterval(tick, 40);
			}
		};
	}();

	/**
	 * 跳转到指定链接地址, 通过替换字符串的方式
	 * @param param
	 * @param value
	 * @param tripFile
	 */
	exports.go = function(param, value, tripFile) {
		var stringObj = window.location.href.replace(/#/, '');
		var params;
		typeof tripFile == 'undefined' ? tripFile = window.location.pathname : tripFile;
		var lstr = "&";

		if (stringObj.indexOf(tripFile + '?') == -1) {
			lstr = "?";
		}
		if (param.indexOf('|') >= 0) {
			params = param.split('|')
		}
		else {
			params = [param];
		}
		var urlGo = stringObj;
		for (var i in params) {
			var param_re = params[i];
			var reg = new RegExp(param_re + "=[0-9a-zA-Z,-_]*", "g"); //创建正则RegExp对象
			var ch = stringObj.indexOf(param_re + '=');
			if (ch == -1) {
				urlGo += lstr + param_re + "=" + value;
			}
			if (ch != -1) {
				urlGo = urlGo.replace(reg, param_re + "=" + value);
			}

		}
		window.location = urlGo;
	};

	/**
	 * 设置为主页
	 */
	exports.set_homepage = function() {
		if (document.all) {
			document.body.style.behavior = 'url(#default#homepage)';
			document.body.setHomePage(window.location.href);
		}
		else if (window.sidebar) {
			if (window.netscape) {
				try {
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
				} catch (e) {
					alert("该操作被浏览器拒绝，如果想启用该功能，请在地址栏内输入 about:config,然后将项 signed.applets.codebase_principal_support 值该为true");
				}
			}
			var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
			prefs.setCharPref('browser.startup.homepage', window.location.href);
		}
		else {
			alert('您的浏览器不支持自动自动设置首页, 请使用浏览器菜单手动设置!');
		}
	};

	/**
	 * 通过 post 的方法异步读取数据
	 * @param targetPhp
	 * @param queryString
	 * @param success
	 * @param method
	 */
	exports.make_request = function(targetPhp, queryString, success, method) {
		if (typeof  queryString == 'string') {
			queryString += queryString.indexOf('&') < 0
				? '_token=' + exports.csrf_token()
				: '&_token=' + exports.csrf_token();
		}
		if (typeof queryString == 'object') {
			queryString['_token'] = exports.csrf_token();
		}
		if (typeof queryString == 'undefined') {
			queryString = {
				'_token' : exports.csrf_token()
			}
		}
		if (typeof success == 'undefined') {
			success = exports.splash;
		}
		if (typeof method == 'undefined') {
			method = 'post';
		}
		$.ajax({
			async   : false,
			cache   : false,
			type    : method,
			url     : targetPhp,
			data    : queryString,
			success : function(data) {
				var obj_data = exports.to_json(data);
				success(obj_data);
			}
		});
	};

	/**
	 * 通过 json p 的方式获取数据
	 * @param targetPhp
	 * @param queryString
	 * @param success
	 */
	exports.make_jsonp = function(targetPhp, queryString, success) {
		$.getJSON(targetPhp + '&callback=?', queryString, success);
	};

	/**
	 * 生成随机字符
	 * @param length
	 * @returns {string}
	 */
	exports.random = function(length) {
		if (typeof length == 'undefined' || parseInt(length) == 0) {
			length = 18;
		}
		var chars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
		var str = '';
		for (var i = 0; i < length; i++) {
			str += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		return str;
	};


	/**
	 * 方便添加维护类
	 * @returns {{hasClass: *, addClass: *, removeClass: *, toggleClass: toggleClass, has: *, add: *, remove: *, toggle: toggleClass}}
	 */
	exports.classie = function() {
		function classReg(className) {
			return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
		}

		// classList support for class management
		// altho to be fair, the api sucks because it won't accept multiple classes at once
		var hasClass, addClass, removeClass;

		if ('classList' in document.documentElement) {
			hasClass = function(elem, c) {
				return elem.classList.contains(c);
			};
			addClass = function(elem, c) {
				elem.classList.add(c);
			};
			removeClass = function(elem, c) {
				elem.classList.remove(c);
			};
		}
		else {
			hasClass = function(elem, c) {
				return classReg(c).test(elem.className);
			};
			addClass = function(elem, c) {
				if (!hasClass(elem, c)) {
					elem.className = elem.className + ' ' + c;
				}
			};
			removeClass = function(elem, c) {
				elem.className = elem.className.replace(classReg(c), ' ');
			};
		}

		function toggleClass(elem, c) {
			var fn = hasClass(elem, c) ? removeClass : addClass;
			fn(elem, c);
		}

		return {
			// full names
			hasClass    : hasClass,
			addClass    : addClass,
			removeClass : removeClass,
			toggleClass : toggleClass,
			// short names
			has         : hasClass,
			add         : addClass,
			remove      : removeClass,
			toggle      : toggleClass
		};
	};

	/**
	 * 大图显示对应缩略图
	 * @param big    大图列表ul Id
	 * @param small  缩略图列表ul Id
	 */
	exports.show_thumb = function(big, small) {
		var $bigImg = $(big).find("li");
		$(small).find("li:first").children("i").show();
		$(small).find("li").each(function(index, element) {
			$(this).hover(function() {
				$(this).children("i").show();
				$(this).siblings().children("i").hide();
				$bigImg.eq(index).show().siblings().hide();
			});
		});
	};

	/**
	 * 对象转换成url地址
	 * @param obj
	 * @param url
	 * @returns {*}
	 */
	exports.obj_to_url = function(obj, url) {
		var str = "";
		for (var key in obj) {
			if (str != "") {
				str += "&";
			}
			str += key + "=" + obj[key];
		}
		if (typeof url != 'undefined') {
			return url.indexOf('?') >= 0 ? url + '&' + str : url + '?' + str;
		}
		else {
			return str;
		}
	};

	/**
	 * 计算对象的长度
	 * @param obj
	 * @returns {number}
	 */
	exports.obj_size = function(obj) {
		var count = 0;

		if (typeof obj === "object") {

			if (Object.keys) {
				count = Object.keys(obj).length;
			}
			else if (window._) {
				count = _.keys(obj).length;
			}
			else if (window.$) {
				count = $.map(obj, function() {
					return 1;
				}).length;
			}
			else {
				for (var key in obj) if (obj.hasOwnProperty(key)) count++;
			}

		}

		return count;
	};

	/*
	 * 提示信息
	 * @params word  String 提示信息
	 * */
	exports.splash = function(resp, append_callback) {
		var obj_resp = exports.to_json(resp);
		var obj_init = {
			message : 'No Message Send By Server!',
			status  : 1,
			data    : {
				callback : '',
				show     : 'tip',
				time     : 0
			}
		};

		obj_resp = $.extend(obj_init, obj_resp);
		if (obj_resp.data.show === 'tip') {
			obj_resp.data.time = parseInt(obj_resp.data.time) ? parseInt(obj_resp.data.time) : 0;
			var jump_time;
			if (obj_resp.data.location) {
				jump_time = 800;
			}
			if (obj_resp.data.reload) {
				jump_time = 800;
			}
			if (obj_resp.data.reload_opener) {
				jump_time = 800;
			}
			setTimeout(function() {
				toastr.options = {
					closeButton   : true,
					progressBar   : true,
					showMethod    : 'slideDown',
					timeOut       : 3000,
					positionClass : "toast-top-center"
				};
				if (obj_resp.status === 'success' || obj_resp.status === 0) {
					toastr.success(obj_resp.message);
				}
				else {
					toastr.error(obj_resp.message);
				}
			}, jump_time);
		}

		if (obj_resp.show === 'dialog') {
			delete obj_resp.show;
			var conf = {};
			conf.title = !conf.hasOwnProperty('title') ? conf.msg : conf.title;
			conf.content = !conf.hasOwnProperty('content') ? conf.msg : conf.content;
			dialog(conf).show();
			return false;
		}

		if (obj_resp.status === 'callback' || obj_resp.callback) {
			var func = obj_resp.callback;
			setTimeout(function() {
				eval(func + ";");
			}, obj_init.time);
		}

		if (obj_resp.reload) {
			setTimeout(function() {
				window.location.reload()
			}, obj_init.time);
			return;
		}
		if (obj_resp.top_reload) {
			setTimeout(function() {
				top.window.location.reload()
			}, obj_init.time);
			return;
		}

		if (obj_resp.location) {
			setTimeout(function() {
				window.location.href = obj_resp.location;
			}, obj_init.time);
		}

		if (obj_resp.top_location) {
			setTimeout(function() {
				top.window.location.href = obj_resp.top_location;
			}, obj_init.time);
		}

		if (obj_resp.reload_opener) {
			setTimeout(function() {
				var opener = exports.opener(obj_resp.reload_opener);
				opener.location.reload();
			}, obj_init.time);
		}

		if (obj_resp.iframe_close) {
			setTimeout(function() {
				var opener = exports.opener(obj_resp.iframe_close);
				opener.iframe.close();
			}, obj_init.time);
		}

		if (typeof append_callback === 'function') {
			append_callback(obj_resp);
		}
	};


	/**
	 * 追加元素到对象
	 * @param append
	 * @returns {{}}
	 */
	exports.append_to_obj = function(append) {
		var data = {};
		if (append) {
			var appends = [append];
			if (append.indexOf(',') >= 0) {
				appends = append.split(',');
			}
			for (var i in appends) {
				var item = appends[i];
				var re = /(.*)\((.*)\)/;
				var m;

				if ((m = re.exec(item)) !== null) {
					if (m.index === re.lastIndex) {
						re.lastIndex++;
					}
				}

				if (m[1].indexOf('checked') >= 0 && m[1].indexOf('radio') < 0) {
					var id_array = [];
					$(m[1]).each(function() {
						id_array.push($(this).val());//向数组中添加元素
					});
					data[m[2]] = id_array;//将数组元素连接起来以构建一个字符串
				}
				else {
					data[m[2]] = $(m[1]).val();
				}

			}
		}
		return data;
	};

	/**
	 * 条件转换
	 * @param append
	 * @returns {{}}
	 */
	exports.condition_to_obj = function(append) {
		var data = {};
		if (append) {
			var appends = append.split(',');
			for (var i in appends) {
				var item = appends[i];
				var re = /(.*):(.*)/;
				var m;
				if ((m = re.exec(item)) !== null) {
					if (m.index === re.lastIndex) {
						re.lastIndex++;
					}
					data[m[1]] = m[2];
				}
			}
		}
		return data;
	};

	/**
	 * 重新载入当前页面
	 */
	exports.refresh = function() {
		top.window.location.reload();
	};

	/**
	 * 获取openner
	 * @param workspace
	 * @returns {*}
	 */
	exports.opener = function(workspace) {
		var opener = top.frames[workspace];
		if (typeof opener == 'undefined') {
			opener = top;
		}
		return opener;
	};

	/**
	 * 重新加载验证码
	 * @param id
	 */
	exports.reload_captcha = function(id) {
		$(id).trigger('click');
	};

	/**
	 * 按钮交互
	 * @param btn_selector
	 * @param data
	 * @param error_submit
	 */
	exports.button_interaction = function(btn_selector, data, error_submit) {
		var objData;
		if (typeof data == 'undefined' || !isNaN(parseInt(data))) {
			$(btn_selector).attr('disabled', true);
			if (!isNaN(parseInt(data))) {
				var time = parseInt(data);
				setTimeout(function() {
					$(btn_selector).attr('disabled', false);
				}, time * 1000);
			}
		}
		objData = exports.to_json(data);
		if (objData.status == 'error') {
			$(btn_selector).attr('disabled', false);
			if (typeof error_submit != 'undefined') {
				$(btn_selector).html(error_submit);
			}
		}
	};

	/**
	 * 字串转 json
	 * @param resp
	 * @returns {*}
	 */
	exports.to_json = function(resp) {
		var objResp;
		if (typeof resp == 'object') {
			objResp = resp;
		}
		else {
			if ($.trim(resp) == '') {
				objResp = {};
			}
			else {
				objResp = $.parseJSON(resp);
			}
		}
		return objResp;
	};

	/**
	 * 按钮倒计时工具
	 * @param btn_selector
	 * @param str
	 * @param time
	 * @param end_str
	 */
	exports.countdown = function(btn_selector, str, time, end_str) {
		var count = time;
		var handlerCountdown;
		var $btn = $(btn_selector);
		var displayStr = typeof end_str != 'undefined' ? end_str : $btn.text();

		handlerCountdown = setInterval(_countdown, 1000);
		$btn.attr("disabled", true);

		function _countdown() {
			var count_str = str.replace(/\{time\}/, count);
			$btn.text(count_str);
			if (count == 0) {
				$btn.text(displayStr).removeAttr("disabled");
				clearInterval(handlerCountdown);
			}
			count--;
		}
	};

	/**
	 * 事件请求, 使用post 方法
	 * @param $this
	 * @param splash_func
	 * @returns {boolean}
	 */
	exports.request_event = function($this, splash_func) {
		// confirm
		var str_confirm = $this.attr('data-confirm');
		if (str_confirm == 'true') {
			str_confirm = '您确定删除此条目 ?';
		}
		if (str_confirm) {
			if (!confirm(str_confirm)) return false;
		}
		var append = $this.attr('data-append');
		var data = exports.append_to_obj(append);

		var condition_str = $this.attr('data-condition');
		var condition = exports.condition_to_obj(condition_str);
		for (var i in data) {
			if (condition.hasOwnProperty(i) && !data.hasOwnProperty(i)) {
				splash_func({
					'status' : 'error',
					'msg'    : condition[i]
				});
				return false;
			}
		}

		// do request
		var href = $this.attr('href');
		if (!href) {
			href = $this.attr('data-url');
		}
		data._token = exports.csrf_token();
		$.post(href, data, splash_func);
	};

	/**
	 * 获取页面中的 csrf token
	 * @returns {*|jQuery}
	 */
	exports.csrf_token = function() {
		return $('meta[name="csrf-token"]').attr('content');
	};

	/**
	 * 预览图像地址
	 * @param imgSrc
	 * @param w
	 * @returns {boolean}
	 */
	exports.image_popup_show = function(imgSrc, w) {
		if (!imgSrc) {
			exports.splash('error', '没有图像文件!');
			return false;
		}
		exports.image_size(imgSrc, _popup_show);

		/**
		 * imgObj.width   imgObj.height  imgObj.url
		 * @param imgObj
		 * @private
		 */
		function _popup_show(imgObj) {
			var _w = imgObj.width;
			var _h = imgObj.height;
			if (typeof w != 'undefined' && imgObj.width > w) {
				_w = w;
				_h = parseInt(_w * imgObj.height / imgObj.width);
			}
			var imgStr = '<img src="' + imgObj.url + '" width="' + _w + '" height="' + _h + '" />';
			layer.open({
				title   : '图片预览',
				content : imgStr
			});
		}

	};

	/**
	 * 图片预览显示
	 * @param ctr
	 * @param imgUrl
	 * @param w
	 * @returns {boolean}
	 */
	exports.image_hover_show = function(ctr, imgUrl, w) {
		if (typeof imgUrl == 'undefined' || !imgUrl) {
			exports.splash('error', '没有图像文件!');
			return false;
		}
		exports.image_size(imgUrl, _previewShow);

		/**
		 * imgObj.width   imgObj.height  imgObj.url
		 * @param imgObj
		 * @private
		 */
		function _previewShow(imgObj) {
			var _w = imgObj.width;
			var _h = imgObj.height;
			if (typeof w != 'undefined' && imgObj.width > w) {
				_w = w;
				_h = parseInt(_w * imgObj.height / imgObj.width);
			}

			var imgStr = '<div id="J_previewShow" class="file-element"><div class="file-preview" style="left: 110px;top:0;">' +
				'<img src="' + imgObj.url + '" width="' + _w + '" height="' + _h + '" />' +
				'</div>';
			$('#J_previewShow').remove();
			$(ctr).append(imgStr);
			$('#J_previewShow>.file-preview').show();
		}
	};

	/**
	 * 图片预览显示关闭
	 */
	exports.image_hover_hide = function() {
		$('#J_previewShow').remove();
	};

	/**
	 * 获取当前视窗的大小
	 * To get the correct viewport width
	 * based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
	 * @returns {{width: *, height: *}}
	 */
	exports.get_viewport = function() {
		var e = window,
		    a = 'inner';
		if (!('innerWidth' in window)) {
			a = 'client';
			e = document.documentElement || document.body;
		}

		return {
			width  : e[a + 'Width'],
			height : e[a + 'Height']
		};
	};

	/**
	 * 是否是触摸设备
	 * check for device touch support
	 * @returns {boolean}
	 */
	exports.is_touch_device = function() {
		try {
			document.createEvent("TouchEvent");
			return true;
		} catch (e) {
			return false;
		}
	};

	/**
	 * 获取唯一ID
	 * @param prefix
	 * @returns {string}
	 */
	exports.get_unique_id = function(prefix) {
		var _pre = (typeof prefix == 'undefined') ? 'prefix_' : prefix;
		return _pre + Math.floor(Math.random() * (new Date()).getTime());
	};

	/**
	 * 获取url中的参数值
	 * @param paramName
	 * @returns {string}
	 */
	exports.get_url_parameter = function(paramName) {
		var searchString   = window.location.search.substring(1),
		    i, val, params = searchString.split("&");

		for (i = 0; i < params.length; i++) {
			val = params[i].split("=");
			if (val[0] == paramName) {
				return unescape(val[1]);
			}
		}
		return '';
	};

	/**
	 * 检查浏览器是否支持 local 存储
	 * @returns {boolean}
	 */
	exports.local_storage_support = function() {
		return (('localStorage' in window) && window['localStorage'] !== null)
	};

	/**
	 * 全屏
	 * @param ele
	 */
	exports.full_screen = function(ele) {
		var element;
		if (typeof ele == 'undefined') {
			element = document.documentElement;
		}
		else {
			element = document.getElementById(ele);
		}
		if (element.requestFullscreen) {
			element.requestFullscreen();
		}
		else if (element.mozRequestFullScreen) {
			element.mozRequestFullScreen();
		}
		else if (element.webkitRequestFullscreen) {
			element.webkitRequestFullscreen();
		}
		else if (element.msRequestFullscreen) {
			element.msRequestFullscreen();
		}
	};

	/**
	 * 退出全屏
	 */
	exports.exit_full_screen = function() {
		if (document.exitFullscreen) {
			document.exitFullscreen();
		}
		else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		}
		else if (document.webkitExitFullscreen) {
			document.webkitExitFullscreen();
		}
	};

	/**
	 * 执行一次动画
	 * @param selector
	 * @param animation_name
	 */
	exports.animate = function(selector, animation_name) {
		var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		$(selector).addClass('animated ' + animation_name).one(animationEnd, function() {
			$(this).removeClass('animated ' + animation_name);
		});
	}
});