<?php

return [
	'graphql' => [
		// input type
		'input_role_type'                    => '需要保存/修改的用户角色',
		'input_role_filter_type'             => '角色筛选过滤器',

		// mutation
		'mutation_permission'                => '权限保存',
		'mutation_permission_arg_permission' => '需要保存的权限ID列表',

		// query
		'queries_desc'                       => '角色查询',
		'query_desc'                         => '角色详情',
		'type_desc'                          => '角色类型',
		'mutation_desc'                      => '角色修改/创建',
		'do_desc'                            => '角色操作',
		'do_action'                          => '角色操作项目',
		'input_role_desc'                    => '角色输入项目',
		'can_permission'                     => '可以设置权限',
		'input_permission_desc'              => '保存的权限条目',
		'mutation_arg_permission'            => '权限列表',
		'filter_desc'                        => '查询过滤器',

		// types
		'list'                               => '列表项',
		'pagination'                         => '分页',
	],
	'db'      => [
		'id'            => 'ID',
		'name'          => '角色标识',
		'title'         => '角色名称',
		'type'          => '角色类型',
		'description'   => '角色描述',
		'role_id'       => '角色ID',
		'permission_id' => '权限ID',
	],
	'action'  => [
		'permissions'                  => '权限ID',
		'permission_error'             => '权限错误',
		'no_policy_to_delete'          => '无权删除此角色',
		'no_policy_to_create'          => '无权创建角色',
		'no_policy_to_update'          => '无权更改角色',
		'no_policy_to_save_permission' => '无权保存权限',
		'role_not_exists'              => '角色不存在',
		'role_has_account'             => '当前角色下存在用户, 请先清除用户的这类角色信息, 再行删除',
	],
];