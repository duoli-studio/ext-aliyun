<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Application\Platform\Request\MamaReq;
use App\Models\PlatformAccount;
use Illuminate\Console\Command;

/**
 * 获取代练妈妈列表
 */
class GetMamaLists extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:get-mama-lists';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'get mama status lists';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$account_list = PlatformAccount::where('platform', PlatformAccount::PLATFORM_MAMA)->get();
		foreach ($account_list as $account) {
			$verify = new MamaReq($account->id);
			$verify->syncTotal();
		}
	}
}
