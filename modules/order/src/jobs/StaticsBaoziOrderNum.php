<?php namespace Order\Jobs;

use App\Lemon\Dailian\Application\Platform\Baozi;
use App\Models\PlatformAccount;
use App\Models\PlatformStatus;
use Poppy\Framework\Application\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StaticsBaoziOrderNum extends Job implements SelfHandling, ShouldQueue
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
			->where('baozi_is_publish', 1)
			->where('baozi_is_delete', 0)
			->where('baozi_is_over', 0)
			->where(function ($query) {
				$query->orWhere('baozi_order_status', Baozi::ORDER_STATUS_ING);
				$query->orWhere(function ($q) {
					$q->where('baozi_order_status', Baozi::ORDER_STATUS_CANCEL);
					$q->where('baozi_cancel_status', '!=', Baozi::CANCEL_STATUS_DONE);
				});
			})
			->count();

		$this->platformAccount->order_num = $num;
		$this->platformAccount->save();
	}


}
