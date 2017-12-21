<?php namespace System\Conf;

use Illuminate\Support\ServiceProvider;
use System\Conf\Repository\ConfRepository;


/**
 * Class SettingServiceProvider.
 */
class ConfServiceProvider extends ServiceProvider
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
		return ['system.conf'];
	}

	/**
	 * Register for service provider.
	 */
	public function register()
	{
		$this->app->singleton('system.conf', function () {
			return new ConfRepository();
		});
	}
}
