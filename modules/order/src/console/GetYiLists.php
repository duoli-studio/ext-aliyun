<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Application\Platform\Request\YiReq;
use App\Models\PlatformAccount;
use Illuminate\Console\Command;

/**
 * 获取易代练列表
 */
class GetYiLists extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:get-yi-lists';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'get yi status lists';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$account_list = PlatformAccount::where('platform', PlatformAccount::PLATFORM_YI)->get();
		foreach ($account_list as $account) {
			$verify = new YiReq($account->id);
			$verify->syncTotal();
		}
	}
}
