define(function(require) {
	var $ = require('jquery');
	require('bt3');
	require('bt3.hover-dropdown');
	var poppy = require('poppy/util');
	require('poppy/cp');
	// 用于导航下部点击滑动
	require('smooth-scroll');

	// pace , 顶部滑动
	var pace = require('pace');
	pace.start({
		document : false
	});

	$(function() {
		_handle_footer();
		$('[data-toggle="tooltip"]').tooltip();
	});
	$(window).resize(_handle_footer);

	function _handle_footer() {
		var $p_footer = $('#footer');
		if (($('body').height() == poppy.get_viewport().height) || ($('body').height() < poppy.get_viewport().height - $p_footer.height())) {
			$p_footer.css({
				position : 'fixed',
				bottom   : 0
			}).fadeIn(500);
		}
		else {
			$p_footer.css({
				position : 'inherit'
			}).show();
		}
	}

});