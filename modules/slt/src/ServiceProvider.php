<?php namespace Slt;

use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider as ModuleServiceProviderBase;
use Slt\Console\FeCommand;
use Slt\Console\SampleCommand;
use Slt\Models\ArticleBook;
use Slt\Models\ArticleContent;
use Slt\Policies\ArticleBookPolicy;
use Slt\Policies\ArticleContentPolicy;
use Slt\Request\RouteServiceProvider;


class ServiceProvider extends ModuleServiceProviderBase
{

	protected $name = 'slt';

	protected $policies = [
		ArticleContent::class => ArticleContentPolicy::class,
		ArticleBook::class    => ArticleBookPolicy::class,
	];

	/**
	 * Bootstrap the application events.
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{
		parent::boot($this->name);
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
