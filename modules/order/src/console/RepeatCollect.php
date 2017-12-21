<?php namespace Order\Skeleton\Console;

use App\Models\PlatformRepeat;
use App\Models\PlatformStatus;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * 重单收集
 */
class RepeatCollect extends Command
{

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'order:repeat-collect';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Collect Repeat Order';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{

		$repeat = PlatformStatus::select(\DB::raw('order_id, count(id) as count'))
			->where(function (Builder $query) {
				$query->orWhere('tong_is_accept', 1);
				$query->orWhere('mao_is_accept', 1);
				$query->orWhere('yi_is_accept', 1);
				$query->orWhere('mama_is_accept', 1);
			})->where('pt_account_id', '!=', 0)
			->groupBy('order_id')
			->havingRaw('count>1')
			->get();
		$data   = [];
		foreach ($repeat as $item) {
			$data[$item['order_id']] = $item['count'];
		}

		if ($data) {
			$notIn = PlatformRepeat::lists('order_id', 'order_id');
			foreach ($data as $order_id => $count) {
				if (!isset($notIn[$order_id])) {
					PlatformRepeat::updateOrCreate([
						'order_id' => $order_id,
					], [
						'order_id'    => $order_id,
						'num'         => $count,
						'main_status' => 0,
						'status'      => '',
						'is_over'     => 0,
						'need_user'   => 0,  // 需要人工干预
						'content'     => '',
					]);
				}
			}
		}
	}
}