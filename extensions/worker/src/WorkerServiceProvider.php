<?php namespace Poppy\Extension\Worker;

use Illuminate\Support\ServiceProvider;

class WorkerServiceProvider extends ServiceProvider
{

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerCommandCreate();
		$this->registerCommandStart();
		$this->registerCommandStop();
		$this->registerCommandRestart();
		$this->registerCommandReload();
		$this->registerCommandStatus();

		$this->registerCredentialProcessor();
	}

	private function registerCommandCreate()
	{
		$this->app->singleton(
			'command.poppy.ext-worker.create',
			function ($app) {
				return $app['Poppy\Extension\Worker\Commands\CreateCommand'];
			}
		);

		$this->commands('command.poppy.ext-worker.create');
	}

	private function registerCommandStart()
	{
		$this->app->singleton(
			'command.poppy.ext-worker.start',
			function ($app) {
				return $app['Poppy\Extension\Worker\Commands\StartCommand'];
			}
		);

		$this->commands('command.poppy.ext-worker.start');
	}

	private function registerCommandStop()
	{
		$this->app->singleton(
			'command.poppy.ext-worker.stop',
			function ($app) {
				return $app['Poppy\Extension\Worker\Commands\StopCommand'];
			}
		);

		$this->commands('command.poppy.ext-worker.stop');
	}

	private function registerCommandRestart()
	{
		$this->app->singleton(
			'command.poppy.ext-worker.restart',
			function ($app) {
				return $app['Poppy\Extension\Worker\Commands\RestartCommand'];
			}
		);

		$this->commands('command.poppy.ext-worker.restart');
	}

	private function registerCommandReload()
	{
		$this->app->singleton(
			'command.poppy.ext-worker.reload',
			function ($app) {
				return $app['Poppy\Extension\Worker\Commands\ReloadCommand'];
			}
		);

		$this->commands('command.poppy.ext-worker.reload');
	}

	private function registerCommandStatus()
	{
		$this->app->singleton(
			'command.poppy.ext-worker.status',
			function ($app) {
				return $app['Poppy\Extension\Worker\Commands\StatusCommand'];
			}
		);

		$this->commands('command.poppy.ext-worker.status');
	}

	private function registerCredentialProcessor()
	{
		$this->app->singleton(
			'poppy.ext-worker.credentialprocessor',
			function ($app) {
				$credentialProcessorClass = \Config::get('poppy-worker.credential_processor', 'Poppy\Extension\Worker\CredentialProcessor');
				return $app[$credentialProcessorClass];
			}
		);
	}

	public function boot()
	{
		// Configuration publish
		$this->publishes([
			__DIR__ . '/config/worker.php' => config_path('poppy-worker.php'),
		], 'poppy');
	}
}