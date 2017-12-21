<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Application\Platform\Request\MaoReq;
use App\Models\PlatformAccount;
use Illuminate\Console\Command;

/**
 * 刷新代练猫订单
 */
class MaoRefresh extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:mao-refresh';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh Mao Order!';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$account_list = PlatformAccount::where('platform', PlatformAccount::PLATFORM_MAO)->get();
		foreach ($account_list as $account) {
			$mark   = '[' . $account->mao_account . ']';
			$verify = new MaoReq($account->id);
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