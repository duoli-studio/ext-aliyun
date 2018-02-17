<?php

\Route::group([
	'middleware' => ['cross'],
	'prefix'     => 'backend/system',
	'namespace'  => 'System\Request\ApiV1\Backend',
], function(Illuminate\Routing\Router $route) {

	$route->group([
		'middleware' => ['auth:jwt_backend'],
	], function(Illuminate\Routing\Router $route) {
		// layout
		$route->any('/layout/information', 'LayoutController@information');
		$route->any('/layout/dashboards', 'LayoutController@dashboards');
		$route->any('/layout/page/{path?}', 'LayoutController@page');
		$route->any('/layout/menus', 'LayoutController@menus');

		// setting
		$route->any('/setting/fetch', 'SettingController@fetch');
		$route->any('/setting/establish', 'SettingController@establish');

		// role
		$route->post('role/permissions', 'RoleController@permissions');
		$route->post('role/permissions_store', 'RoleController@permissionsStore');
		$route->post('role/lists', 'RoleController@lists');
		$route->post('role/establish', 'RoleController@establish');
		$route->post('role/do', 'RoleController@do');

		// core
		$route->post('/core/cache_clear', 'CoreController@cacheClear');

		// mail
		$route->post('mail/fetch', 'MailController@fetch');
		$route->post('mail/store', 'MailController@store');
		$route->post('mail/test', 'MailController@test');

		// pam
		$route->post('pam/lists', 'PamController@lists');
		$route->post('pam/establish', 'PamController@establish');
		$route->post('pam/do', 'PamController@do');
		$route->post('pam/disable', 'PamController@disable');
		$route->post('pam/password', 'PamController@password');

		// area
		$route->post('area/lists', 'AreaController@lists');
		$route->post('area/establish', 'AreaController@establish');
		$route->post('area/do', 'AreaController@do');

		// help
		$route->post('help/lists', 'HelpController@lists');
		$route->post('help/establish', 'HelpController@establish');
		$route->post('help/do', 'HelpController@do');

		// category
		$route->post('category/lists', 'CategoryController@lists');
		$route->post('category/establish', 'CategoryController@establish');
		$route->post('category/do', 'CategoryController@do');

		//netease im
		$route->post('im/send_notice','NeteaseImController@systemNotice');
	});
});