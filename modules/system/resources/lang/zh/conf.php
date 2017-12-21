<?php

return [
	'graphql' => [
		// conf
		'conf_module' => '模块名称',
		'conf_group'  => '分组名称',

		'group'                => '分组名称',
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