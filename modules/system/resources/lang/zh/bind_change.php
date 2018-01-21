<?php

return [
	'graphql' => [
		'mutation_desc' => '手机换绑',
		'action_desc'   => '换绑操作',
	],
	'db'      => [
		'account_id'  => 'ID',
		'mobile'      => '手机号',
		'passport'    => '通行证',
		'captcha'     => '验证码',
		'verify_code' => '生成验证串码',
	],
	'action'  => [
		'old_send'     => '原手机号发送验证码(mobile)',
		'old_validate' => '原手机号验证(captcha)',
		'new_send'     => '新手机号发送验证码(verify_code,mobile)',
		'new_validate' => '新手机号验证(mobile,captcha)',
	],
];