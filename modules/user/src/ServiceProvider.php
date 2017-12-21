<?php namespace User;


use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Support\ModuleServiceProvider as ModuleServiceProviderBase;

# use Sour\Lemon\Support\Resp;
# use Sour\System\Command\Rbac as LemonRbac;
# use Sour\System\Command\Fe as LemonFe;

class ServiceProvider extends ModuleServiceProviderBase
{
	private $name = 'user';

	protected $listens = [
		'Illuminate\Auth\Events\Failed' => [
			'User\Events\Listeners\FailedLog',
		],
		'Illuminate\Auth\Events\Login'  => [
			'User\Events\Listeners\LoginLog',
			'User\Events\Listeners\LoginNum',
		],
	];


	/**
	 * Bootstrap the application events.
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{

		parent::boot($this->name);

		// policies
		$this->bootPolicies();

		// listener
		$this->bootListener();

		// validation
		$this->bootValidation();

	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{

		$this->registerCommand();

	}


	/**
	 * Register command
	 */
	private function registerCommand()
	{
		$this->registerConsoleCommand('pam.rbac', 'User\Console\Rbac');
		$this->registerConsoleCommand('pam.init', 'User\Console\Init');
		$this->registerConsoleCommand('pam.user', 'User\Console\User');
		// $this->registerConsoleCommand('lemon.fe', LemonFe::class);
	}


	private function bootValidation()
	{
		\Validator::extend('mobile', function ($attribute, $value, $parameters) {
			return UtilHelper::isMobile($value);
		});
		\Validator::extend('json', function ($attribute, $value, $parameters) {
			return UtilHelper::isJson($value);
		});
		\Validator::extend('date', function ($attribute, $value, $parameters) {
			return UtilHelper::isDate($value);
		});
		\Validator::extend('chid', function ($attribute, $value, $parameters) {
			return UtilHelper::isChId($value);
		});
		\Validator::extend('pwd', function ($attribute, $value, $parameters) {
			return UtilHelper::isPwd($value);
		});
	}

	public function provides()
	{
		return [

		];
	}
}
