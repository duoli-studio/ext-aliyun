<?php namespace Order\Jobs;

use App\Jobs\Job;
use App\Models\PlatformAccount;
use App\Models\PlatformStatus;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StaticsMamaOrderNum extends Job implements SelfHandling, ShouldQueue
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
			->where('mama_is_publish', 1)
			->where('mama_is_delete', 0)
			->where('mama_is_over', 0)
			->count();

		$this->platformAccount->order_num = $num;
		$this->platformAccount->save();
	}


}