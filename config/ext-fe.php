<?php
return [
	/*
	|--------------------------------------------------------------------------
	| disk to save public url
	|--------------------------------------------------------------------------
	*/
	'disk'    => 'public',

	/*
	|--------------------------------------------------------------------------
	| folder position
	|--------------------------------------------------------------------------
	*/
	'folder'  => [
		'js_dir'   => 'assets/js',
		'font_dir' => 'assets/fonts',
		'scss_dir' => 'assets/scss',
	],

	/*
	|--------------------------------------------------------------------------
	| bower definition
	|--------------------------------------------------------------------------
	| js       : js path define
	| js.main  : main file
	| js.aim   : aim file position {VERSION} is version number.
	| font     : font reflection
	| css      : css reflection
	| shim     : dependence in requirejs.
	| key      : config key
	*/
	'bower'   => [
		"ace-builds" => [
			"js"  => [
				'main' => 'src-min/ace.js',
				'aim'  => 'ace/{VERSION}/ace.js',
			],
			'key' => 'ace',
		],
		"bootstrap"  => [
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
			'key'  => 'bt3',
		],


		"jquery" => [
			"js" => [
				'main'    => 'jquery.min.js',
				'aim'     => 'jquery/{VERSION}/jquery.min.js',
				'publish' => [

				],
			],
		],

		"toastr" => [
			"js"   => [
				'main' => 'toastr.min.js',
				'aim'  => 'jquery/toastr/{VERSION}/jquery.toastr.js',
			],
			"key"  => 'jquery.toastr',
			'shim' => ['jquery'],
		],

		"layer" => [
			"js"   => [
				'main' => 'src/layer.js',
				'aim'  => 'jquery/layer/{VERSION}/jquery.layer.js',
			],
			"css"  => [
				'src/theme/default/*' => 'jquery/layer/default',
				'src/theme/moon/*'    => 'jquery/layer/moon',
			],
			"key"  => 'jquery.layer',
			'shim' => ['jquery'],
		],


		"jquery-form" => [
			"js"   => [
				'aim' => 'jquery/form/{VERSION}/jquery.form.js',
			],
			"key"  => 'jquery.form',
			'shim' => ['jquery'],
		],

		"jquery-validation" => [
			"js"   => [
				'aim' => 'jquery/validation/{VERSION}/jquery.validation.js',
			],
			"key"  => 'jquery.validation',
			'shim' => ['jquery'],
		],
	],
	'global'  => [
		'url_site' => env('URL_SITE'),
	],
	'appends' => [
		'lemon' => "/assets/js/lemon",
	],
];