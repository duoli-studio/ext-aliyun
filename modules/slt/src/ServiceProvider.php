<?php namespace Slt;

use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider as ModuleServiceProviderBase;
use Slt\Console\FeCommand;
use Slt\Console\SampleCommand;
use Slt\Models\PrdBook;
use Slt\Models\PrdContent;
use Slt\Policies\PrdBookPolicy;
use Slt\Policies\PrdContentPolicy;
use Slt\Request\RouteServiceProvider;


class ServiceProvider extends ModuleServiceProviderBase
{

	protected $name = 'slt';

	protected $policies = [
		PrdContent::class => PrdContentPolicy::class,
		PrdBook::class    => PrdBookPolicy::class,
	];

	/**
	 * Bootstrap the application events.
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{
		$modulePath = poppy_path($this->name);
		$this->loadViewsFrom($modulePath . '/resources/views', $this->name);
		$this->loadTranslationsFrom($modulePath . '/resources/lang', $this->name);
		// policies
		$this->bootPolicies();
	}

	public function register()
	{
		$this->app->register(RouteServiceProvider::class);
		$this->registerCommand();
	}


	private function registerCommand()
	{
		$this->registerConsoleCommand('slt.fe', FeCommand::class);
		$this->registerConsoleCommand('slt.sample', SampleCommand::class);
	}

}
