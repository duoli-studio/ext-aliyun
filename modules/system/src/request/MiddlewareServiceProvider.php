<?php namespace System\Request;


use Clockwork\Support\Laravel\ClockworkMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{

	public function boot(Router $router)
	{
		// backend is normal auth
		$router->aliasMiddleware('backend.auth', 'System\Request\Middleware\Auth');
		$router->middlewareGroup('backend', [
			'Illuminate\Cookie\Middleware\EncryptCookies',
			'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
			'Illuminate\Session\Middleware\StartSession',
			'Illuminate\View\Middleware\ShareErrorsFromSession',
			'System\Request\Middleware\VerifyCsrfToken',
			'Illuminate\Routing\Middleware\SubstituteBindings',
		]);

		if (env('APP_ENV', 'production') === 'local') {
			$router->pushMiddlewareToGroup('web', ClockworkMiddleware::class);
			$router->pushMiddlewareToGroup('backend', ClockworkMiddleware::class);
		}
	}
}