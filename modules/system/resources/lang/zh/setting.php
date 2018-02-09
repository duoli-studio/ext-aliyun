<?php

return [
	'db'         => [
		'config' => [
			'namespace' => '命名空间',
			'group'     => '配置项分组名称',
		],
	],
	'mutation'   => [
		'be_setting' => [
			'desc'      => '后台配置',
			'arg_key'   => '配置项键',
			'arg_value' => '配置项值',
		],
	],
	'query'      => [
		'be_setting_list' => [
			'desc'          => '设置列表',
			'arg_namespace' => '配置项命名空间',
			'arg_group'     => '配置项分组名称',
		],
	],
	'type'       => [
		'be_setting' => [
			'desc'              => '设置类型',
			'field_key'         => '配置项键',
			'field_value'       => '配置项值',
			'field_description' => '配置项描述',
		],
		'resp'       => [
			'desc'          => '设置类型',
			'field_status'  => '状态值',
			'field_message' => '信息',
			'field_data'    => '附加数据',
		],
	],
	'repository' => [
		'setting' => [
			'key_not_match' => '给定的键 :key 格式不匹配',
		],
	],
];