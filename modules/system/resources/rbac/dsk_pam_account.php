<?php
return [
	'title'     => '账号管理',
	'route'     => 'dsk_pam_account',
	'operation' => [
		'index'   => [
			'title' => '用户列表',
			'icon'  => '<em class="fa fa-user"></em>',
			'menu'  => true,
			'group' => true,
		],
		'log'     => [
			'title' => '登录日志',
			'icon'  => '<em class="fa fa-calendar"></em>',
			'menu'  => true,
			'group' => true,
		],
		'status'  => [
			'title' => '启用/禁用账号',
			'menu'  => false,
		],
		'create'  => [
			'title' => '创建账号',
			'menu'  => false,
		],
		'edit'    => [
			'title' => '更改用户资料',
			'menu'  => false,
		],
		'destroy' => [
			'title' => '删除用户',
			'menu'  => false,
		],
		'acl'     => [
			'title' => '设置权限',
			'menu'  => false,
		],
	],
];