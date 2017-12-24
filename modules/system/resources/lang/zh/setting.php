<?php

return [
	'graphql' => [
		'query_desc'    => '配置查询',
		'queries_desc'  => '多条配置查询',
		'mutation_desc' => '配置项修改',
		'type_desc'     => '配置项',
		'namespace'     => '配置项命名空间',
		'group'         => '配置项分组名称',
		'key'           => '配置项键',
		'value'         => '配置项值',
		'description'   => '配置项描述',

		// conf
		'conf_module'   => '模块名称',
		'conf_group'    => '分组名称',

		'children'             => '分组设置项目',

		// children
		'children_namespace'   => '设置命名空间',
		'children_group'       => '设置分组',
		'children_item'        => '设置键',
		'children_value'       => '设置值',
		'children_description' => '设置描述',

		// item
		'item'                 => '设置内容条目项目',
		'item_key'             => '设置键(遵循服务端解析 namespace::group.item)',
		'item_value'           => '设置值',

		// item
		'resp_status'          => '响应代码',
		'resp_message'         => '响应信息',
		'resp_errors'          => '相应错误信息',
	],
	'resp'    => [
		'key_not_match' => '给定的键 :key 格式不匹配',
	],
];