<?php namespace Slt\Request;


use EloquentFilter\TestClass\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Slt\Request\Web\UserController;
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
			'middleware' => 'web',
			'prefix'     => 'slt',
		], function (Router $router) {
			$router->get('/', HomeController::class . '@index')
				->name('slt');
			$router->get('tool/{type?}', ToolController::class . '@index')
				->name('slt:tool');

			$router->any('nav', NavController::class . '@index')
				->name('slt:nav.index');
			$router->get('nav/jump/{id?}', NavController::class . '@jump')
				->name('slt:nav.jump');
			$router->get('nav/jump_user/{id?}', NavController::class . '@jumpUser')
				->name('slt:nav.jump_user');
			$router->any('nav/collection/{id?}', NavController::class . '@collection')
				->name('slt:nav.collection');
			$router->any('nav/collection_destroy/{id?}', NavController::class . '@collectionDestroy')
				->name('slt:nav.collection_destroy');
			$router->any('nav/url/{id?}', NavController::class . '@url')
				->name('slt:nav.url');
			$router->any('nav/url_destroy/{id?}', NavController::class . '@urlDestroy')
				->name('slt:nav.url_destroy');
			$router->any('nav/title', NavController::class . '@title')
				->name('slt:nav.title');
			$router->any('nav/tag', NavController::class . '@tag')
				->name('slt:nav.tag');

			// user
			$router->any('user/login', UserController::class . '@login')
				->name('slt:user.login');
			$router->any('user/register/{type?}', UserController::class . '@register')
				->name('slt:user.register');
			$router->any('user/forgot_password', UserController::class . '@getForgotPassword')
				->name('slt:user.forgot_password');
			$router->group([
				'middleware' => 'auth:web',
			], function (Router $router) {
				$router->any('user/profile', UserController::class . '@profile')
					->name('slt:user.profile');
				$router->any('user/nickname', UserController::class . '@nickname')
					->name('slt:user.nickname');
				$router->any('user/avatar', UserController::class . '@avatar')
					->name('slt:user.avatar');
				$router->any('user/logout', UserController::class . '@logout')
					->name('slt:user.logout');
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
			'namespace'  => $this->namespace . '\Api',
			'prefix'     => 'api',
		], function ($router) {

		});
	}
}





