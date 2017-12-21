<?php
return [
	'title'     => '游戏类型',
	'route'     => 'dsk_game_type',
	'operation' => [
		'index'   => [
			'title' => '游戏类型',
			'icon'  => '<em class="fa fa-bar-chart"></em>',
			'menu'  => true,
		],
		'create'  => [
			'title' => '添加游戏类型',
			'menu'  => false,
		],
		'edit'    => [
			'title' => '编辑游戏类型',
			'menu'  => false,
		],
		'destroy' => [
			'title' => '删除游戏类型',
			'menu'  => false,
		],
		'sort'    => [
			'title' => '排序',
			'menu'  => false,
		],
	],
];