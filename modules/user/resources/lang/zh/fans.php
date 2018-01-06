<?php

return [
	'graphql' => [
		'queries_desc'  => '查询关注列表',
		'mutation_desc' => '关注',
		'delete_desc'   => '取消关注',
		'handle'        => [
			'type'      => '粉丝操作',
			'attention' => '关注',
			'cancel'    => '取消关注',
		],
	],

	'db' => [
		'account_id'  => '被关注者ID',
		'fans_id'     => '关注者ID',
		'concern_num' => '关注人数',
	],
];