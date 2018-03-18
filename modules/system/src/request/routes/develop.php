<?php

use Illuminate\Routing\Router;

\Route::group([
	'namespace' => 'System\Request\Develop',
], function (Router $router) {
	$router->any('login', 'PamController@login')
		->name('system:develop.pam.login');

	$router->group([
		'middleware' => ['web', 'auth:develop', 'disabled_pam'],
	], function (Router $router) {
		$router->get('/', 'CpController@index')
			->name('system:develop.cp.cp');

		$router->get('api', 'CpController@api')
			->name('system:develop.cp.api');
		$router->any('set_token', 'CpController@setToken')
			->name('system:develop.cp.set_token');
		$router->any('api_login', 'CpController@apiLogin')
			->name('system:develop.cp.api_login');
		$router->get('doc/{type?}', 'CpController@doc')
			->name('system:develop.cp.doc');
		$router->get('/graphi/{schema?}', 'CpController@graphi')
			->name('system:develop.cp.graphql');

		// 环境检测
		$router->get('env/phpinfo', 'EnvController@phpinfo')
			->name('system:develop.env.phpinfo');
		$router->get('env/check', 'EnvController@check')
			->name('system:develop.env.check');
		$router->get('env/db', 'EnvController@db')
			->name('system:develop.env.db');

		// 工具
		$router->any('/tool/graphql-reverse', 'ToolController@graphqlReverse')
			->name('system:develop.tool.graphql_reverse');
		$router->any('/tool/html-entity', 'ToolController@htmlEntity')
			->name('system:develop.tool.html_entity');

		// 扩展
		$router->any('/log', 'LogController@index')
			->name('system:develop.log.index');
		$router->any('/api_doc/{type?}', 'ApiDocController@auto')
			->name('system:develop.doc.index');
	});
});