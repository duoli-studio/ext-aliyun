<?php

use Illuminate\Routing\Router;

\Route::group([
	'namespace' => 'System\Request\Backend',
], function (Router $router) {
	$router->any('/', 'HomeController@login')->name('backend:home.login');
	$router->any('/test', 'HomeController@test')->name('backend:home.test');
	$router->group([
		'middleware' => ['auth:backend', 'disabled_pam'],
	], function (Router $router) {
		$router->any('/cp', config('poppy.backend_cp') ?: 'HomeController@cp')
			->name('backend:home.cp');
		$router->any('/password', 'HomeController@password')
			->name('backend:home.password');
		$router->any('/logout', 'HomeController@logout')
			->name('backend:home.logout');
		$router->any('/setting/{path?}', 'HomeController@setting')
			->name('backend:home.setting');

		$router->get('/role', 'RoleController@index')
			->name('backend:role.index');
		$router->any('/role/establish/{id?}', 'RoleController@establish')
			->name('backend:role.establish');
		$router->any('/role/delete/{id?}', 'RoleController@delete')
			->name('backend:role.delete');
		$router->any('/role/menu/{id}', 'RoleController@menu')
			->name('backend:role.menu');

		$router->get('/pam', 'PamController@index')
			->name('backend:pam.index');
		$router->any('/pam/establish', 'PamController@establish')
			->name('backend:pam.establish');
		$router->any('/pam/password/{id}', 'PamController@password')
			->name('backend:pam.password');
	});
});

