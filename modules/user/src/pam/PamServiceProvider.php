<?php namespace User\Pam;

use Illuminate\Support\ServiceProvider;
use User\Pam\Action\Pam;

/**
 * Class PermissionServiceProvider.
 */
class PamServiceProvider extends ServiceProvider
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
		$this->app->bind('act.pam', function ($app) {
			return new Pam();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.pam',
		];
	}
}
