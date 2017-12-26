<?php namespace Slt\Request;


use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Slt\Request\Web\HomeController;
use Slt\Request\Web\NavController;
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
		], function (Router $router) {
			$router->get('/', HomeController::class . '@index')
				->name('slt');
			$router->get('tool/{type?}', ToolController::class . '@index')
				->name('slt:tool');

			$router->any('nav', NavController::class . '@index')
				->name('web:nav.index');
			$router->get('nav/jump/{id?}', NavController::class . '@jump')
				->name('web:nav.jump');
			$router->get('nav/jump_user/{id?}', NavController::class . '@jumpUser')
				->name('web:nav.jump_user');
			$router->any('nav/collection/{id?}', NavController::class . '@collection')
				->name('web:nav.collection');
			$router->any('nav/collection_destroy/{id?}', NavController::class . '@collectionDestroy')
				->name('web:nav.collection_destroy');
			$router->any('nav/url/{id?}', NavController::class . '@url')
				->name('web:nav.url');
			$router->any('nav/url_destroy/{id?}', NavController::class . '@urlDestroy')
				->name('web:nav.url_destroy');
			$router->any('nav/title', NavController::class . '@title')
				->name('web:nav.title');
			$router->any('nav/tag', NavController::class . '@tag')
				->name('web:nav.tag');
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





