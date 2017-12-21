<?php
return [
	'title'     => '游戏来源',
	'route'     => 'dsk_game_source',
	'operation' => [
		'index'   => [
			'title' => '游戏来源',
			'icon'  => '<em class="fa fa-feed"></em>',
			'menu'  => true,
			'group' => false,
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
	],
];