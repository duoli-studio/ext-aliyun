<?php namespace Order\Skeleton\Console;

use App\Jobs\Platform\SyncDetailJob;
use App\Models\PlatformOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncDetail extends Command {

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'order:sync-detail';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Sync Platform Detail Content';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle() {
		$interval = site('pt_detail_sync_interval');
		$amount   = site('pt_detail_sync_amount');

		if (!$interval || !$amount) {
			$this->info('尚未设置同步订单资料, 无法自动获取状态!');
			return;
		}
		$syncDatetime = Carbon::now()->subMinutes($interval)->toDateTimeString();
		$items        = PlatformOrder::whereNotIn('order_status', [
			PlatformOrder::ORDER_STATUS_CREATE,
			PlatformOrder::ORDER_STATUS_OVER,
			PlatformOrder::ORDER_STATUS_REFUND,
			PlatformOrder::ORDER_STATUS_DELETE,
		])
			->where('employee_publish', 0)   // 非员工单
			->where('cancel_status', '!=', PlatformOrder::CANCEL_STATUS_DONE)
			->where('updated_at', '<', $syncDatetime)
			->orderBy('updated_at', 'asc')
			->take($amount)->get();

		if (!empty($items)) {
			$this->info('同步详情：' . count($items) . ' @ ' . Carbon::now()->toDateTimeString());
			foreach ($items as $item) {
				dispatch(new SyncDetailJob($item->order_id));
			}
		}
	}
}