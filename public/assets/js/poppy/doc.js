/**
 * doc 文档
 * @class lemon.doc
 */
define(function (require, exports) {
	var hl = require('highlight');
	var $ = require('jquery');
	
	/**
	 * 填充并且高亮
	 * @param source_id
	 * @param aim_id
	 * @param type
	 */
	exports.fill_and_highlight = function (source_id, aim_id, type) {
		$(function () {
			var text = '';
			if ( type == 'script' ) {
				text = '<script>' +
					$('#' + source_id).text() +
					'</' + 'script>';
				text = text.replace("\t" + '</' + 'script>', '</' + 'script>');
			} else if ( type == 'html' ) {
				text = $('#' + source_id).andSelf().html();
				text = text.replace(/\s*$/, '');
			} else {
				text = $('#' + source_id).html();
			}

			$('#' + aim_id).text(text);
			$('pre').each(function (i, block) {
				hl.highlightBlock(block);
			});
		})
	};


	exports.highlight = function () {
	    // source script  J_script_source
        // parent  J_wrap
        // html    J_html
        // code    J_script
		$(function () {
		    // script > script 标签中
            if ($('.J_script_source').length) {
                $('.J_script_source').each(function (e) {
                    var $this = $(this);
                    var $wrap = $this.parents('.J_wrap');
                    var script_text = $this.text();
                    var patt = /\n\s{4}/g;
                    var result = script_text.replace(patt, "\n");
                    var text = '<script>' +
                        result +
                        '</' + 'script>';
                    text = text.replace("\t" + '</' + 'script>', '</' + 'script>');
                    $wrap.find('.J_script').text(text);
                });
                $('.J_script, .J_html').each(function (i, block) {
                    hl.highlightBlock(block);
                });
            }
		})
	};

	/**
	 * 去除首尾空格
	 * @param id
	 */
	exports.trim_content = function (id) {
		var $id = $('#' + id);
		$id.html($.trim($id.html()));
	};
});