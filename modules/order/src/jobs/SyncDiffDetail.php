<?php namespace Order\Jobs;

use App\Lemon\Dailian\Action\ActionPlatformOrder;
use Poppy\Framework\Application\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncDiffDetail extends Job implements SelfHandling, ShouldQueue {

	use InteractsWithQueue;

	private $res;

	/**
	 * MaoVerifyStatus constructor.
	 * @param $res
	 */
	public function __construct($res) {
		$this->res = $res;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$Order = new ActionPlatformOrder($this->res);
		$Order->syncDetail();
		$Order->syncAccept();
		/*
		|--------------------------------------------------------------------------
		| 隐藏掉检测全部. 这里检测一个感觉是错误的
		|--------------------------------------------------------------------------
		|
		$order = PlatformOrder::with('platformStatus')->find($this->res);
		if ($order->platformStatus) {
			$allDestroy = true;
			foreach ($order->platformStatus as $status) {
				if ($status->platform == PlatformAccount::PLATFORM_MAMA && !$status->mama_is_delete) {
					$allDestroy = false;
				}
			}
			if ($allDestroy) {
				$order->order_status = PlatformOrder::ORDER_STATUS_CREATE;
				$order->save();
			}
		}
		*/
	}


}
