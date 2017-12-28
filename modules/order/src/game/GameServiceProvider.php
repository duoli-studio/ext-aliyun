<?php namespace Order\Game;

use Illuminate\Support\ServiceProvider;
use Order\Game\Action\Server;


class GameServiceProvider extends ServiceProvider
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
		$this->app->bind('act.game', function ($app) {
			return new Server();
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.game',
		];
	}
}
