<?php namespace System;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Console\Scheduling\Schedule;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;
use System\Addon\AddonServiceProvider;
use System\Addon\Commands\GenerateCommand;
use System\Addon\Commands\ListCommand;
use System\Addon\Commands\ListUnloadedCommand;
use System\Backend\BackendServiceProvider;
use System\Console\DevHtmlCommand;
use System\Console\InstallCommand;
use System\Events\ListenerServiceProvider;
use System\Extension\ExtensionServiceProvider;
use System\Module\ModuleServiceProvider;
use System\Pam\Commands\UserCommand;
use System\Pam\PamServiceProvider;
use System\Permission\Commands\PermissionCommand;
use System\Permission\PermissionServiceProvider;
use System\Rbac\RbacServiceProvider;
use System\Request\MiddlewareServiceProvider;
use System\Request\RouteServiceProvider;
use System\Setting\SettingServiceProvider;

class ServiceProvider extends PoppyServiceProvider
{

	protected $listens = [
		'Poppy\Framework\Poppy\Events\PoppyOptimized' => [
			'System\Module\Listeners\ClearCacheListener',
			'System\Extension\Listeners\ClearCacheListener',
		],
	];


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
		$this->app->register(ListenerServiceProvider::class);
		$this->app->register(PermissionServiceProvider::class);
		$this->app->register(PamServiceProvider::class);

		$this->registerSchedule();
		$this->registerConsole();
	}


	private function registerSchedule()
	{
		$this->app['events']->listen('console.schedule', function (Schedule $schedule) {

			$schedule->command('clockwork:clean')->everyThirtyMinutes();
		});
	}


	private function registerConsole()
	{
		// system
		$this->registerConsoleCommand('system.permission', PermissionCommand::class);
		$this->registerConsoleCommand('system.user', UserCommand::class);
		$this->registerConsoleCommand('system.install', InstallCommand::class);
		$this->registerConsoleCommand('system.dev_html', DevHtmlCommand::class);

		// addon
		$this->registerConsoleCommand('addon.generate', GenerateCommand::class);
		$this->registerConsoleCommand('addon.list', ListCommand::class);
		$this->registerConsoleCommand('addon.list_unloaded', ListUnloadedCommand::class);
	}


	public function provides()
	{
		return [];
	}
}
