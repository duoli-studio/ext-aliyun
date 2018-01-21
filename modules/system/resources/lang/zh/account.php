<?php

return [
	'graphql' => [
		// input type
		'input_pam_filter' => '账号筛选过滤器',

		// query
		'query_desc'       => '用户账号详情',
		'queries_desc'     => '用户账号列表查询',
		'filter_desc'      => '查询过滤器',

		'mutation_desc' => '用户账号操作',
		// types
		'list_type'     => '类型',
		'list_desc'     => '列表项',
		'pagination'    => '分页',
		'action_desc'   => '操作类型',
	],
	'db'      => [
		'id'             => '账号ID',
		'username'       => '用户名',
		'mobile'         => '手机号',
		'email'          => 'Email',
		'password'       => '密码',
		'type'           => '用户类型',
		'is_enable'      => '是否禁用',
		'disable_reason' => '禁用原因',
	],
	'action'  => [
		'enable'         => '账号启用',
		'disable'        => '账号禁用',
		'is_enable'      => '是否禁用',
		'disable_data'   => '禁用项数据{"disable_reason":"","disableMin":""}',
		'disable_reason' => '禁用原因',
		'disable_to'     => '禁用时间',
	],
];