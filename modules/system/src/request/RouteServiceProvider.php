<?php namespace System\Request;

/**
 * Copyright (C) Update For IDE
 */
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Poppy\Framework\GraphQL\GraphQLController;
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
		], function (Router $router) {
			$router->get('/', HomeController::class . '@layout');
			$router->get('/login', HomeController::class . '@login');
		});

		// backend web
		\Route::group([
			'middleware' => 'backend',
			'prefix'     => 'backend',
		], function () {
			require_once poppy_path('system', 'src/request/routes/backend.php');
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
		], function () {
			require_once poppy_path('system', 'src/request/routes/develop.php');
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
		], function () {
			require_once poppy_path('system', 'src/request/routes/web_v1.php');
			require_once poppy_path('system', 'src/request/routes/backend_v1.php');
		});

		$this->graphqlApi();
	}

	private function graphqlApi()
	{
		\Route::group([
			'middleware' => ['cross'],
		], function (Router $route) {
			$route->any('api/g/{graphql_schema?}', GraphQLController::class . '@query')
				->name('api.graphql');
		});
		\Route::group([
			'middleware' => ['cross'],
		], function (Router $router) {
			$router->any('api/j/{url}', HomeController::class . '@json')
				->where('url', '[a-zA-z/]+');
		});
	}
}