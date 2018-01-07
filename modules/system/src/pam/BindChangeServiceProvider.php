<?php namespace System\Pam;

use Illuminate\Support\ServiceProvider;
use System\Pam\Action\BindChange;

/**
 * Class PermissionServiceProvider.
 */
class BindChangeServiceProvider extends ServiceProvider
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
		$this->app->bind('act.bind_change', function ($app) {
			return new BindChange();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.bind_change',
		];
	}
}

