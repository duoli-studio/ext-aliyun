<?php namespace System\Pam;

use Illuminate\Support\ServiceProvider;
use System\Pam\Action\Role;

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
		$this->app->bind('act.role', function ($app) {
			return new Role();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.role',
		];
	}
}
