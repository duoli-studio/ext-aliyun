<?php namespace System\Request;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Poppy\Framework\GraphQL\GraphQLController;
use System\Request\Backend\HomeController as BackendHomeController;
use System\Request\Backend\PamController as BackendPamController;
use System\Request\Backend\RoleController as BackendRoleController;
use System\Request\Develop\CpController as DevCpController;
use System\Request\Develop\EnvController as DevEnvController;
use System\Request\Develop\PamController as DevPamController;
use System\Request\Develop\ToolController as DevToolController;
use System\Request\System\HomeController;

class RouteServiceProvider extends ServiceProvider
{


	/**
	 * Define the routes for the module.
	 * @return void
	 */
	public function map()
	{
		$this->mapWebRoutes();

		$this->mapDevRoutes();

		$this->mapApiRoutes();
	}

	/**
	 * Define the "web" routes for the module.
	 * These routes all receive session state, CSRF protection, etc.
	 * @return void
	 */
	protected function mapWebRoutes()
	{
		\Route::group([
			'prefix' => 'system',
		], function(Router $router) {
			$router->get('/', HomeController::class . '@layout');
			$router->get('/login', HomeController::class . '@login');
		});

		// backend web
		\Route::group([
			'middleware' => 'backend',
			'prefix'     => 'backend',
		], function(Router $router) {
			$router->any('/', BackendHomeController::class . '@login')->name('backend:home.login');
			$router->any('/test', BackendHomeController::class . '@test')->name('backend:home.test');
			$router->group([
				'middleware' => ['auth:backend', 'disabled_pam'],
			], function(Router $router) {
				$router->any('/cp', config('poppy.backend_cp') ?: BackendHomeController::class . '@cp')
					->name('backend:home.cp');
				$router->any('/password', BackendHomeController::class . '@password')
					->name('backend:home.password');
				$router->any('/logout', BackendHomeController::class . '@logout')
					->name('backend:home.logout');
				$router->any('/setting/{path?}', BackendHomeController::class . '@setting')
					->name('backend:home.setting');

				$router->get('/role', BackendRoleController::class . '@index')
					->name('backend:role.index');
				$router->any('/role/establish/{id?}', BackendRoleController::class . '@establish')
					->name('backend:role.establish');
				$router->any('/role/delete/{id?}', BackendRoleController::class . '@delete')
					->name('backend:role.delete');
				$router->any('/role/menu/{id}', BackendRoleController::class . '@menu')
					->name('backend:role.menu');

				$router->get('/pam', BackendPamController::class . '@index')
					->name('backend:pam.index');
				$router->any('/pam/establish', BackendPamController::class . '@establish')
					->name('backend:pam.establish');
				$router->any('/pam/password/{id}', BackendPamController::class . '@password')
					->name('backend:pam.password');
			});
		});
	}

	/**
	 * Define the "web" routes for the module.
	 * These routes all receive session state, CSRF protection, etc.
	 * @return void
	 */
	protected function mapDevRoutes()
	{
		// develop
		\Route::group([
			'middleware' => 'web',
			'prefix'     => 'develop',
		], function(Router $router) {
			$router->any('login', DevPamController::class . '@login')
				->name('system:develop.pam.login');

			$router->group([
				'middleware' => ['web', 'auth:develop', 'disabled_pam'],
			], function(Router $router) {
				$router->get('/', DevCpController::class . '@index')
					->name('system:develop.cp.cp');

				$router->get('api', DevCpController::class . '@api')
					->name('system:develop.cp.api');
				$router->any('set_token', DevCpController::class . '@setToken')
					->name('system:develop.cp.set_token');
				$router->any('api_login', DevCpController::class . '@apiLogin')
					->name('system:develop.cp.api_login');
				$router->get('doc/{type?}', DevCpController::class . '@doc')
					->name('system:develop.cp.doc');
				$router->get('/graphi/{schema?}', DevCpController::class . '@graphi')
					->name('system:develop.cp.graphql');
				$router->get('/phpinfo', DevEnvController::class . '@phpinfo')
					->name('system:develop.env.phpinfo');
				$router->get('/check', DevEnvController::class . '@check')
					->name('system:develop.env.check');
				$router->any('/tool/graphql-reverse', DevToolController::class . '@graphqlReverse')
					->name('system:develop.tool.graphql_reverse');
				$router->any('/tool/html-entity', DevToolController::class . '@htmlEntity')
					->name('system:develop.tool.html_entity');

				if (app('extension')->has('poppy/ext-fe')) {
					if (class_exists('Poppy\Extension\Fe\Http\LogController')) {
						$router->any('/log', 'Poppy\Extension\Fe\Http\LogController@index')
							->name('system:develop.log.index');
						$router->any('/api_doc/{type?}', 'Poppy\Extension\Fe\Http\ApiDocController@auto')
							->name('system:develop.doc.index');
					}
				}
			});
		});
	}

	/**
	 * Define the "api" routes for the module.
	 * These routes are typically stateless.
	 * @return void
	 */
	protected function mapApiRoutes()
	{

		// Api V1 版本
		\Route::group([
			'middleware' => ['cross'],
			'prefix'     => 'api_v1',
		], function() {
			require_once poppy_path('system', 'src/request/routes/web_v1.php');
			require_once poppy_path('system', 'src/request/routes/backend_v1.php');
		});


		$this->graphqlApi();
	}


	private function graphqlApi()
	{
		\Route::group([
			'middleware' => ['cross'],
		], function(Router $route) {
			$route->any('api/g/{graphql_schema?}', GraphQLController::class . '@query')
				->name('api.graphql');
		});

	}
}
