define(function(require) {
	var $ = require('jquery');
	var util = require('poppy/util');

	return {
		fix_height      : function() {
			var heightWithoutNavbar = $("body > #wrapper").height() - 61;
			$(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");

			var navbarHeigh = $('nav.navbar-default').height();
			var $page_wrapper = $('#page-wrapper');
			var wrapperHeigh = $page_wrapper.height();

			if (navbarHeigh > wrapperHeigh) {
				$page_wrapper.css("min-height", navbarHeigh + "px");
			}

			if (navbarHeigh < wrapperHeigh) {
				$page_wrapper.css("min-height", $(window).height() + "px");
			}
		},
		animation_hover : function(element, animation) {
			element = $(element);
			element.hover(
				function() {
					element.addClass('animated ' + animation);
				},
				function() {
					//wait for animation to finish before removing classes
					window.setTimeout(function() {
						element.removeClass('animated ' + animation);
					}, 2000);
				});
		},
		smoothly_menu   : function() {
			var $body = $('body');
			if (!$body.hasClass('mini-navbar') || $body.hasClass('body-small')) {
				// Hide menu in order to smoothly turn on when maximize menu
				$('#side-menu').hide();
				// For smoothly turn on menu
				setTimeout(
					function() {
						$('#side-menu').fadeIn(500);
					}, 100);
			}
			else if ($body.hasClass('fixed-sidebar')) {
				$('#side-menu').hide();
				setTimeout(
					function() {
						$('#side-menu').fadeIn(500);
					}, 300);
			}
			else {
				// Remove all inline style from jquery fadeIn function to reset menu state
				$('#side-menu').removeAttr('style');
			}
		},


		animate_footer : function() {
			var $p_footer = $('.J_p_footer');
			if (($('body').height() == util.get_viewport().height) || ($('body').height() < util.get_viewport().height - $p_footer.height())) {
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

	}
});