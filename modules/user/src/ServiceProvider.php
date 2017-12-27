<?php namespace User;


use Poppy\Framework\Exceptions\ModuleNotFoundException;
use User\Pam\PamServiceProvider;
use User\Request\RouteServiceProvider;
use Poppy\Framework\Support\PoppyServiceProvider;

# use Sour\Lemon\Support\Resp;
# use Sour\System\Command\Rbac as LemonRbac;
# use Sour\System\Command\Fe as LemonFe;

class ServiceProvider extends PoppyServiceProvider
{
	private $name = 'user';

	protected $policies = [];

	protected $listens = [
		'Illuminate\Auth\Events\Failed' => [
			'User\Events\Listeners\FailedLog',
		],
		'Illuminate\Auth\Events\Login'  => [
			'User\Events\Listeners\LoginLog',
			'User\Events\Listeners\LoginNum',
		],
		'User\Pam\Events\PamRegistered' => [
			// 统计
		],
	];


	/**
	 * Bootstrap the application events.
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{

		parent::boot($this->name);

	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		$this->registerCommand();
		$this->app->register(RouteServiceProvider::class);
		$this->app->register(PamServiceProvider::class);
	}


	/**
	 * Register command
	 */
	private function registerCommand()
	{
		$this->registerConsoleCommand('user.rbac', 'User\Console\Rbac');
		$this->registerConsoleCommand('user.init', 'User\Console\Init');
		$this->registerConsoleCommand('user.user', 'User\Console\User');
		// $this->registerConsoleCommand('lemon.fe', LemonFe::class);
	}


	public function provides()
	{
		return [

		];
	}
}
