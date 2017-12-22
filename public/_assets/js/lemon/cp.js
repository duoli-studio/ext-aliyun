/**
 * 控制面板
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2013-2016 lemon team
 */
define(function (require) {
	var $     = require('jquery'),
	    util  = require('lemon/util'),
	    layer = require('jquery.layer'),
	    $body = $('body');
	require('jquery.form');
	require('jquery.validate');

	$(function () {
		// 对话框, 用于显示信息提示
		// 不能用于生成图片组件
		// @see http://stackoverflow.com/questions/12271105/swfupload-startupload-fails-if-not-called-within-the-file-dialog-complete-hand
		$('.J_dialog').on('click', function (e) {
			// confirm
			var tip     = $(this).attr('data-tip');
			var element = $(this).attr('data-element');
			var title   = $(this).attr('data-title') ? $(this).attr('data-title') : $(this).html();
			var width   = parseInt($(this).attr('data-width')) ? parseInt($(this).attr('data-width')) : 400;
			var height  = parseInt($(this).attr('data-height')) ? parseInt($(this).attr('data-height')) : '';
			var area    = height ? [width + 'px', height + 'px'] : width + 'px';

			// 获取到元素的 html, 并且存入到当前元素
			if (element) {
				tip = $(element).html();
				$(this).attr('data-tip', tip);
			}

			// open with layer
			layer.open({
				// type   : 1,
				title  : title,
				content: tip,
				area   : area
			});
			e.preventDefault();
		});

		// 弹出 iframe url
		$('a.J_iframe,tr.J_iframe,tbody.J_iframe').on('click', function (e) {
			var $this = $(this);
			// confirm
			var href  = $(this).attr('href');
			if (!href) {
				href = $(this).attr('data-href');
			}
			var title = $(this).attr('data-title') ? $(this).attr('data-title') : '';
			if (!title) {
				title = $(this).attr('title') ? $(this).attr('title') : '';
			}
			if (!title) {
				title = $(this).attr('data-original-title') ? $(this).attr('data-original-title') : $(this).html();
			}
			var width       = parseInt($(this).attr('data-width')) ? parseInt($(this).attr('data-width')) : '500';
			var height      = parseInt($(this).attr('data-height')) ? parseInt($(this).attr('data-height')) : '500';
			var shade_close = $(this).attr('data-shade_close') == 'true';
			var append      = $this.attr('data-append');
			var data        = util.append_to_obj(append);
			href            = util.obj_to_url(data, href);
			layer.open({
				type      : 2,
				content   : href,
				area      : [width + 'px', height + 'px'],
				title     : title,
				shadeClose: shade_close
			});
			e.preventDefault();
		});

		// 全选 start
		$('.J_checkAll, .J_check_all').on('click change', function () {
			if (this.checked) {
				$(".J_checkItem, .J_check_item").prop('checked', true)
			} else {
				$(".J_checkItem, .J_check_item").prop('checked', false)
			}
		});

		// 确定 请求后台操作, POST 方法
		$body.on('click', '.J_request', function (e) {
			util.request_event($(this), util.splash);
			e.preventDefault();
		});

		// 给用户增加访问量
		$('.J_view').on('click', function (e) {
			var url = $(this).attr('data-url');
			if (!url) return;
			util.make_request(url, {}, function () {
			});
		});

		// 图片预览
		$body.on('click', '.J_image_preview', function (e) {
			var _src = $(this).attr('src');
			if (_src.indexOf('nopic') >= 0) {
				return;
			}
			if (e.ctrlKey) {
				window.open($(this).attr('src'), '_blank')
			} else {
				if (!_src) {
					util.splash('error', '没有图像文件!');
					return false;
				}
				util.image_size(_src, _popup_show);

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
						type      : 1,
						title     : false,
						closeBtn  : 0,
						area      : _w,
						skin      : 'layui-layer-lan', //没有背景色
						shadeClose: true,
						content   : imgStr
					});
				}
			}
		});
		$body.on('mouseover', '.J_image_preview', function (e) {
			var _src = $(this).attr('data-src');
			if (!_src) {
				return;
			}
			if (_src.indexOf('nopic') >= 0) {
				return;
			}
			var $attach = $(this);
			var width   = 350;
			// util.image_hover_show('body', _src, 500);
			util.image_size(_src, _previewShow);

			/**
			 * imgObj.width   imgObj.height  imgObj.url
			 * @param imgObj
			 * @private
			 */
			function _previewShow(imgObj) {
				var _w = imgObj.width;
				var _h = imgObj.height;
				if (imgObj.width > width) {
					_w = width;
					_h = parseInt(_w * imgObj.height / imgObj.width);
				}

				var imgStr = '<div class="file-preview">' +
					'<img src="' + imgObj.url + '" width="' + _w + '" height="' + _h + '" />' +
					'</div>';
				layer.tips(imgStr, $attach, {
					tips    : [2, '#bebebe'],
					time    : 4000,
					maxWidth: 400
				})
			}
		});
		$body.on('mouseout', '.J_image_preview', function () {
			var index = layer.tips();
			layer.close(index);
		});

		// reload
		$('.J_reload').on('click', function () {
			window.location.reload();
		});

		// print
		$('.J_print').on('click', function () {
			window.print();
		});

		/**
		 * 把当前表单的数据临时提交到指定的地址
		 * .J_submit  用法
		 * data-url    : 设置本表单请求的URL
		 * data-ajax   : true|false  设置是否进行ajax 请求
		 * data-confirm: 确认操作提交的提示信息
		 * data-error  : 错误提交方式
		 */
		$body.on('click', '.J_submit', function (e) {
			var request_url = $(this).attr('data-url');
			// 不对请求的地址进行判定, 因为如果是请求当前url的时候, 可以不用填写
			// if ( !request_url ) {
			// 	util.splash({
			// 		'status' : 'error',
			// 		'msg'    : '当前请求没有设置请求的URI'
			// 	});
			// 	return false;
			// }
			var $form = $(this).parents('form');
			if (!$form.length) {
				util.splash({
					status: 'error',
					msg   : '您不在表单范围内， 请添加到表单范围内'
				});
				return false;
			}

			var old_url = $form.attr('action');
			if (!request_url) {
				request_url = old_url;
			}
			// confirm
			var str_confirm = $(this).attr('data-confirm');
			if (str_confirm == 'true') {
				str_confirm = '您确定删除此条目 ?';
			}
			if (str_confirm && !confirm(str_confirm)) return false;

			var data_ajax   = $(this).attr('data-ajax');
			var data_method = $(this).attr('data-method') ? $(this).attr('data-method') : 'post';

			$form.attr('action', request_url);
			$form.attr('method', data_method);

			// 显示 layer 层
			var index = layer.load(0, {shade: false});
			if ((data_ajax == 'false')) {
				$form.submit();
			} else {
				var $btn = $(this);
				util.button_interaction($btn, 5);
				$form.ajaxSubmit({
					success: function (data) {
						layer.close(index);
						util.splash(data);
						util.button_interaction($btn, data)
					}
				});
			}
			// 还原
			$form.attr('action', old_url);
			e.preventDefault();
		});

		// 确定 ajax删除 的操作
		$('a.J_delete').on('click', function (e) {
			// confirm
			var str_confirm = $(this).attr('data-confirm');
			str_confirm     = str_confirm ? str_confirm : '您确定要删除吗?';
			if (!confirm(str_confirm)) return false;

			// do request
			var href  = $(this).attr('href');
			var token = util.csrf_token();
			$.post(href, {
				_method: 'DELETE',
				_token : token
			}, util.splash);
			e.preventDefault();
		});

		/**
		 * 表单的验证提交
		 */
		$('.J_validate').each(function (i, element) {
			var $form = $(element).parents('form');
			if (!$form.length) {
				return;
			}

			// confirm
			var data_ajax = $form.attr('data-ajax');
			var conf;
			if ((data_ajax == 'false')) {
				conf = util.validate_config({}, false, 'table');
				$form.validate(conf);
				// ajax 禁用掉默认
				$(element).on('click', function (e) {
					e.preventDefault();
				})
			} else {
				conf = util.validate_config({}, true, 'table');
				$form.validate(conf);
			}
		});


		/**
		 * 禁用按钮
		 */
		$('a.J_delay, button.J_delay').on('click', function (e) {
			var $this = $(this);
			var tag   = $this.prop("tagName").toLowerCase();
			if (tag == 'a' && !$this.data('delay')) {
				var _href = $(this).attr('href');
				$this.attr('href', 'javascript:void(0)').addClass('disabled').attr('data-delay', 'ing');
				setTimeout(function () {
					$this.attr('href', _href).removeClass('disabled').removeAttr('data-delay');
				}, 3000);
				e.preventDefault();
			}
			if (tag == 'button' && !$this.data('delay')) {
				$this.addClass('disabled');
				if ($(this).parents('form') && $this.prop('type') == 'submit') {
					$(this).parents('form').submit(function () {
						$this.prop('disabled', true);
					});
				}
				setTimeout(function () {
					$this.removeClass('disabled');
					$this.prop('disabled', false);
				}, 3000)
			}

		});

		/**
		 * 返回传输的内容, 并且将内容显示在弹窗中
		 */
		$(".J_info").each(function () {
			var $this      = $(this);
			var data_url   = $this.attr("data-url");
			var layer_id   = "";
			var index      = '';
			var common_opt = {
				type    : 1,
				area    : ['400px', 'auto'],
				tips    : [2, '#fff'],
				closeBtn: 0,
				shade   : 0,
				shift   : 5
			};
			$this.on("mouseover", function () {
				$.ajax({
					type   : 'get',
					url    : data_url,
					data   : {
						_token : util.csrf_token()
					},
					success: function (data) {
						var com_content = data.content; //html内容
						var com_opt = $.extend({}, common_opt, {
							content: com_content,
							success: function (layer_obj) {
								layer_id = layer_obj.selector;
							}
						});
						index = layer.open(com_opt);
					},
					error  : function (XMLHttpRequest, textStatus, errorThrown) {
						alert(XMLHttpRequest.status);
						alert(XMLHttpRequest.readyState);
						alert(textStatus);
					}
				})
			}).on("mouseout", function () {
				var count = 0;
				$(layer_id).on('mouseover', function () {
					count = 1;
				}).on('mouseout', function () {
					count = 0;
				});
				$this.on('mouseover', function () {
					count = 1;
				});
				$body.on('mouseover', function () {
					if (count == 3) {
						clearInterval(t);
					}
				});
				var t = setInterval(function () {
					if (count == 0) {
						layer.close(index);
						count = 3;
					}
				}, 150);
			})
		})
	});
});
