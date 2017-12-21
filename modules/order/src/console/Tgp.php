<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Models\PlatformOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Tgp extends Command
{

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'order:tgp';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Sync Platform tgp';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{
		$interval = site('pt_detail_sync_interval');
		$amount   = site('pt_detail_sync_amount');

		if (!$interval || !$amount) {
			$this->info('尚未设置同步战绩, 无法自动获取状态!');
			return;
		}
		// $syncDatetime = Carbon::now()->subMinutes($interval)->toDateTimeString();
		$items = PlatformOrder::where('order_status', PlatformOrder::ORDER_STATUS_ING)
			->where('employee_publish', 0)// 非员工单
			->orderBy('tpl_updated_at', 'asc')
			->take('1')->get();

		if (!empty($items)) {
			$this->info('查询战绩：' . count($items) . ' @ ' . Carbon::now()->toDateTimeString());
			foreach ($items as $item) {
				$Order = new ActionPlatformOrder($item->order_id);
				$Order->getRecord($item->order_id);

			}
		}
	}
}