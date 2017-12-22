<?php namespace Poppy\Extension\IpStore;

use Illuminate\Support\ServiceProvider;

class IpStoreServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot()
	{
		// 发布 config 文件, 在命令行中使用 --tag=duoli 来确认配置文件
		$this->publishes([
			__DIR__ . '/../config/config.php' => config_path('l5-ip.php'), // config
		], 'duoli');

	}


	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfig();
		$this->registerIp();
	}

	/**
	 * Merges user's and sl-upload's configs.
	 * @return void
	 */
	private function mergeConfig()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../config/config.php', 'l5-ip'
		);
	}

	private function registerIp()
	{
		$this->app->singleton('duoli.ip', function () {
			$type  = ucfirst(camel_case(config('l5-ip.store')));
			$class = 'Poppy\Extension\IpStore\\Repositories\\' . $type;
			$sms   = new $class();
			return $sms;
		});
	}


	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides()
	{
		return ['duoli.ip'];
	}

}
