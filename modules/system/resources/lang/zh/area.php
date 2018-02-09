<?php

return [

	'db'       => [
		'type'      => '类型',
		'id'        => '地区ID',
		'code'      => '地区编码',
		'title'     => '名称',
		'parent_id' => '父级ID',
	],
	'input'    => [
		'input_be_area' => [
			'desc' => '输入地区',
		],
	],
	'mutation' => [
		'be_area_2e' => [
			'desc' => '地区创建/修改',
		],

		'be_area_do' => [
			'desc' => '地区操作',
		],
	],
	'queries'  => [
		'be_area'      => [
			'desc' => '地区详细查询',
		],
		'be_area_list' => [
			'desc' => '地区列表查询',
		],
	],
	'types'    => [
		'be_area_do_action' => [
			'desc'        => '地区操作类型',
			'delete_desc' => '删除地区',
		],
		'be_area'           => [
			'desc' => '地区类型',
		],
		'be_area_list'      => [
			'desc' => '地区列表项',
		],
	],
	'message'  => [
		'same_error'      => '父级元素不能和子集元素相同',
		'exist_error'     => '该类别下存在子元素, 不得删除',
		'undefined_error' => '条目不存在, 不得操作',
	],
];