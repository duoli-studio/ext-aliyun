<?php namespace Slt;

use Poppy\Framework\Support\ModuleServiceProvider as ModuleServiceProviderBase;
use Slt\Models\PrdBook;
use Slt\Models\PrdContent;
use Slt\Policies\PrdBookPolicy;
use Slt\Policies\PrdContentPolicy;
use Slt\Request\RouteServiceProvider;


class ServiceProvider extends ModuleServiceProviderBase
{


	protected $policies = [
		PrdContent::class => PrdContentPolicy::class,
		PrdBook::class    => PrdBookPolicy::class,
	];

	/**
	 * Bootstrap the application events.
	 */
	public function boot()
	{
		parent::boot('slt');
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
		$this->registerConsoleCommand('slt.fe', 'Slt\Console\Fe');
	}

}
