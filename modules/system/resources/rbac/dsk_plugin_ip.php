<?php
return [
	'title'     => 'Ip控制',
	'route'     => 'dsk_plugin_ip',
	'operation' => [
		'index'   => [
			'title' => 'IP列表',
			'icon'  => '<em class="fa fa-internet-explorer"></em>',
			'menu'  => true,
			'group' => false,
		],
		'create'  => [
			'title' => '增加IP',
			'menu'  => false,
		],
		'edit'    => [
			'title' => '编辑',
			'menu'  => false,
		],
		'destroy' => [
			'title' => '删除',
			'menu'  => false,
		],
	],
];