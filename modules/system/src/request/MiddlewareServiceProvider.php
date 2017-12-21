<?php namespace System\Request;


use Clockwork\Support\Laravel\ClockworkMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{

	public function boot(Router $router)
	{
		$router->aliasMiddleware('system.auth', 'System\Request\Middleware\Auth');
		$router->middlewareGroup('system', [
			'Illuminate\Cookie\Middleware\EncryptCookies',
			'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
			'Illuminate\Session\Middleware\StartSession',
			'Illuminate\View\Middleware\ShareErrorsFromSession',
			'System\Request\Middleware\VerifyCsrfToken',
			'Illuminate\Routing\Middleware\SubstituteBindings',
		]);

		if (env('APP_ENV', 'production') === 'local') {
			$router->pushMiddlewareToGroup('web', ClockworkMiddleware::class);
			$router->pushMiddlewareToGroup('system', ClockworkMiddleware::class);
		}
	}
}