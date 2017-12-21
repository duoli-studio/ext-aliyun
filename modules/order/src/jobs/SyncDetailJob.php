<?php namespace Order\Jobs;

/*
| This is NOT a Free software.
| When U have some Question or Advice can contact Me.
| @author     Mark <zhaody901@126.com>
| @copyright  Copyright (c) 2013-2017 Sour Lemon Team
*/

use App\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * 同步详情
 */
class SyncDetailJob extends Job implements SelfHandling, ShouldQueue
{

	use InteractsWithQueue;

	protected $orderId;


	/**
	 * 统计用户计算数量
	 * @param $order_id
	 */
	public function __construct($order_id)
	{
		$this->orderId = $order_id;
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		try {
			pt_sync_order($this->orderId, $log);
			// \Log::debug('sync detail start @ job'.Carbon::now());
		} catch (\Exception $e) {
			\Log::error('SYNC:ORDER_ID:' . $this->orderId);
			\Log::error($log);
		}
	}
}