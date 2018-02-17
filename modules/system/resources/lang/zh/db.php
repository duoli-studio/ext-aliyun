<?php

return [

	'area'     => [
		'type'      => '类型',
		'id'        => '地区ID',
		'code'      => '地区编码',
		'title'     => '名称',
		'parent_id' => '父级ID',
	],
	'captcha'  => [
		'passport' => '通行证',
		'captcha'  => '图像验证码',
	],
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
];