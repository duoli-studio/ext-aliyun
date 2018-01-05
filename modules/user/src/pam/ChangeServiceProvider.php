<?php namespace User\Pam;

use Illuminate\Support\ServiceProvider;
use User\Pam\Action\Change;

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
		$this->app->bind('act.change', function ($app) {
			return new Change();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.change',
		];
	}
}
