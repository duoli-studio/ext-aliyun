<?php namespace System\Request;

use Clockwork\Support\Laravel\ClockworkMiddleware;
use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Http\Middlewares\CrossPreflight;
use System\Request\Middleware\DisabledPam;

class MiddlewareServiceProvider extends ServiceProvider
{
	public function boot(Router $router)
	{
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

		$router->aliasMiddleware('disabled_pam', DisabledPam::class);

		// add options
		if ($this->app->make('request')->getMethod() == 'OPTIONS') {
			$this->app->make(KernelContract::class)->prependMiddleware(CrossPreflight::class);
		}
	}
}