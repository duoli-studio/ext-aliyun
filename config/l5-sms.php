<?php
return [
	/*
	|--------------------------------------------------------------------------
	| 短信发送接口
	|--------------------------------------------------------------------------
	: ihuyi : 互亿无线   www.ihuyi.com
	: log   : 保存到本地日志, 不是真实发送
	|
	*/
	'api_type' => env('SMS_TYPE', 'ihuyi'),


	'sms' => [

		'log'    => [
			'sign' => '【酸柠檬】',
			'type' => 'log',
		],
		'ihuyi'  => [
			'public_key' => env('SMS_IHUYI_ACCOUNT'),
			'password'   => env('SMS_IHUYI_PASSWORD'),
			'type'       => 'ihuyi',
		],
		'ihuyi2' => [
			'public_key' => env('SMS_IHUYI2_ACCOUNT'),
			'password'   => env('SMS_IHUYI2_PASSWORD'),
			'type'       => 'ihuyi',
		],

		'jianzhou' => [
			'public_key' => 'your-key',
			'password'   => 'your-password',
			'type'       => 'jianzhou',
			'sign'       => '【your-sign】',
		],

	],
];