<?php

return [
	'action'   => [
		'role'        => [
			'permissions'                  => '权限ID',
			'permission_error'             => '权限错误',
			'no_policy_to_delete'          => '无权删除此角色',
			'no_policy_to_create'          => '无权创建角色',
			'no_policy_to_update'          => '无权更改角色',
			'no_policy_to_save_permission' => '无权保存权限',
			'role_not_exists'              => '角色不存在',
			'role_has_account'             => '当前角色下存在用户, 请先清除用户的这类角色信息, 再行删除',
		],
		'pam'         => [
			'not_set_name_prefix'       => '尚未设置用户名默认前缀, 无法注册, 请联系管理员',
			'account_disable_not_login' => '本账户被禁用, 不得登入',
			'login_fail_again'          => '您输入的账号或密码有误！',
			'user_name_not_space'       => '用户名中不得包含空格',
			'role_not_exists'           => '给定的用户角色不存在',
			'mobile_already_registered' => '该手机号已经注册过',
			'account_disabled'          => '当前用户已禁用',
			'account_enabled'           => '当前用户为启用状态',
			'disable_reason'            => '禁用原因',
			'disable_to'                => '禁用时间',
		],
		'bind_change' => [
			'mobile_not_account'        => '该手机号不是本账号绑定手机',
			'mobile_already_registered' => '该手机号已经注册过',
		],
	],
	'db'       => [
		'role'        => [
			'id'            => 'ID',
			'name'          => '角色用户名',
			'title'         => '角色名称',
			'type'          => '角色类型',
			'description'   => '角色描述',
			'role_id'       => '角色ID',
			'permission_id' => '权限ID',
			'field'         => '关键词字段',
			'kw'            => '关键词',
		],
		'bind_change' => [
			'account_id'  => 'ID',
			'mobile'      => '手机号',
			'passport'    => '通行证',
			'captcha'     => '验证码',
			'verify_code' => '生成验证串码',
		],
		'account'     => [
			'id'             => '账号ID',
			'passport'       => '通行证',
			'username'       => '用户名',
			'mobile'         => '手机号',
			'email'          => 'Email',
			'password'       => '密码',
			'type'           => '用户类型',
			'is_enable'      => '是否禁用',
			'disable_reason' => '禁用原因',
			'field'          => '关键词字段',
			'kw'             => '关键词',
		],
	],
	'input'    => [
		'be_pam_filter'  => [
			'desc' => '账号筛选过滤器',
		],
		'be_role_filter' => [
			'desc' => '角色筛选过滤器',
		],
		'be_role'        => [
			'desc' => '角色输入项目',
		],
	],
	'mutation' => [
		'be_pam_disable'     => [
			'desc'       => '账号禁用',
			'arg_date'   => '禁用日期',
			'arg_reason' => '禁用原因',
		],
		'be_role_2e'         => [
			'desc' => '角色修改/创建',
		],
		'be_role_do'         => [
			'desc' => '角色操作',
		],
		'be_pam_do'          => [
			'desc'       => '用户账号操作',
			'arg_action' => '操作类型',
		],
		'be_role_permission' => [
			'desc'           => '权限保存',
			'arg_permission' => '操作类型',
		],
		'rebind_passport'    => [
			'desc' => '手机换绑',
		],
		'be_pam_create'      => [
			'desc' => '账户创建',
		],
		'be_pam_edit_pwd'    => [
			'desc' => '账户修改密码',
		],
	],
	'queries'  => [
		'be_pam_list'  => [
			'desc'        => '用户账号列表查询',
			'arg_filters' => '查询过滤器',
		],
		'be_pam'       => [
			'desc' => '用户账号详情',
		],
		'be_role_list' => [
			'desc'        => '角色查询',
			'arg_filters' => '查询过滤器',
		],
		'be_role'      => [
			'desc' => '角色详情',
		],
	],
	'types'    => [
		'be_pam_do_action'  => [
			'desc'         => '操作类型',
			'value_enable' => '账号启用',
		],
		'be_pam_list'       => [
			'desc'       => '类型',
			'filed_list' => '列表项',
		],
		'be_pam'            => [
			'desc'              => '类型',
			'field_can_enable'  => '是否可以启用',
			'field_can_disable' => '是否可以禁用',
		],
		'be_role_do_action' => [
			'type_desc'    => '角色类型',
			'value_delete' => '删除角色',
		],
		'be_role_guard'     => [
			'type_desc' => '角色类型',
		],
		//PamAccount
		'guard_type'        => [
			'type_web'     => '用户角色',
			'type_backend' => '后台角色',
			'type_develop' => '开发者角色',
		],
		//PamRole
		'role_guard_type'   => [
			'type_user'    => '前台用户角色',
			'type_root'    => '后台管理员角色',
			'type_develop' => '开发者角色',
		],
		'be_role_list'      => [
			'type_desc'  => '角色类型',
			'filed_list' => '列表项',
		],
		'be_role'           => [
			'type_desc'            => '角色类型',
			'filed_can_permission' => '可以设置权限',
			'filed_can_delete'     => '可以删除',
		],
		'be_pam_register'   => [
			'type_desc' => '用户注册类型',
		],
		'pam_account_type'  => [
			'desc'          => '账号类型',
			'value_user'    => '用户',
			'value_backend' => '后台',
			'value_develop' => '开发者',
		],
		'register_type'     => [
			'type_user'    => '用户类型',
			'type_backend' => '后台管理类型',
			'type_develop' => '开发者类型',
		],
	],
];