<?php namespace Poppy\Extension\Fe;

use Poppy\Extension\Fe\Console\Bower;
use Poppy\Framework\Support\ModuleServiceProvider;

class ExtensionServiceProvider extends ModuleServiceProvider
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
		$this->publishes([
			__DIR__ . '/../config/fe.php' => config_path('ext-fe.php'),
		], 'poppy-extension');
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		// 配置文件合并
		$this->mergeConfigFrom(__DIR__ . '/../config/fe.php', 'ext-fe');
		$this->registerConsoleCommand('extension.fe.bower', Bower::class);

	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
