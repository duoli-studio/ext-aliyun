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

		"animate.css" => [
			"css" => [
				'animate.css' => 'animate/animate.css',
			],
			"key" => 'animate.css',
		],

		"ace-builds" => [
			"js"   => [
				'main'    => 'src/ace.js',
				'aim'     => 'ace/{VERSION}/ace.js',
				'dispose' => [
					'src/*' => 'ace/{VERSION}/',
				],
				'config'  => [
					// 和 key 相同, 覆盖掉 key 的定义位置
					'__same' => '',
				],
			],
			'key'  => 'ace',
			'shim' => [
				"exports" => "ace",
			],
		],


		"bootstrap" => [
			"js"   => [
				'main' => 'dist/js/bootstrap.js',
				'aim'  => 'bt3/{VERSION}/bootstrap.js',
			],
			"css"  => [
				'dist/css/*.css' => 'bt3/{VERSION}/css/',
				'dist/fonts/*'   => 'bt3/{VERSION}/fonts/',
			],
			'shim' => ['jquery'],
			'key'  => 'bt3',
		],

		"bootstrap-hover-dropdown" => [
			"js"   => [
				'aim' => 'bt3/hover-dropdown/{VERSION}/bt3.hover-dropdown.js',
			],
			'shim' => ['jquery', 'bt3'],
			'key'  => 'bt3.hover-dropdown',
		],

		"clipboard" => [
			"js" => [
				'aim' => 'clipboard/{VERSION}/clipboard.min.js',
			],
		],


		"jquery" => [
			"js" => [
				'main' => 'jquery.min.js',
				'aim'  => 'jquery/{VERSION}/jquery.min.js',
			],
		],


		"layer" => [
			"js"   => [
				'main' => 'src/layer.js',
				'aim'  => 'jquery/layer/{VERSION}/jquery.layer.js',
			],
			"css"  => [
				'src/theme/*' => 'jquery/layer/',
			],
			"key"  => 'jquery.layer',
			'shim' => ['jquery'],
		],

		"toastr" => [
			"js"   => [
				'main' => 'toastr.min.js',
				'aim'  => 'jquery/toastr/{VERSION}/jquery.toastr.js',
			],
			"css"  => [
				'toastr.css' => 'jquery/toastr/toastr.css',
			],
			"key"  => 'jquery.toastr',
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


		"smooth-scroll" => [
			"js"  => [
				'main' => 'smooth-scroll.js',
				'aim'  => 'smooth-scroll/{VERSION}/smooth-scroll.js',
			],
			'key' => 'smooth-scroll',
		],

		"vkBeautify" => [
			"js"   => [
				'main' => 'vkbeautify.js',
				'aim'  => 'vkbeautify/vkbeautify.js',
			],
			"key"  => 'vkbeautify',
			"shim" => [
				"exports" => "vkbeautify",
			],
		],

		"PACE" => [
			"js"  => [
				'main' => 'pace.min.js',
				'aim'  => 'pace/{VERSION}/pace.min.js',
			],
			"css" => [
				'themes/*' => 'pace/',
			],
			"key" => 'pace',
		],
	],
	'global'  => [
		'url_site' => env('URL_SITE'),
	],
	'appends' => [
		'lemon' => "/assets/js/lemon",
	],
];