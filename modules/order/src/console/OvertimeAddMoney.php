<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Lemon\Repositories\Sour\LmStr;
use App\Models\PlatformOrder;
use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * 订单自动加价
 */
class OvertimeAddMoney extends Command
{

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'order:overtime-add-money';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'overtime add money';


	public function handle()
	{
		if (site('pt_overtime_add_money')) {
			$overtime_add_money = LmStr::parseKey(site('pt_overtime_add_money'));
			ksort($overtime_add_money);
			reset($overtime_add_money);
			$first_key = intval(key($overtime_add_money));
			$overtime  = date('Y-m-d H:i:s', Carbon::now()->timestamp - ($first_key * 3600));
			$items     = PlatformOrder::where('order_status', PlatformOrder::ORDER_STATUS_PUBLISH)
				->where('first_published_at', '<', $overtime)
				->get();
			foreach ($items as $item) {
				$hour = floor((Carbon::now()->timestamp - strtotime($item->first_published_at)) / 3600);
				$hour = intval($hour);
				if (isset($overtime_add_money[$hour])) {
					if ($item->overtime_add_money < $overtime_add_money[$hour]) {
						$add_money                = $overtime_add_money[$hour] - $item->overtime_add_money;
						$item->order_price        += $add_money;
						$item->overtime_add_money = $overtime_add_money[$hour];
						$item->save();
						$Order = new ActionPlatformOrder($item->order_id);
						$Order->batchRePublish();
					}
				}
			}
		}
	}
}