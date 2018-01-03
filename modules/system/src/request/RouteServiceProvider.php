<?php namespace System\Request;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Poppy\Framework\GraphQL\GraphQLController;
use System\Request\Api\AuthController;
use System\Request\Api\DashboardsController;
use System\Request\Api\HomeController as SysApiHomeController;
use System\Request\Backend\BeHomeController;
use System\Request\Develop\DevController;
use System\Request\System\HomeController;
use System\Request\System\TestController as SystemTestController;

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
		], function (Router $router) {
			$router->get('/', HomeController::class . '@layout');

			$router->get('/login', HomeController::class . '@login');
			$router->get('/test', SystemTestController::class . '@test');
		});
		\Route::group([
			'middleware' => 'backend',
			'prefix'     => 'backend',
		], function (Router $router) {
			$router->get('/login', BeHomeController::class . '@login');
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
		], function (Router $router) {
			$router->any('login', DevController::class . '@login')
				->name('system:develop.login');

			$router->get('api', DevController::class . '@api')
				->name('system:develop.api');

			$router->group([
				'middleware' => 'auth:develop',
			], function (Router $router) {

				$router->get('/graphi/{schema?}', HomeController::class . '@graphi')
					->name('system:develop.graphql');

				$router->get('/', DevController::class . '@cp')
					->name('system:develop.cp');

				$router->get('/phpinfo', DevController::class . '@phpinfo')
					->name('system:develop.phpinfo');
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
		$this->graphqlApi();

		\Route::group([
			'middleware' => ['cross'],
			'prefix'     => 'api/system',
		], function (Router $route) {
			$route->any('/information', SysApiHomeController::class . '@information');
			$route->any('/dashboard', DashboardsController::class . '@list');
			$route->group([
				'middleware' => ['auth:jwt_backend'],
			], function (Router $route) {
				$route->any('/access', AuthController::class . '@access')
					->name('system:api.access');

				// page
				$route->any('/page/{path?}', SysApiHomeController::class . '@page');

			});
		});
	}

	private function graphqlApi()
	{
		\Route::group([
			'middleware' => ['cross'],
		], function (Router $route) {
			$route->any('api/token/{guard?}', AuthController::class . '@token')
				->name('api.token');
			$route->any('api/g/{graphql_schema?}', GraphQLController::class . '@query')
				->name('api.graphql');
		});

	}
}
