<?php namespace Util;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Console\Scheduling\Schedule;
use Util\Request\RouteServiceProvider;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;
use Util\Util\UtilServiceProvider;

class ServiceProvider extends PoppyServiceProvider
{
	protected $listens = [
		'console.schedule' => [

		],
	];
	/**
	 * @var string The poppy name slug.
	 */
	private $name = 'util';

	/**
	 * Bootstrap the module services.
	 * @return void
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{
		parent::boot($this->name);
	}

	/**
	 * Register the module services.
	 * @return void
	 */
	public function register()
	{
		$this->app->register(RouteServiceProvider::class);
		$this->app->register(UtilServiceProvider::class);

		$this->registerConsoleCommand('util.clear_captcha', 'Util\Util\Console\ClearCaptchaCommand');

		$this->registerSchedule();
	}


	private function registerSchedule()
	{
		$this->app['events']->listen('console.schedule', function (Schedule $schedule) {
			$schedule->command('util:clear-captcha')
				->everyMinute()
				->appendOutputTo(storage_path('console/util.log'));
		});
	}
}
