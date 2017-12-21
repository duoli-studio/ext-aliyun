<?php
return [
	'title'     => '系统插件',
	'route'     => 'dsk_lemon_system',
	'operation' => [
		'supervisor' => [
			'title'  => 'Supervisor',
			'icon'   => '<em class="fa fa-plug"></em>',
			'menu'   => true,
			'url'    => env('LM_SUPERVISOR_URL', env('URL_SITE') . ':9001'),
			'is_url' => true,
		],
	],
];