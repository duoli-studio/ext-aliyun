<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Repositories\Sour\LmTime;
use App\Models\PlatformLog;
use App\Models\PlatformMoney;
use App\Models\PlatformOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * 账户管理
 * Class PlatformSyncLogController
 * @package App\Http\Controllers\Desktop
 */
class PlatformStaticsController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}


	/**
	 * 订单资金统计
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getMoney(Request $request) {
		$Db       = PlatformOrder::orderBy('created_at', 'desc');
		$order_id = $request->input('order_id');
		if ($order_id) {
			$Db->where('order_id', $order_id);
		}
		if ($request->input('start_date')) {
			$Db->where('created_at', '>', LmTime::dayStart($request->input('start_date')));
		}
		if ($request->input('end_date')) {
			$Db->where('created_at', '<', LmTime::dayEnd($request->input('end_date')));
		}

		$this->orderRange($Db);

		$items = $Db->paginate($this->pagesize);

		$result = [
			'order_get_in_price' => 0,
			'order_price'        => 0,
			'fee_zhuandan'       => 0,
			'fee_pub_bufen'      => 0,
			'fee_sd_bufen'       => 0,
			'fee_sd_huaidan'     => 0,
			'fee_pub_buchang'    => 0,
			'fee_other'          => 0,
			'fee_earn'           => 0,
		];
		if ($items->total()) {
			(new Collection($items->items()))->each(function(&$item) use (&$result) {
				$result['order_get_in_price'] += $item->order_get_in_price;
				$result['order_price'] += $item->order_price;
				$result['fee_zhuandan'] += $item->fee_zhuandan;
				$result['fee_pub_bufen'] += $item->fee_pub_bufen;
				$result['fee_sd_bufen'] += $item->fee_sd_bufen;
				$result['fee_sd_huaidan'] += $item->fee_sd_huaidan;
				$result['fee_pub_buchang'] += $item->fee_pub_buchang;
				$result['fee_other'] += $item->fee_other;
				$item->fee_earn = PlatformOrder::calcFeeEarn($item);
				$result['fee_earn'] += $item->fee_earn;
			});
		}
		return view('desktop.platform_statics.money', [
			'items'  => $items,
			'result' => $result,
		]);
	}


	/**
	 * 资金流水账
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getJournal(Request $request) {
		$Db       = PlatformMoney::orderBy('created_at', 'desc');
		$order_id = $request->input('order_id');
		if ($order_id) {
			$Db->where('order_id', $order_id);
		}
		$type = $request->input('type');
		if ($type) {
			$Db->where('type', $type);
		}
		if ($request->input('start_date')) {
			$Db->where('created_at', '>', LmTime::dayStart($request->input('start_date')));
		}
		if ($request->input('end_date')) {
			$Db->where('created_at', '<', LmTime::dayEnd($request->input('end_date')));
		}

		$items = $Db->paginate($this->pagesize);
		$items->appends($request->except('_token'));
		$amount = 0;
		if ($items->total()) {
			(new Collection($items->items()))->each(function($item) use (&$amount) {
				$amount += $item->amount;
			});
		}
		return view('desktop.platform_statics.journal', [
			'items'  => $items,
			'amount' => sprintf('%01.2f', $amount),
		]);
	}


	/**
	 * Remove the specified resource from storage.
	 * @param Request $request
	 * @return \Response
	 */
	public function getEnter(Request $request) {
		$Db         = PlatformOrder::groupBy('publish_id')
			->where('order_status', '!=', PlatformOrder::ORDER_STATUS_DELETE)
			->select([
				'publish_id',
				\DB::raw('count(order_id) as publish_count'),
				\DB::raw('sum(order_get_in_price) as sum_get_in_price'),
				\DB::raw('sum(order_price) as sum_order_price'),
				\DB::raw('sum(fee_zhuandan) as sum_zhuandan'),
				\DB::raw('sum(fee_pub_bufen) as sum_pub_bufen'),
				\DB::raw('sum(fee_sd_bufen) as sum_sd_bufen'),
				\DB::raw('sum(fee_sd_huaidan) as sum_sd_huaidan'),
				\DB::raw('sum(fee_pub_buchang) as sum_pub_buchang'),
				\DB::raw('sum(fee_other) as sum_other'),
				\DB::raw('sum(fee_earn) as sum_fee_earn'),
			])
			->with('pam');
		$publish_id = $request->input('publish_id');
		if ($publish_id) {
			$Db->where('publish_id', $publish_id);
		}

		if ($request->input('start_date')) {
			$Db->where('created_at', '>', LmTime::dayStart($request->input('start_date')));
		}
		if ($request->input('end_date')) {
			$Db->where('created_at', '<', LmTime::dayEnd($request->input('end_date')));
		}

		$this->orderRange($Db);

		$items  = $Db->paginate($this->pagesize);
		$result = [
			'publish_count'    => 0,
			'sum_get_in_price' => 0,
			'sum_order_price'  => 0,
			'sum_zhuandan'     => 0,
			'sum_pub_bufen'    => 0,
			'sum_sd_bufen'     => 0,
			'sum_sd_huaidan'   => 0,
			'sum_pub_buchang'  => 0,
			'sum_other'        => 0,
			'sum_earn'         => 0,
		];

		if ($items->total()) {
			(new Collection($items->items()))->each(function(&$item) use (&$result) {
				$result['publish_count'] += $item->publish_count;
				$result['sum_get_in_price'] += $item->sum_get_in_price;
				$result['sum_order_price'] += $item->sum_order_price;
				$result['sum_zhuandan'] += $item->sum_zhuandan;
				$result['sum_pub_bufen'] += $item->sum_pub_bufen;
				$result['sum_sd_bufen'] += $item->sum_sd_bufen;
				$result['sum_sd_huaidan'] += $item->sum_sd_huaidan;
				$result['sum_pub_buchang'] += $item->sum_pub_buchang;
				$result['sum_other'] += $item->sum_other;
				$result['sum_earn'] += $item->fee_earn;
			});
		}
		$publishTotal = $result['publish_count'];
		$result       = array_map(function($item) {
			return sprintf('%01.2f', $item);
		}, $result);

		return view('desktop.platform_statics.enter', [
			'items'         => $items,
			'result'        => $result,
			'publish_total' => $publishTotal,
		]);
	}

	/**
	 * @param $Db PlatformOrder
	 */
	private function orderRange(&$Db) {
		$status = \Input::input('status');
		if (!is_array($status)) {
			return;
		}
		$Db->where(function(Builder $query) use ($status) {
			if (in_array('ing', $status)) {
				$query->orWhere('order_status', PlatformOrder::ORDER_STATUS_ING);
			}
			if (in_array('over', $status)) {
				$query->orWhere('order_status', PlatformOrder::ORDER_STATUS_OVER);
			}
			if (in_array('refund', $status)) {
				$query->orWhere('order_status', PlatformOrder::ORDER_STATUS_REFUND);
			}
			if (in_array('canceled', $status)) {
				$query->orWhere(function(Builder $q) {
					$q->where('order_status', PlatformOrder::ORDER_STATUS_CANCEL);
					$q->where('cancel_status', PlatformOrder::CANCEL_STATUS_DONE);
				});
			}
		});
	}

	public function getSms() {
		$Db        = PlatformLog::where('type', PlatformOrder::ORDER_STATUS_OVER)
			->orderBy('created_at', 'desc');
		$sms_total = PlatformLog::whereIn('type', [
			PlatformLog::ORDER_STATUS_OVER,
			PlatformLog::ORDER_STATUS_ING,
		])->count();
		$sms_lists = $Db->paginate($this->pagesize);
		return view('desktop.platform_statics.sms', [
			'sms_lists' => $sms_lists,
			'sms_total' => $sms_total,
		]);
	}

}
