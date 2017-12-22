<?php
return [
	'disk'   => 'resources',
	'folder' => [
		'lib_dir'     => 'assets/js/libs',
		'scss_dir'    => 'assets/sass/libs',
		'config_file' => 'assets/js/config.js',
	],
	'bower'  => [
		"bootstrap" => [
			"folder" => "bt3",
			"shim"   => ["jquery"],
		],
	],
];