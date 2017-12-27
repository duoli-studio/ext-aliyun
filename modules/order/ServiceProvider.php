<?php namespace Order\Providers;


use Illuminate\Console\Scheduling\Schedule;
use Poppy\Framework\Support\PoppyServiceProvider as ModuleServiceProviderBase;

class ServiceProvider extends ModuleServiceProviderBase
{
	protected $listens = [
		'platform.order_create' => [
			OrderCreateMoney::class,
		],
		'platform.order_handle' => [
			OrderHandleMoney::class,
		],
		'platform.order_change' => [
			OrderChangeMoney::class,
		],
		'platform.order_cancel' => [
			OrderCancelMoney::class,
		],
	];

	/**
	 * 应用的策略映射
	 * @var array
	 */
	protected $policies = [
		PlatformOrder::class  => PlatformOrderPolicy::class,
		PlatformStatus::class => PlatformStatusPolicy::class,
	];

	/**
	 * Bootstrap the application events.
	 */
	public function boot()
	{
		parent::boot('order');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		parent::register('order');

		$this->registerCommand();

		$this->registerSchedule();
	}

	private function registerCommand()
	{
		$this->registerConsoleCommand('order.get_mama_list', 'Order\Skeleton\Console\GetMamaLists');
		$this->registerConsoleCommand('order.get_mao_lists', 'Order\Skeleton\Console\GetMaoLists');
		$this->registerConsoleCommand('order.get_yi_lists', 'Order\Skeleton\Console\GetYiLists');
		$this->registerConsoleCommand('order.mama_refresh', 'Order\Skeleton\Console\MamaRefresh');
		$this->registerConsoleCommand('order.mao_refresh', 'Order\Skeleton\Console\MaoRefresh');
		$this->registerConsoleCommand('order.overtime_add_money', 'Order\Skeleton\Console\OvertimeAddMoney');
		$this->registerConsoleCommand('order.repeat_collect', 'Order\Skeleton\Console\RepeatCollect');
		$this->registerConsoleCommand('order.repeat_publish_collect', 'Order\Skeleton\Console\RepeatPublishCollect');
		$this->registerConsoleCommand('order.repeat_revocation', 'Order\Skeleton\Console\RepeatRevocation');
		$this->registerConsoleCommand('order.re_publish', 'Order\Skeleton\Console\RePublish');
		$this->registerConsoleCommand('order.sync_detail', 'Order\Skeleton\Console\SyncDetail');
		$this->registerConsoleCommand('order.tgp', 'Order\Skeleton\Console\Tgp');
	}

	private function registerSchedule()
	{
		$this->app['events']->listen('console.schedule', function (Schedule $schedule) {
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




