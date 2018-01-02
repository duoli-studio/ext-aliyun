<?php

return [
	'util'    => [
		'send_captcha_passport_format_error' => '无法发送验证码, 格式不正确',
	],
	'graphql' => [
		'type_desc'     => '验证码',
		'mutation_desc' => '发送验证码',
		'queries_desc'  => '验证码查询',
	],
	'db'      => [
		'id'          => 'ID',
		'passport'    => '手机号或邮箱',
		'captcha'     => '验证码',
		'type'        => '验证类型',
		'num'         => '发送次数',
		'disabled_at' => '失效时间',
	],
];