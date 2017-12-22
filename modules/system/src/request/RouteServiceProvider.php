<?php namespace System\Request;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Poppy\Framework\GraphQL\GraphQLController;
use System\Request\Api\ConfigurationController;
use System\Request\Api\ConfigurationsController;
use System\Request\Api\DashboardsController;
use System\Request\Api\InformationController;
use System\Request\Backend\BeHomeController;
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
			'middleware' => 'system',
			'prefix'     => 'system',
		], function (Router $router) {
			$router->get('/', HomeController::class . '@layout');
			$router->get('/graphi', HomeController::class . '@graphi');
		});
		\Route::group([
			'middleware' => 'backend',
			'prefix'     => 'backend',
		], function (Router $router) {
			$router->get('/login', BeHomeController::class . '@login');
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
			// todo auth
			// 'middleware' => 'system',
			'prefix' => 'api/system',
		], function (Router $route) {
			$route->any('/graphql/{schema?}', GraphQLController::class . '@query');
			$route->any('/information', InformationController::class . '@list');
			$route->any('/dashboard', DashboardsController::class . '@list');
			$route->any('/configuration/{path?}', ConfigurationController::class . '@definition');
		});
	}
}
