<?php namespace System\Request;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Poppy\Framework\GraphQL\GraphQLController;
use System\Request\Api\AuthController;
use System\Request\Api\ConfigurationController;
use System\Request\Api\DashboardsController;
use System\Request\Api\InformationController;
use System\Request\Backend\BeHomeController;
use System\Request\Develop\DevController;
use System\Request\System\HomeController;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 * In addition, it is set as the URL generator's root namespace.
	 * @var string
	 */
	protected $namespace = 'System\Request';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 * @return void
	 */
	public function boot()
	{
		//

		parent::boot();
	}

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
			$router->get('/graphi/{schema?}', HomeController::class . '@graphi');
			$router->get('/login', HomeController::class . '@login');
			$router->get('/test', HomeController::class . '@test');
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
		\Route::group([
			'middleware' => 'web',
			'prefix'     => 'develop',
		], function (Router $router) {
			$router->any('login', DevController::class . '@login')->name('system:develop.login');
			$router->group([
				'middleware' => 'auth:develop',
			], function (Router $router) {
				$router->get('/', DevController::class . '@cp')->name('system:develop.cp');
			});

		});

		\Route::group([
			'prefix' => 'develop/system',
		], function (Router $router) {
			$router->get('/', HomeController::class . '@layout');
			$router->get('/graphi/{schema?}', HomeController::class . '@graphi');
			$router->get('/login', HomeController::class . '@login');
			$router->get('/test', HomeController::class . '@test');
		});
	}

	/**
	 * Define the "api" routes for the module.
	 * These routes are typically stateless.
	 * @return void
	 */
	protected function mapApiRoutes()
	{
		\Route::group([
			'middleware' => ['cross'],
			'prefix'     => 'api/system',
		], function (Router $route) {
			$route->any('/graphql', GraphQLController::class . '@query');
			$route->any('/token', AuthController::class . '@token');
			$route->any('/information', InformationController::class . '@list');
			$route->any('/dashboard', DashboardsController::class . '@list');
			$route->group([
				'middleware' => ['auth:api'],
			], function (Router $route) {
				$route->any('/graphql/backend', GraphQLController::class . '@query');
				$route->any('/access', AuthController::class . '@access');
				$route->any('/configuration/{path?}', ConfigurationController::class . '@definition');
			});

		});
	}
}
