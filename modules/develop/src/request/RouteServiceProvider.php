<?php namespace Develop\Request;

/**
 * Copyright (C) Update For IDE
 */

use Develop\Request\Web\HomeController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 * In addition, it is set as the URL generator's root namespace.
	 * @var string
	 */
	protected $namespace = 'Develop\Request';

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
		Route::group([
			'prefix' => 'dev',
		], function (Router $router) {
			$router->any('login', HomeController::class . '@login')->name('develop:home.login');
			$router->group([
				'middleware' => 'auth:develop',
			], function (Router $router) {
				$router->get('/', HomeController::class . '@cp')->name('develop:home.cp');
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
		Route::group([
			'middleware' => 'api',
			'prefix'     => 'develop',
		], function ($router) {

		});
	}
}
