<?php namespace User\Pam;

use Illuminate\Support\ServiceProvider;

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
		$this->app->bind('act.pam', 'User\Pam\Action\Pam');
		$this->app->bind('act.user', 'User\Pam\Action\User');
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.pam',
			'act.user',
		];
	}
}
