<?php
return [
	'disk'    => 'public',
	'folder'  => [
		'js_dir'   => 'assets/js',
		'font_dir' => 'assets/fonts',
		'scss_dir' => 'assets/scss',
	],
	'bower'   => [
		"bootstrap" => [
			"js"   => [
				'main'    => 'dist/js/bootstrap.js',
				'aim'     => 'bt3/{VERSION}/bootstrap.js',
				'publish' => [

				],
			],
			"font" => [
				'fonts/*' => 'bt3/',
			],
			"css"  => [
				'dist/css/*.css' => 'bt3/{VERSION}',
			],
			'shim' => ['jquery'],
		],
		"jquery"    => [
			"js" => [
				'main'    => 'jquery.min.js',
				'aim'     => 'jquery/{VERSION}/jquery.min.js',
				'publish' => [

				],
			],
		],
		"toastr"    => [
			"js"   => [
				'main' => 'toastr.min.js',
				'aim'  => 'jquery/toastr/{VERSION}/jquery.toastr.js',
			],
			"key"  => 'jquery.toastr',
			'shim' => ['jquery'],
		],
	],
	'global'  => [
		'url_site' => env('URL_SITE'),
	],
	'appends' => [

	],
];