<?php namespace User\Pam;

use Illuminate\Support\ServiceProvider;
use User\Pam\Action\Pay;

/**
 * Class PermissionServiceProvider.
 */
class PayServiceProvider extends ServiceProvider
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
		$this->app->bind('act.pay', function ($app) {
			return new Pay();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.pay',
		];
	}
}
