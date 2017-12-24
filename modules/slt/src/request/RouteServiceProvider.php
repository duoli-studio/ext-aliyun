<?php namespace Slt\Request;


use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Slt\Request\Web\HomeController;
use Slt\Request\Web\ToolController;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 * In addition, it is set as the URL generator's root namespace.
	 * @var string
	 */
	protected $namespace = 'Slt\Request';

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
			'prefix' => 'slt',
		], function ($router) {
			$router->get('/', HomeController::class . '@index');
			$router->get('/tool/{type?}', ToolController::class . '@index')->name('slt:tool');
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
			'namespace'  => $this->namespace . '\Api',
			'prefix'     => 'api',
		], function ($router) {

		});
	}
}





