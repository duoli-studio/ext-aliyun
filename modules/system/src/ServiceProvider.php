<?php namespace System;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Console\Scheduling\Schedule;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Addon\AddonServiceProvider;
use System\Extension\Extension;
use System\Backend\BackendServiceProvider;
use System\Classes\AuthProvider;
use System\Extension\ExtensionServiceProvider;
use System\Rbac\Rbac;
use System\Models\PamAccount;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\ModuleServiceProvider as ModuleServiceProviderBase;
use System\Request\MiddlewareServiceProvider;
use System\Request\RouteServiceProvider;
use System\Setting\ConfServiceProvider;

class ServiceProvider extends ModuleServiceProviderBase
{
	use PoppyTrait;
	/**
	 * @var string Module name
	 */
	protected $name = 'system';

	/**
	 * Bootstrap the module services.
	 * @return void
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{
		parent::boot($this->name);

		$path = poppy_path($this->name);
		$this->mergeConfigFrom($path . '/resources/config/graphql.php', 'graphql');

		// rbac
		$this->bootRbacBladeDirectives();
	}

	/**
	 * Register the module services.
	 * @return void
	 */
	public function register()
	{
		$this->app->register(MiddlewareServiceProvider::class);
		$this->app->register(RouteServiceProvider::class);
		$this->app->register(ConfServiceProvider::class);
		$this->app->register(BackendServiceProvider::class);
		$this->app->register(ExtensionServiceProvider::class);
		$this->app->register(AddonServiceProvider::class);

		$this->registerAuth();
		$this->registerRbac();
		$this->registerSchedule();
		$this->registerConsole();
	}

	/**
	 * 注册
	 */
	private function registerAuth()
	{
		\Auth::provider('system.pam', function ($app) {
			return new AuthProvider(PamAccount::class);
		});
	}

	/**
	 * register rbac and alias
	 */
	private function registerRbac()
	{
		$this->app->bind('system.rbac', function ($app) {
			return new Rbac($app);
		});
		$this->app->alias('system.rbac', 'System\Rbac\Rbac');
	}


	private function registerSchedule()
	{
		$this->app['events']->listen('console.schedule', function (Schedule $schedule) {
			$schedule->command('system:sample')
				->everyMinute()
				->appendOutputTo(storage_path('console/system.log'));

			$schedule->command('clockwork:clean')->everyThirtyMinutes();
		});
	}


	private function registerConsole()
	{
		$this->registerConsoleCommand('system.sample', 'System\Console\Sample');
	}

	/**
	 * Register the blade directives
	 * @return void
	 */
	private function bootRbacBladeDirectives()
	{
		// Call to Entrust::hasRole
		\Blade::directive('role', function ($expression) {
			return "<?php if (\\Rbac::hasRole{$expression}) : ?>";
		});

		\Blade::directive('endrole', function ($expression) {
			return "<?php endif; // Rbac::hasRole ?>";
		});

		// Call to Entrust::capable
		\Blade::directive('permission', function ($expression) {
			return "<?php if (\\Rbac::capable{$expression}) : ?>";
		});

		\Blade::directive('endpermission', function ($expression) {
			return "<?php endif; // Rbac::capable ?>";
		});

		// Call to Entrust::ability
		\Blade::directive('ability', function ($expression) {
			return "<?php if (\\Rbac::ability{$expression}) : ?>";
		});

		\Blade::directive('endability', function ($expression) {
			return "<?php endif; // Rbac::ability ?>";
		});
	}

	public function provides()
	{
		return [
			'system.rbac',
			'system.backend.manager',
		];
	}
}
