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
		$route->any('role/permissions', 'RoleController@permissions');
		$route->any('role/permissions_store', 'RoleController@permissionsStore');
		$route->any('role/lists', 'RoleController@lists');
		$route->any('role/establish', 'RoleController@establish');
		$route->any('role/do', 'RoleController@do');

		// core
		$route->any('/core/cache_clear', 'CoreController@cacheClear');

		// mail
		$route->any('mail/fetch', 'MailController@fetch');
		$route->any('mail/store', 'MailController@store');
		$route->any('mail/test', 'MailController@test');

		// pam
		$route->any('pam/lists', 'PamController@lists');
		$route->any('pam/establish', 'PamController@establish');
		$route->any('pam/do', 'PamController@do');
		$route->any('pam/disable', 'PamController@disable');
		$route->any('pam/password', 'PamController@password');

		// area
		$route->any('area/lists', 'AreaController@lists');
		$route->any('area/establish', 'AreaController@establish');
		$route->any('area/do', 'AreaController@do');
		$route->any('area/fix', 'AreaController@fix')->name('backend:api_v1.area.fix');
		$route->any('area/child', 'AreaController@child')->name('backend:api_v1.area.child');

		// help
		$route->any('help/lists', 'HelpController@lists');
		$route->any('help/establish', 'HelpController@establish');
		$route->any('help/do', 'HelpController@do');

		// category
		$route->any('category/lists', 'CategoryController@lists');
		$route->any('category/establish', 'CategoryController@establish');
		$route->any('category/do', 'CategoryController@do');

		//netease im
		$route->any('im/send','NeteaseImController@systemNotice');
		$route->any('im/set','NeteaseImController@set');
	});
});