<?php
return [
	'title'     => '发单价格管理',
	'route'     => 'dsk_billing_price',
	'operation' => [
		'index'   => [
			'title' => '发单价格列表',
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