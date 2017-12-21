<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Application\Platform\Request\MamaReq;
use App\Models\PlatformAccount;
use Illuminate\Console\Command;

/**
 * 刷新代练妈妈订单
 */
class MamaRefresh extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:mama-refresh';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh Mama Order!';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$account_list = PlatformAccount::where('platform', PlatformAccount::PLATFORM_MAMA)->get();
		foreach ($account_list as $account) {
			$mark   = '[' . $account->mama_account . ']';
			$verify = new MamaReq($account->id);
			if (!$verify->make('refresh_all')) {
				$this->error($mark . $verify->getError());
			}
			$result = $verify->getResp();
			if ($result['result'] == 1) {
				// success
				$this->info($mark . '刷新成功');
			}
			else {
				// error
				$this->error($mark . $result['data']);
			}
		}
	}
}

