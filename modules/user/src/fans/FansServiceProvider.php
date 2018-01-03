<?php namespace User\Fans;

use Illuminate\Support\ServiceProvider;
use User\Fans\Action\Fans;

/**
 * Class PermissionServiceProvider.
 */
class FansServiceProvider extends ServiceProvider
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
		$this->app->bind('act.fans', function ($app) {
			return new Fans();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.fans',
		];
	}
}
