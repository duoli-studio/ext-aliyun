<?php namespace Util\Util;

use Illuminate\Support\ServiceProvider;


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
		$this->app->bind('act.util', 'Util\Util\Action\Util');
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
