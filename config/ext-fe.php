<?php
return [
	'disk'   => 'resources',
	'folder' => [
		'js_dir'   => 'assets/js',
		'scss_dir' => 'assets/sass',
	],
	'bower'  => [
		"bootstrap" => [
			"folder" => "bt3",
			"shim"   => ["jquery"],
		],
	],
	'global' => [
		'url_site' => env('URL_SITE'),
	],
];