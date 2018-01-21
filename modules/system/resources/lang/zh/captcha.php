<?php

return [
	'graphql' => [
		// 发送验证码类型
		'captcha_action_type'     => '发送验证码类型',
		'captcha_action_login'    => '登录/注册发送验证码',
		'captcha_action_password' => '找回密码发送验证码',
		'captcha_action_user'     => '给自己发送验证码',
		'captcha_action_rebind'   => '绑定新手机发送验证码',

		'captcha_query'            => '发送验证码',
		'captcha_query_passport'   => '发送验证码的通行证, 支持手机/邮箱(暂未发送)',
		'captcha_query_image_code' => '图片验证码',

		'validate_captcha_query'    => '验证验证码正确与否, 并返回验证串',
		'validate_captcha_passport' => '验证码支持的通行证',
		'validate_captcha_captcha'  => '获取验证码',

		'user_captcha_query'          => '用户发送验证码',
		'user_captcha_query_passport' => '用户发送验证码的类型, 支持(mobile)手机/(email)邮箱(暂不支持)',

		'send_captcha_success' => '成功发送验证码',

	],
	'db'      => [
		'passport' => '通行证',
		'captcha'  => '图像验证码',
	],
	'action'  => [
		'send_passport_format_error' => '无法发送验证码, 格式不正确',
		'account_miss'               => '指定账号不存在, 无法发送',
		'account_exists'             => '指定手机号已经存在, 不对绑定, 请更换',
	],
];