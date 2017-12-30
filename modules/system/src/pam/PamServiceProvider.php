<?php namespace System\Pam;

use Illuminate\Support\ServiceProvider;
use System\Models\PamAccount;
use System\Pam\Action\Role;
use System\Pam\Auth\Guard\JwtAuthGuard;
use System\Pam\Auth\Provider\BackendProvider;
use System\Pam\Auth\Provider\DevelopProvider;
use System\Pam\Auth\Provider\WebProvider;

/**
 * Class PermissionServiceProvider.
 */
class PamServiceProvider extends ServiceProvider
{
	/**
	 * @var bool
	 */
	protected $defer = true;


	/**
	 * ServiceProvider register.
	 */
	public function register()
	{

		\Auth::provider('pam.web', function ($app) {
			return new WebProvider(PamAccount::class);
		});
		\Auth::provider('pam.backend', function ($app) {
			return new BackendProvider(PamAccount::class);
		});
		\Auth::provider('pam.develop', function ($app) {
			return new DevelopProvider(PamAccount::class);
		});

		\Auth::extend('jwt.backend', function ($app, $name, array $config) {
			// dd($app['auth']->createUserProvider($config['provider']));
			$guard = new JwtAuthGuard(
				$app['tymon.jwt'],
				$app['auth']->createUserProvider($config['provider']),
				$app['request']
			);
			$app->refresh('request', $guard, 'setRequest');
			return $guard;
		});


		$this->app->bind('act.role', function ($app) {
			return new Role();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.role',
		];
	}
}
