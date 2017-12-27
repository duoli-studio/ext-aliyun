<?php namespace Util\Util;

use Illuminate\Support\ServiceProvider;
use Util\Util\Action\Util;


class UtilServiceProvider extends ServiceProvider
{
	/**
	 * @var bool
	 */
	protected $defer = true;


	/**
	 * ServiceProvider register.
	 */
	public function register()
	{
		$this->app->bind('act.util', function ($app) {
			return new Util();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.util',
		];
	}
}
