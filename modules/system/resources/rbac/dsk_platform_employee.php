<?php
return [
	'title'     => '员工专属',
	'route'     => 'dsk_platform_employee',
	'operation' => [
		'index'              => [
			'title' => '接单详情',
			'icon'  => '<em class="fa fa-sellsy"></em>',
			'menu'  => true,
			//'group' => false,
		],
		'money'              => [
			'title' => '资金管理',
			'icon'  => '<em class="fa fa-soundcloud"></em>',
			'menu'  => true,
			//'group' => false,
		],
		'delete'             => [
			'title' => '删除订单',
		],
		'handle'             => [
			'title' => '接手订单',
		],
		'over'               => [
			'title' => '完成订单',
		],
		'progress'           => [
			'title' => '更新进度',
		],
		'exception'          => [
			'title' => '提交异常',
		],
		'cancel_exception'   => [
			'title' => '取消异常',
		],
		'confirm_order_over' => [
			'title' => '确认完单',
		],

	],
];