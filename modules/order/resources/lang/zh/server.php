<?php

return [
	'graphql' => [
		'query_desc'    => '游戏区服详细',
		'queries_desc'  => '游戏区服查询',
		'mutation_desc' => '游戏区服修改/创建',
		'type_desc'     => '游戏区服',
		'delete'        => '删除游戏区服',
		'is_enable'     => '是否启用',
		'is_default'    => '是否默认',
	],
	'db'      => [
		'id'            => 'ID',
		'title'         => '区服名称',
		'parent_id'     => '上一级的ID',
		'code'          => '区服代码',
		'top_parent_id' => '顶层ID, 父元素',
		'children'      => '所有的子元素',
		'is_enable'     => '是否启用',
		'is_default'    => '是否默认',
	],
];