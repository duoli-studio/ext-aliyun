define(function(require) {
	var $ = require('jquery');
	require('bt3');
	var WOW = require('wow');
	var util = require('poppy/util');

	// pace , 顶部滑动
	var pace = require('pace');
	pace.start({
		document : false
	});

	$(function() {
		// Highlight the top nav as scrolling
		$('body').scrollspy({
			target: '.navbar-fixed-top',
			offset: 80
		});

		// Page scrolling feature
		$('a.page-scroll').bind('click', function(event) {
			var link = $(this);
			$('html, body').stop().animate({
				scrollTop: $(link.attr('href')).offset().top - 70
			}, 500);
			event.preventDefault();
		});
	});

	new WOW().init();

	/**
	 * cbpAnimatedHeader.js v1.0.0
	 * http://www.codrops.com
	 *
	 * Licensed under the MIT license.
	 * http://www.opensource.org/licenses/mit-license.php
	 *
	 * Copyright 2013, Codrops
	 * http://www.codrops.com
	 */
	(function() {

		var docElem = document.documentElement,
			header = document.querySelector( '.navbar-default' ),
			didScroll = false,
			changeHeaderOn = 200;

		function init() {
			window.addEventListener( 'scroll', function( event ) {
				if( !didScroll ) {
					didScroll = true;
					setTimeout( scrollPage, 250 );
				}
			}, false );
		}

		function scrollPage() {
			var sy = scrollY();
			if ( sy >= changeHeaderOn ) {
				util.classie().add( header, 'navbar-scroll' );
			}
			else {
				util.classie().remove( header, 'navbar-scroll' );
			}
			didScroll = false;
		}

		function scrollY() {
			return window.pageYOffset || docElem.scrollTop;
		}

		init();

	})();
});