<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Lemon\Repositories\Sour\LmStr;
use App\Models\PlatformOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * 订单重发
 */
class RePublish extends Command
{

	protected $signature = 'order:re-publish';

	protected $description = 'RePublish not accepted';

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{
		$interval = site('pt_re_publish_interval');
		$amount   = site('pt_re_publish_amount');
		if (!$interval || !$amount) {
			$this->info('尚未设置重新发单资料, 无法进行自动发单!');
			return;
		}
		$rePublishTimer = Carbon::now()->subMinutes($interval)->toDateTimeString();

		$nameOrderIds    = cache_name(__CLASS__, 'order_id');
		$nameErrorIds    = cache_name(__CLASS__, 'error_id');
		$nameForgotTimer = cache_name(__CLASS__, 'mark');
		$cacheOrderIds   = \Cache::get($nameOrderIds);
		$cacheErrorIds   = \Cache::get($nameErrorIds);

		// 尚未接单并且是已经发布， 并且发单时间超出范围
		$Db = PlatformOrder::where('order_status', PlatformOrder::ORDER_STATUS_PUBLISH)
			->where('employee_publish', 0)
			->where('published_at', '<', $rePublishTimer)
			->whereNotNull('published_at')
			->orderBy('published_at', 'asc');
		if ($cacheOrderIds) {
			$Db->whereNotIn('order_id', $cacheOrderIds);
		}
		if ($cacheErrorIds) {
			$Db->whereNotIn('order_id', $cacheErrorIds);
		}

		$allRePublishAmount = PlatformOrder::where('order_status', PlatformOrder::ORDER_STATUS_PUBLISH)->count();
		$currentHandleItems = $Db->take($amount)->get();

		$rand = LmStr::random(4, '0123456789');


		if (!empty($currentHandleItems)) {
			// 压入不重复队列
			foreach ($currentHandleItems as $item) {
				$cacheOrderIds[$item->order_id] = $item->order_id;
			}
			\Cache::forever($nameOrderIds, $cacheOrderIds);

			// mark
			$mark = '重发 RT ' . $rand . ' @ ';
			$this->info($mark . '-----------' . Carbon::now()->toDateTimeString() . ' # statics index(' . ((int) \Cache::get($nameForgotTimer) + 0) . ') ' . $allRePublishAmount . ' @ ' . count($cacheOrderIds) . "\n");
			$cacheOrderIds = [];
			foreach ($currentHandleItems as $num => $item) {
				sleep(1);

				$cacheOrderIds = \Cache::get($nameOrderIds);
				$orderId       = $item->order_id;
				$no            = count($cacheOrderIds);
				$index         = $num + 1;
				$innerMark     = $mark . "-" . sprintf("%03d", $index);
				if (!isset($cacheOrderIds[$orderId])) {
					$this->info($innerMark . '       order id :' . $orderId . ' ignore ~' . "\n");
					continue;
				}
				else {
					$this->info($innerMark . " ({$no}) " . Carbon::now()->toDateTimeString() . " start, order id : " . $orderId . "\n");
					unset($cacheOrderIds[$orderId]);
					\Cache::forever($nameOrderIds, $cacheOrderIds);
				}

				try {
					// 发单前先同步
					$Order = new ActionPlatformOrder($orderId);
					if ($Order->rePublish()) {
						$this->info($innerMark . "       " . Carbon::now()->toDateTimeString() . ' # result success, order id : ' . $orderId . "\n");
					}
					else {
						$msg = $Order->getError();
						$this->info($innerMark . "       " . Carbon::now()->toDateTimeString() . ' # result error, order id : ' . $orderId . " msg: {$msg}\n");

						// save to error
						$cacheErrorIds           = \Cache::get($nameErrorIds);
						$cacheErrorIds[$orderId] = $orderId;
						\Cache::forever($nameErrorIds, $cacheErrorIds);
					}

				} catch (\Exception $e) {
					$this->info(($innerMark . "       " . Carbon::now()->toDateTimeString() . ' # result fail, order id : ' . "$orderId, message : " . $e->getMessage() . "\n"));
				}
			}
			\Cache::increment($nameForgotTimer);
			if (\Cache::get($nameForgotTimer) > 5) {
				\Cache::forget($nameForgotTimer);
				\Cache::forget($nameOrderIds);
				\Cache::forget($nameErrorIds);
				$this->info($mark . '-----------' . Carbon::now()->toDateTimeString() . ' # statics refresh ' . "\n");
			}
			$this->info($mark . '-----------' . Carbon::now()->toDateTimeString() . ' # statics loop over, left ' . count($cacheOrderIds) . "\n");
		}
	}
}