<?php namespace Order\Game;

use Illuminate\Support\ServiceProvider;
use Order\Game\Action\Server;


class GameServiceProvider extends ServiceProvider
{
	/**
	 *  是否延时加载提供器
	 * @var bool
	 */
	protected $defer = true;


	/**
	 * 在容器中注册绑定
	 * ServiceProvider register.
	 */
	public function register()
	{
		$this->app->bind('act.server', function ($app) {
			return new Server();
		});
	}

	/**
	 * 获取提供器提供的服务
	 * @return array
	 */
	public function provides()
	{
		return [
			'act.server',
		];
	}
}
