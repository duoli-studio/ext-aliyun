<?php namespace System;

/**
 * Copyright (C) Update For IDE
 */
use Illuminate\Console\Scheduling\Schedule;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;
use System\Backend\BackendServiceProvider;
use System\Classes\Auth\Guard\JwtAuthGuard;
use System\Classes\Auth\Provider\BackendProvider;
use System\Classes\Auth\Provider\DevelopProvider;
use System\Classes\Auth\Provider\PamProvider;
use System\Classes\Auth\Provider\WebProvider;
use System\Classes\FeForm;
use System\Commands\BowerCommand;
use System\Commands\DevHtmlCommand;
use System\Commands\DocCommand;
use System\Commands\InstallCommand;
use System\Commands\LogCommand;
use System\Commands\PamAutoEnableCommand;
use System\Commands\UserCommand;
use System\Extension\ExtensionServiceProvider;
use System\Models\PamAccount;
use System\Models\PamRole;
use System\Models\Policies\AccountPolicy;
use System\Models\Policies\RolePolicy;
use System\Module\ModuleServiceProvider;
use System\Permission\Commands\PermissionCommand;
use System\Permission\PermissionServiceProvider;
use System\Rbac\RbacServiceProvider;
use System\Request\MiddlewareServiceProvider;
use System\Request\RouteServiceProvider;
use System\Setting\SettingServiceProvider;

class ServiceProvider extends PoppyServiceProvider
{
	/**
	 * @var string Module name
	 */
	protected $name = 'system';

	protected $listens = [
		'Poppy\Framework\Poppy\Events\PoppyOptimized' => [
			'System\Module\Listeners\ClearCacheListener',
			'System\Extension\Listeners\ClearCacheListener',
		],
		'System\Setting\Events\SettingUpdated'        => [
			'System\Module\Listeners\ClearCacheListener',
		],
		'System\Event\LoginSuccessEvent'              => [
			'System\Listeners\LoginSuccess\LogListener',
		],
	];

	protected $policies = [
		PamRole::class    => RolePolicy::class,
		PamAccount::class => AccountPolicy::class,
	];

	/**
	 * Bootstrap the module services.
	 * @return void
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{
		parent::boot($this->name);
		$path = poppy_path($this->name);

		// 配置文件
		$this->mergeConfigFrom($path . '/resources/config/graphql.php', 'graphql');
		$this->mergeConfigFrom($path . '/resources/config/fe.php', 'fe');

		// register extension
		$this->app['extension']->register();

		$this->bootConfigMail();
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
		$this->app->register(ModuleServiceProvider::class);
		$this->app->register(RbacServiceProvider::class);
		$this->app->register(PermissionServiceProvider::class);

		$this->registerConsole();

		$this->registerAuth();

		$this->registerSchedule();
	}

	private function registerSchedule()
	{
		$this->app['events']->listen('console.schedule', function (Schedule $schedule) {
			$schedule->command('system:pam-auto_enable')
				->everyThirtyMinutes()->appendOutputTo($this->consoleLog());
			// auto clean
			$schedule->command('clockwork:clean')
				->everyThirtyMinutes()->appendOutputTo($this->consoleLog());
		});
	}

	private function registerConsole()
	{
		// system
		$this->commands([
			PermissionCommand::class,
			UserCommand::class,
			InstallCommand::class,
			DevHtmlCommand::class,
			PamAutoEnableCommand::class,
			BowerCommand::class,
			DocCommand::class,
		]);
	}

	private function registerAuth()
	{
		\Auth::provider('pam.web', function ($app) {
			return new WebProvider(PamAccount::class);
		});
		\Auth::provider('pam.backend', function ($app) {
			return new BackendProvider(PamAccount::class);
		});
		\Auth::provider('pam.develop', function ($app) {
			return new DevelopProvider(PamAccount::class);
		});
		\Auth::provider('pam', function ($app) {
			return new PamProvider(PamAccount::class);
		});

		\Auth::extend('jwt.backend', function ($app, $name, array $config) {
			$guard = new JwtAuthGuard(
				$app['tymon.jwt'],
				$app['auth']->createUserProvider($config['provider']),
				$app['request']
			);
			$app->refresh('request', $guard, 'setRequest');

			return $guard;
		});

		$this->app->singleton('poppy.form', function ($app) {
			$form = new FeForm($app['html'], $app['url'], $app['view'], $app['session.store']->token());

			return $form->setSessionStore($app['session.store']);
		});
	}

	private function bootConfigMail()
	{
		try {
			// mail config
			$Setting = app('setting');
			config([
				'mail.driver'       => $Setting->get('system::mail.driver') ?: config('mail.driver'),
				'mail.encryption'   => $Setting->get('system::mail.encryption') ?: config('mail.encryption'),
				'mail.port'         => $Setting->get('system::mail.port') ?: config('mail.port'),
				'mail.host'         => $Setting->get('system::mail.host') ?: config('mail.host'),
				'mail.from.address' => $Setting->get('system::mail.from') ?: config('mail.from.address'),
				'mail.from.name'    => $Setting->get('system::mail.from') ?: config('mail.from.name'),
				'mail.username'     => $Setting->get('system::mail.username') ?: config('mail.username'),
				'mail.password'     => $Setting->get('system::mail.password') ?: config('mail.password'),
			]);
		} catch (\Exception $e) {
			\Log::error('[System:ServiceProvider]' . $e->getMessage());
		}
	}

	public function provides()
	{
		return [
			'poppy.form',
		];
	}
}