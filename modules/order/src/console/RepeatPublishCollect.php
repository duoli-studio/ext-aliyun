<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Mao;
use App\Lemon\Dailian\Application\Platform\Tong;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Lemon\Repositories\Sour\LmArr;
use App\Models\PlatformAccount;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * 重复发布的收集
 */
class RepeatPublishCollect extends Command
{

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'order:repeat-publish-collect';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Collect Repeat Order where has publish item';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		// 获取已经进行中的除去
		// 删除 (delete), 完成 (over), 退款(refund), 员工(is_employee=1)
		$orderIds = PlatformOrder::where('employee_publish', 0)
			->whereIn('order_status', [
				PlatformOrder::ORDER_STATUS_ING,
				PlatformOrder::ORDER_STATUS_EXAMINE,
				PlatformOrder::ORDER_STATUS_EXCEPTION,
				PlatformOrder::ORDER_STATUS_CANCEL,
			])->where('cancel_status', '!=', PlatformOrder::CANCEL_STATUS_DONE)
			->lists('order_id');

		// 获取有两个数量的
		// dd($orderIds);
		/** @var Collection $repeat */
		$repeat = PlatformStatus::whereIn('order_id', $orderIds)
			->where('pt_account_id', '!=', 0)
			->where(function (Builder $query) {
				$query->orWhere('mao_order_status', Mao::ORDER_STATUS_CREATE);
				$query->orWhere('mama_order_status', Mama::ORDER_STATUS_CREATE);
				$query->orWhere('tong_order_status', Tong::ORDER_STATUS_PUBLISH);
				$query->orWhere('yi_order_status', Yi::ORDER_STATUS_PUBLISH);
			})
			->groupBy('order_id')
			->havingRaw('count(id)>1')
			->get();

		// 所有 publish 的去除掉
		if ($repeat->count()) {
			foreach ($repeat as $status) {
				$order = PlatformOrder::find($status->order_id);
				$this->info('[重单] Order Id : ' . $status->order_id);
				// 删除是发布状态的订单
				switch ($status->platform) {
					case PlatformAccount::PLATFORM_MAO:
						$mao       = new Mao($status->pt_account_id, $order, null);
						$nowStatus = $mao->getStatus();
						if ($nowStatus->mao_order_status == Mao::ORDER_STATUS_CREATE) {
							$this->info('[重单]平台mao 状态 : ' . $nowStatus->mao_order_status);
							$mao->delete();
						}
						break;
					case PlatformAccount::PLATFORM_MAMA:
						$mama      = new Mama($status->pt_account_id, $order, null);
						$nowStatus = $mama->getStatus();

						if ($nowStatus->mama_order_status == Mama::ORDER_STATUS_CREATE) {
							$this->info('[重单]平台mama 状态 : ' . $nowStatus->mama_order_status);
							$mama->delete();
						}
						break;
					case PlatformAccount::PLATFORM_TONG:
						$tong      = new Tong($status->pt_account_id, $order, null);
						$nowStatus = $tong->getStatus();
						if ($nowStatus->tong_order_status == Tong::ORDER_STATUS_PUBLISH) {
							$this->info('[重单]平台tong状态 : ' . $nowStatus->tong_order_status);
							$tong->delete();
						}
						break;
					case PlatformAccount::PLATFORM_YI:
						$yi        = new Yi($status->pt_account_id, $order, null);
						$nowStatus = $yi->getStatus();

						if ($nowStatus->yi_order_status == Yi::ORDER_STATUS_PUBLISH) {
							$this->info('[重单]平台yi 状态 : ' . $nowStatus->yi_order_status);
							// 撤销中
							$yi->delete();
						}
						break;
				}
			}
		}

	}
}