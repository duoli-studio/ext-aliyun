<?php
return [
	'title'     => '游戏管理',
	'route'     => 'dsk_game_name',
	'operation' => [
		'index'   => [
			'title' => '游戏管理',
			'icon'  => '<em class="fa fa-gamepad"></em>',
			'menu'  => true,
			'group' => false,
		],
		'create'  => [
			'title' => '创建游戏',
			'menu'  => false,
		],
		'edit'    => [
			'title' => '编辑游戏',
			'menu'  => false,
		],
		'destroy' => [
			'title' => '删除游戏',
			'menu'  => false,
		],
	],
];