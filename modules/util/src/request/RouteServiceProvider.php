<?php namespace Util\Request;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Util\Request\Web\UtilController;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 * In addition, it is set as the URL generator's root namespace.
	 * @var string
	 */
	protected $namespace = 'Util\Request';

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

		//
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

		});
	}

	/**
	 * Define the "api" routes for the module.
	 * These routes are typically stateless.
	 * @return void
	 */
	// protected function mapApiRoutes()
	// {
	// 	\Route::group([
	// 		// todo auth
	// 		// 'middleware' => 'system',
	// 		'prefix' => 'api/util',
	// 	], function (Router $route) {
	//
	// 	});
	// }

	protected function mapApiRoutes()
	{
		\Route::group([
			'prefix' => 'util',
		], function (Router $router) {
			$router->get('/index',UtilController::class . '@index');
		});
	}
}
