<?php namespace System;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Console\Scheduling\Schedule;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\ModuleServiceProvider as ModuleServiceProviderBase;
use System\Addon\AddonServiceProvider;
use System\Backend\BackendServiceProvider;
use System\Classes\AuthProvider;
use System\Events\EventServiceProvider;
use System\Extension\ExtensionServiceProvider;
use System\Models\PamAccount;
use System\Module\ModuleServiceProvider;
use System\Rbac\RbacServiceProvider;
use System\Request\MiddlewareServiceProvider;
use System\Request\RouteServiceProvider;
use System\Setting\SettingServiceProvider;

class ServiceProvider extends ModuleServiceProviderBase
{
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

		$path = poppy_path($this->name);
		$this->mergeConfigFrom($path . '/resources/config/graphql.php', 'graphql');

		$this->loadViewsFrom($path . '/resources/views', $this->name);
		$this->loadTranslationsFrom($path . '/resources/lang', $this->name);
		$this->loadMigrationsFrom($path . '/resources/database/migrations');

		// register extension
		$this->app['extension']->register();

	}

	/**
	 * Register the module services.
	 * @return void
	 */
	public function register()
	{
		$this->app->register(MiddlewareServiceProvider::class);
		$this->app->register(RouteServiceProvider::class);
		$this->app->register(SettingServiceProvider::class);
		$this->app->register(BackendServiceProvider::class);
		$this->app->register(ExtensionServiceProvider::class);
		$this->app->register(AddonServiceProvider::class);
		$this->app->register(ModuleServiceProvider::class);
		$this->app->register(RbacServiceProvider::class);
		$this->app->register(EventServiceProvider::class);

		$this->registerAuth();
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


	public function provides()
	{
		return [
			'system.backend.manager',
		];
	}
}
