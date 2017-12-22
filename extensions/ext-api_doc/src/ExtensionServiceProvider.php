<?php namespace Poppy\Extension\ApiDoc;

use Poppy\Extension\ApiDoc\Command\ApiDoc;
use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
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
		// 定义视图
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'poppy-api_doc');


		// 注册 api 文档配置
		$this->publishes([
			__DIR__ . '/../config/api_doc.php' => config_path('ext-api_doc.php'),
		]);

		if ($this->app->runningInConsole()) {
			$this->commands([
				ApiDoc::class,
			]);
		}
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		//
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
