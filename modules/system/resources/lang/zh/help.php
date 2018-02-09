<?php

return [
	'action'   => [
		'help'     => [
			'item_not_exist'       => '条目不存在, 不得操作',
			'parent_error'         => '分类级别错误',
			'parent_id_not_exists' => '分类级别不存在',
		],
		'category' => [
			'item_not_exist' => '条目不存在, 不得操作',
			'delete'         => '删除',
		],
	],
	'db'       => [
		'help'     => [
			'id'      => 'ID',
			'cat_id'  => '帮助分类ID',
			'title'   => '帮助标题',
			'content' => '帮助内容',
		],
		'category' => [
			'id'        => 'ID',
			'type'      => '分类类型',
			'parent_id' => '父级ID',
			'title'     => '分类标题',
		],

	],
	'input'    => [
		'input_help'     => [
			'desc' => '帮助类型',
		],
		'input_category' => [
			'desc' => '分类类型',
		],
	],
	'mutation' => [
		'be_category_2e' => [
			'desc'     => '分类的修改/创建',
			'arg_item' => '参数集合',
		],
		'be_category_do' => [
			'desc'       => '分类操作',
			'arg_action' => '分类操作类型',
		],
		'be_help_2e'     => [
			'desc'     => '帮助的修改/创建',
			'arg_item' => '参数集合',
		],
		'be_help_do'     => [
			'desc'       => '帮助操作',
			'arg_action' => '分类操作类型',
		],

	],
	'queries'  => [
		'be_category_list' => [
			'desc' => '分类列表查询',
		],
		'be_category'      => [
			'desc' => '分类详细查询',
		],
		'be_help_list'     => [
			'desc' => '帮助列表查询',
		],
		'be_help'          => [
			'desc' => '帮助详细查询',
		],
	],
	'types'    => [

		'be_category_do_action' => [
			'desc'       => '分类类型',
			'arg_delete' => '删除',
		],
		'be_category_list'      => [
			'desc'     => '分类类型',
			'arg_list' => '列表项',
		],
		'be_category'           => [
			'desc'     => '分类类型',
			'arg_list' => '列表项',
		],
		'be_help_do_action'     => [
			'desc'       => '帮助类型',
			'arg_delete' => '删除',
		],
		'be_help_list'          => [
			'desc'     => '帮助类型',
			'arg_list' => '列表项',
		],
		'be_help'               => [
			'desc' => '帮助类型',
		],
	],
];