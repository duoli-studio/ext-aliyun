<?php namespace Order\Skeleton\Console;

use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Mao;
use App\Lemon\Dailian\Application\Platform\Tong;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Models\BaseConfig;
use App\Models\PlatformAccount;
use App\Models\PlatformOrder;
use App\Models\PlatformRepeat;
use App\Models\PlatformStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * 重复撤销
 */
class RepeatRevocation extends Command
{

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'order:repeat-revocation';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Repeat revocation!';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{

		$repeat = PlatformRepeat::where('is_over', BaseConfig::N)->orderBy('id', 'desc')->first();
		if (!$repeat) {
			return;
		}

		$order = PlatformOrder::where('order_id', $repeat->order_id)->with('platformStatus')->first();
		if (is_null($order)) {
			$repeat->is_over = 1;
			$repeat->content = serialize([
				'status'  => 'no_order',
				'content' => '没有订单信息',
			]);
			$repeat->save();
		}
		else {
			if ($order->accept_id) {
				$repeat->main_status = $order->accept_platform . '|' . $order->accept_id;
			}
			$this->info('[重单]Id : ' . $order->order_id);
			$num   = 0;
			$desc  = [];
			$isPay = 0;

			/** @var PlatformStatus[]|Collection $collection */
			$collection = new  Collection();
			foreach ($order->platformStatus as $status) {
				// 去除已经接单, 对其余内容进行请求
				if ($status->id == $order->accept_id) {
					$this->info('[重单]接单平台' . $order->accept_platform . ', 接单Status[' . $status->id . ']');
					continue;
				}
				if (!($status->tong_is_accept || $status->mao_is_accept || $status->yi_is_accept || $status->mama_is_accept)) {
					$status->delete();
					$this->info('[重单]均未接单, 本条目[' . $status->id . ']删除');
					continue;
				}

				$collection->push($status);

				$num += 1;
				$this->info('[重单]平台 : ' . $status->platform);
				switch ($status->platform) {
					case PlatformAccount::PLATFORM_MAO:
						$mao       = new Mao($status->pt_account_id, $order, null);
						$nowStatus = $mao->getStatus();
						$this->info('[重单]平台mao 状态 : ' . $nowStatus->mao_order_status);
						if ($nowStatus->mao_order_status == Mao::ORDER_STATUS_OVER_CANCEL) {
							// 撤销中
							$desc[$nowStatus->id] = [
								'msg' => '[mao]订单已经撤销完成',
							];
						}
						elseif ($nowStatus->mao_order_status == Mao::ORDER_STATUS_CANCELING) {
							// 撤销中
							$desc[$nowStatus->id] = [
								'msg' => '[mao]订单撤销中',
							];
						}
						elseif ($nowStatus->mao_order_status == Mao::ORDER_STATUS_ING) {
							if ($mao->pubCancel(Mao::PUB_CANCEL_APPLY, 0, 0, '[PT]抱歉耽误您时间了')) {
								$desc[$nowStatus->id] = [
									'msg' => '[mao]订单申请撤销成功',
								];
							}
						}
						elseif ($nowStatus->mao_order_status == Mao::ORDER_STATUS_CREATE) {
							if ($mao->delete()) {
								$desc[$nowStatus->id] = [
									'msg' => '[mao]订单删除成功',
								];
							}
						}
						elseif ($nowStatus->mao_order_status == Mao::ORDER_STATUS_OVER_PAY) {
							$desc[$nowStatus->id] = [
								'msg' => '[mao]订单已经支付完毕',
							];
							// 支付完毕的标识
							$isPay = 1;
						}
						break;
					case PlatformAccount::PLATFORM_MAMA:
						$mama      = new Mama($status->pt_account_id, $order, null);
						$nowStatus = $mama->getStatus();
						$this->info('[重单]平台mama 状态 : ' . $nowStatus->mama_order_status);
						if ($nowStatus->mama_order_status == Mama::ORDER_STATUS_OVER_CANCEL) {
							// 撤销中
							$desc[$nowStatus->id] = [
								'msg' => '[mama]订单已经撤销完成',
							];
						}
						elseif ($nowStatus->mama_order_status == Mama::ORDER_STATUS_CANCELING) {
							// 撤销中
							$desc[$nowStatus->id] = [
								'msg' => '[mama]订单撤销中',
							];
						}
						elseif ($nowStatus->mama_order_status == Mama::ORDER_STATUS_ING) {
							if ($mama->pubCancel(Mama::PUB_CANCEL_APPLY, 0, 0, '[PT]抱歉耽误您时间了')) {
								$desc[$nowStatus->id] = [
									'msg' => '[mama]订单申请撤销成功',
								];
							}
						}
						elseif ($nowStatus->mama_order_status == Mama::ORDER_STATUS_CREATE) {
							if ($mama->delete()) {
								$desc[$nowStatus->id] = [
									'msg' => '[mama]订单删除成功',
								];
							}
						}
						elseif ($nowStatus->mama_order_status == Mama::ORDER_STATUS_OVER_PAY) {
							$desc[$nowStatus->id] = [
								'msg' => '[mama]订单已经支付完毕',
							];
							// 支付完毕的标识
							$isPay = 1;
						}

						break;
					case PlatformAccount::PLATFORM_TONG:
						$tong      = new Tong($status->pt_account_id, $order, null);
						$nowStatus = $tong->getStatus();
						$this->info('[重单]平台tong状态 : ' . $nowStatus->tong_order_status);
						if ($nowStatus->tong_order_status == Tong::ORDER_STATUS_CANCEL) {
							// 撤销中
							$desc[$nowStatus->id] = [
								'msg' => '[tong]订单已操作撤销',
							];
						}
						elseif ($nowStatus->tong_order_status == Tong::ORDER_STATUS_ING) {
							if ($tong->pubCancel(Tong::PUB_CANCEL_APPLY, 0, 0, '[PT]抱歉耽误您时间了')) {
								$desc[$nowStatus->id] = [
									'msg' => '[tong]订单申请撤销成功',
								];
							}
						}
						elseif ($nowStatus->tong_order_status == Tong::ORDER_STATUS_PUBLISH) {
							if ($tong->delete()) {
								$desc[$nowStatus->id] = [
									'msg' => '[tong]订单删除成功',
								];
							}
						}
						elseif ($nowStatus->tong_order_status == Tong::ORDER_STATUS_OVER) {
							$desc[$nowStatus->id] = [
								'msg' => '[tong]订单已经支付完毕',
							];
							// 支付完毕的标识
							$isPay = 1;
						}
						break;
					case PlatformAccount::PLATFORM_YI:
						$yi        = new Yi($status->pt_account_id, $order, null);
						$nowStatus = $yi->getStatus();
						$this->info('[重单]平台yi 状态 : ' . $nowStatus->yi_order_status);
						if ($nowStatus->yi_order_status == Yi::ORDER_STATUS_CANCEL) {
							// 撤销中
							$desc[$nowStatus->id] = [
								'msg' => '[yi]订单已操作撤销',
							];
						}
						elseif ($nowStatus->yi_order_status == Yi::ORDER_STATUS_ING) {
							if ($yi->pubCancel(Yi::PUB_CANCEL_APPLY, 0, 0, '[PT]抱歉耽误您时间了')) {
								$desc[$nowStatus->id] = [
									'msg' => '[yi]订单申请撤销成功',
								];
							}
						}
						elseif ($nowStatus->yi_order_status == Yi::ORDER_STATUS_CREATE) {
							if ($yi->delete()) {
								$desc[$nowStatus->id] = [
									'msg' => '[yi]订单删除成功',
								];
							}
						}
						elseif ($nowStatus->yi_order_status == Yi::ORDER_STATUS_OVER) {
							$desc[$nowStatus->id] = [
								'msg' => '[yi]订单已经支付完毕',
							];
							$isPay                = 1;
						}
						break;
				}

				if (!isset($desc[$status->id])) {
					$this->error('[重单]平台' . $status->platform . ' 状态无法处理');
					return;
				}
			}


			if ($num == count($desc)) {
				$repeat->is_over = 1;
				$strStatus       = '';
				foreach ($collection as $s) {
					$strStatus .= $s->platform . '|' . $s->id . ';';
				}
				$repeat->status  = $strStatus;
				$repeat->is_pay  = $isPay;
				$repeat->content = serialize([
					'status'  => 'handle_over',
					'content' => $desc,
				]);
				$repeat->save();
				$this->info('[重单]Id : ' . $order->order_id . ' 已经处理');
			}
			else {
				$this->error('[重单]Id : ' . $order->order_id . ' 无法处理');
			}
		}

	}
}