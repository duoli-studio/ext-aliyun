<?php
return [
	'title'     => '用户操作',
	'route'     => 'web.user',
	'operation' => [
		'socialite_bind'    => [
			'title' => '绑定账号',
			'menu'  => false,
		],
		'login'             => [
			'title' => '用户登陆',
			'menu'  => false,
		],
		'register'          => [
			'title' => '用户注册',
			'menu'  => false,
		],
		'basic'             => [
			'title' => '基本资料',
			'menu'  => true,
		],
		'avatar'            => [
			'title' => '头像管理',
			'menu'  => false,
		],
		'password'          => [
			'title' => '修改密码',
			'menu'  => true,
		],
		'question'          => [
			'title' => '密保问题',
			'menu'  => false,
		],
		'payword'           => [
			'title' => '修改支付密码',
			'menu'  => false,
		],
		'validate_truename' => [
			'title' => '实名认证',
			'menu'  => false,
		],
		'bind_mobile'       => [
			'title' => '手机绑定',
			'menu'  => false,
		],
		'send_captcha'      => [
			'title' => '发送验证码',
			'menu'  => false,
		],
		'login_log'         => [
			'title' => '登陆日志',
			'menu'  => false,
		],
		'logout'            => [
			'title' => '退出登陆',
			'menu'  => false,
		],
	]
];