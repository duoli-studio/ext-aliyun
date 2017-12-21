<?php
return [
	'title'     => '博文',
	'route'     => 'web:blog',
	'operation' => [
		'index'   => [
			'title' => '博文列表',
			'menu'  => false,
		],
		'create'  => [
			'title' => '创建',
			'menu'  => true,
		],
		'edit'    => [
			'title' => '编辑',
			'menu'  => false,
		],
		'trash'   => [
			'title' => '垃圾桶',
			'menu'  => true,
		],
		'destroy' => [
			'title' => '删除',
			'menu'  => true,
		],
		'show' => [
			'title' => '博文',
			'menu'  => true,
		],
	]
];