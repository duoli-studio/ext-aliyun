<?php namespace System\Module;

use Illuminate\Support\ServiceProvider;

/**
 * Class ModuleServiceProvider.
 */
class ModuleServiceProvider extends ServiceProvider
{
	/**
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * @return array
	 */
	public function provides()
	{
		return ['module'];
	}

	/**
	 * Register for service provider.
	 */
	public function register()
	{
		$this->app->singleton('module', function ($app) {
			return new ModuleManager();
		});
	}
}
