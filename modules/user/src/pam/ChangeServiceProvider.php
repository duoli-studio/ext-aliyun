<?php namespace User\Pam;

use Illuminate\Support\ServiceProvider;
use User\Pam\Action\ProfileChange;

/**
 * Class PermissionServiceProvider.
 */
class ChangeServiceProvider extends ServiceProvider
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
		$this->app->bind('act.profile_change', function ($app) {
			return new ProfileChange();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.profile_change',
		];
	}
}
