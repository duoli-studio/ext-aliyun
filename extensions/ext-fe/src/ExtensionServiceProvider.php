<?php namespace Poppy\Extension\Fe;

use Poppy\Extension\Fe\Console\Bower;
use Poppy\Framework\Support\PoppyServiceProvider;

class ExtensionServiceProvider extends PoppyServiceProvider
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
			__DIR__ . '/../config/fe.php'                         => config_path('ext-fe.php'),
			__DIR__ . '/../resources/css/be_login.css'            => public_path('assets/css/poppy-ext-fe/be_login.css'),
			__DIR__ . '/../resources/mixes/lemon/cp.js'           => public_path('assets/js/lemon/cp.js'),
			__DIR__ . '/../resources/mixes/lemon/doc.js'          => public_path('assets/js/lemon/doc.js'),
			__DIR__ . '/../resources/mixes/lemon/plugin.js'       => public_path('assets/js/lemon/plugin.js'),
			__DIR__ . '/../resources/mixes/lemon/util.js'         => public_path('assets/js/lemon/util.js'),
			__DIR__ . '/../resources/mixes/lemon/backend/cp.js'   => public_path('assets/js/lemon/backend/cp.js'),
			__DIR__ . '/../resources/mixes/lemon/backend/util.js' => public_path('assets/js/lemon/backend/util.js'),
		], 'poppy-extension');

		$this->loadViewsFrom(__DIR__ . '/../resources/views/', 'poppy-fe');
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
