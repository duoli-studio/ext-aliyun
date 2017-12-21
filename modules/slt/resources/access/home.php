<?php
return [
	'title'     => '主面板',
	'route'     => 'web.home',
	'operation' => [
		'cp'      => [
			'title' => '主控制台',
			'menu'  => true,
		],
		'env'     => [
			'title' => '环境检测',
			'menu'  => false,
		],
		'welcome' => [
			'title' => '欢迎界面',
			'menu'  => false,
		],
	]
];