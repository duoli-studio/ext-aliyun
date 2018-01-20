<?php

return [
	'graphql' => [
		'mutation_desc' => '地区管理',
		'action_desc'   => '地区操作',
	],
	'db'      => [
		'id'       => 'ID',
		'data'     => '值{"code":"","province":"","city":"","district":"","parent":""}',
		'code'     => '编码',
		'province' => '省份',
		'city'     => '城市',
		'district' => '区',
		'parent'   => '父级',
	],
	'action'  => [
		'create'  => '创建地区($data)',
		'edit'    => '修改地区($id, $data)',
		'destroy' => '删除地区($id)',
	],
];