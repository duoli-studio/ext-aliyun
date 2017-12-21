<?php
return [
	'title'     => '服务器列表',
	'route'     => 'dsk_game_server',
	'operation' => [
		'index'   => [
			'title' => '服务器列表',
			'icon'  => '<em class="fa fa-server"></em>',
			'menu'  => true,
		],
		'create'  => [
			'title' => '添加游戏服务器',
			'menu'  => false,
		],
		'edit'    => [
			'title' => '编辑游戏服务器',
			'menu'  => false,
		],
		'destroy' => [
			'title' => '删除游戏服务器',
			'menu'  => false,
		],
		'status'  => [
			'title' => '修改服务器状态',
			'menu'  => false,
		],
		'sort'    => [
			'title' => '排序',
			'menu'  => false,
		],
	],
];