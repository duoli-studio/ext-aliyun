<?php namespace Order\Providers;


use Illuminate\Console\Scheduling\Schedule;
use Order\Game\GameServiceProvider;
use Order\Request\RouteServiceProvider;
use Poppy\Framework\Support\PoppyServiceProvider;

class ServiceProvider extends PoppyServiceProvider
{
	private   $name    = 'order';
	protected $listens = [

	];

	/**
	 * 应用的策略映射
	 * @var array
	 */
	protected $policies = [

	];

	/**
	 * Bootstrap the application events.
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
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
		$this->app->register(RouteServiceProvider::class);
		$this->app->register(GameServiceProvider::class);

		$this->registerCommand();

		$this->registerSchedule();
	}

	private function registerCommand()
	{

	}

	private function registerSchedule()
	{
		$this->app['events']->listen('console.schedule', function(Schedule $schedule) {
			// 同步详细
			$schedule->command('order:sync-detail')
				->everyMinute()
				->appendOutputTo(storage_path('console/platform_sync.log'));    // 同步详细
			// 平台重发
			$schedule->command('order:re-publish')
				->everyMinute()
				->appendOutputTo(storage_path('console/platform_sync.log'));
			// 重单收集
			$schedule->command('order:repeat-collect')
				->everyMinute()
				->appendOutputTo(storage_path('console/platform_repeat.log'));
			// 重单撤销
			$schedule->command('order:repeat-revocation')
				->everyMinute()
				->appendOutputTo(storage_path('console/platform_repeat.log'));
			// 发单重复, 未删除的
			$schedule->command('order:repeat-publish-collect')
				->everyMinute()
				->appendOutputTo(storage_path('console/platform_repeat.log'));

			/* 关闭战绩查询
			--------------------------------------------
			$schedule->command('order:tgp')
				->everyMinute();
			*/

			// framework

			/*
			//自动同步代练猫的订单
			$schedule->command('order:mao-lists')
				->everyFiveMinutes()
				->appendOutputTo(storage_path('console/platform_list.log'));

			//自动同步易代练的订单
			$schedule->command('order:yi-lists')
				->everyMinute()
				->appendOutputTo(storage_path('console/platform_list.log'));

			//自动同步代练妈妈的订单
			$schedule->command('order:mama-lists')
				->everyMinute()
				->appendOutputTo(storage_path('console/platform_list.log'));
			*/

			// 代练妈妈刷新
			$schedule->command('order:mama-refresh')->everyMinute();
			// 代练猫刷新
			$schedule->command('order:mao-refresh')->everyMinute();
			// 超时加钱
			$schedule->command('order:overtime-add-money')
				->everyTenMinutes()
				->appendOutputTo(storage_path('console/overtime_add_money.log'));
		});
	}

}




