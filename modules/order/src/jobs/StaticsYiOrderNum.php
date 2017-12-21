<?php namespace Order\Jobs;

use App\Jobs\Job;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Models\PlatformAccount;
use App\Models\PlatformStatus;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StaticsYiOrderNum extends Job implements SelfHandling, ShouldQueue
{

	use InteractsWithQueue;

	/** @var  PlatformAccount */
	private $platformAccount;

	/** @var  string */
	private $platform;

	/**
	 * @param $account
	 * @param $platform
	 */
	public function __construct($account, $platform)
	{
		$this->platformAccount = $account;
		$this->platform        = $platform;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$num = PlatformStatus::where('platform', $this->platform)
			->where('pt_account_id', $this->platformAccount->id)
			->where('yi_is_publish', 1)
			->where('yi_is_delete', 0)
			->where('yi_is_over', 0)
			->where(function ($query) {
				$query->orWhere('yi_order_status', Yi::ORDER_STATUS_ING);
				$query->orWhere(function ($q) {
					$q->where('yi_order_status', Yi::ORDER_STATUS_CANCEL);
					$q->where('yi_cancel_status', '!=', Yi::CANCEL_STATUS_DONE);
				});
			})
			->count();

		$this->platformAccount->order_num = $num;
		$this->platformAccount->save();
	}


}
