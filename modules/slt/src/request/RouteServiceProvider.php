<?php namespace Slt\Request;


use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Slt\Request\Web\FeController;
use Slt\Request\Web\UserController;
use Slt\Request\Web\HomeController;
use Slt\Request\Web\NavController;
use Slt\Request\Web\ToolController;
use Slt\Request\Web\UtilController;

class RouteServiceProvider extends ServiceProvider
{

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
	}

	/**
	 * Define the "web" routes for the module.
	 * These routes all receive session state, CSRF protection, etc.
	 * @return void
	 */
	protected function mapWebRoutes()
	{
		\Route::group([
			'middleware' => ['cross', 'web'],
		], function() {
			require_once poppy_path('slt', 'src/request/routes/web_v1.php');
		});
	}
}





