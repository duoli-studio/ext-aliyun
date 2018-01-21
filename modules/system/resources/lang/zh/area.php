<?php

return [
	'graphql' => [
		'input_desc'    => '输入地区',
		'mutation_desc' => '地区创建/修改',
		'action_desc'   => '地区操作',
		'action_type'   => '地区操作类型',
		'query_desc'    => '地区详细查询',
		'queries_desc'  => '地区列表查询',
		'pagination'    => '分页操作',
		'type_desc'     => '地区',
		'list_desc'     => '地区列表项',
	],
	'db'      => [
		'item'      => '参数集合',
		'type'      => '类型',
		'id'        => '地区ID',
		'code'      => '地区编码',
		'title'     => '名称',
		'parent_id' => '父级ID',
	],
	'action'  => [
		'delete' => '删除地区',
	],
	'message' => [
		'same_error'      => '父级元素不能和子集元素相同',
		'exist_error'     => '该类别下存在子元素, 不得删除',
		'undefined_error' => '条目不存在, 不得操作',
	],
];