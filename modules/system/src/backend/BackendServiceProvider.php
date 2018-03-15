<?php namespace System\Backend;

use Illuminate\Support\ServiceProvider;

/**
 * Class AdministrationServiceProvider.
 */
class BackendServiceProvider extends ServiceProvider
{
	/**
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register for service provider.
	 */
	public function register()
	{
		$this->app->singleton('backend', function () {
			return new BackendManager();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return ['backend'];
	}
}
