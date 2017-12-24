<?php namespace System\Setting;

use Illuminate\Support\ServiceProvider;
use System\Setting\Repository\SettingRepository;


/**
 * Class SettingServiceProvider.
 */
class SettingServiceProvider extends ServiceProvider
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
		return ['system.setting'];
	}

	/**
	 * Register for service provider.
	 */
	public function register()
	{
		$this->app->singleton('system.setting', function () {
			return new SettingRepository();
		});
	}
}
