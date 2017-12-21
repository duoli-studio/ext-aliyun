<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Application\Platform\Request\MaoReq;
use App\Models\PlatformAccount;
use Illuminate\Console\Command;

/**
 * 获取代练猫列表
 */
class GetMaoLists extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:get-mao-lists';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'get mao status lists';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$account_list = PlatformAccount::where('platform', PlatformAccount::PLATFORM_MAO)->get();
		foreach ($account_list as $account) {
			$verify = new MaoReq($account->id);
			$verify->syncTotal();
		}
	}
}
