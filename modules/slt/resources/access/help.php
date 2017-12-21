<?php
return [
	'title'     => '帮助中心',
	'route'     => 'web:help',
	'operation' => [
		'feedback' => [
			'title' => '意见反馈',
			'menu'  => true,
		],
		'show'     => [
			'title' => '文章页面',
			'menu'  => false,
		],
		'index'    => [
			'title' => '文章列表',
			'menu'  => true,
			'param'  => 'cat_id=2',
		],

	]
];