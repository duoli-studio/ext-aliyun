<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Repositories\Sour\LmFile;
use App\Lemon\Repositories\Sour\LmTime;
use App\Lemon\Repositories\System\SysSearch;
use App\Models\GameLog;
use App\Models\GamePicture;
use App\Models\PamAccount;
use App\Models\PamRoleAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformBind;
use App\Models\PlatformLog;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * 员工功能
 * Class PlatformEmployeeController
 * @package App\Http\Controllers\Desktop
 */
class PlatformEmployeeController extends InitController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * 员工专属订单列表
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getIndex(Request $request)
	{
		$days = [
			'2'  => '两天内',
			'7'  => '一周内',
			'10' => '十天内',
			'-'  => '超时',
		];

		$pay_status = [
			'Y' => '是',
			'N' => '否',
		];

		$fields = [
			'order_id'     => '订单ID',
			'game_account' => '游戏账号',
			'order_title'  => '订单标题',
			'game_actor'   => '游戏角色',
		];

		/** @type PlatformOrder $Db */
		$Db = PlatformOrder::whereRaw('1=1');
		$Db->where('employee', \Auth::user()->account_id);
		$kw    = trim($request->input('kw'));
		$field = trim($request->input('field'));
		if ($field && isset($fields[$field]) && $kw) {
			$Db->where($field, 'like', '%' . $kw . '%');
		}

		$order_status = $request->input('order_status');
		if ($order_status) {
			$Db->where('order_status', $order_status);
		}

		$game_id = (int) $request->input('game_id');
		if ($game_id) {
			$Db->where('game_id', $game_id);
		}

		$server_id = $request->input('server_id');
		if ($server_id) {
			$Db->where('server_id', $server_id);
		}

		$type_id = intval($request->input('type_id'));
		if ($type_id) {
			$Db->where('type_id', $type_id);
		}

		// 发单时间
		$publish_start_date = $request->input('publish_start_date');
		if ($publish_start_date) {
			$Db->where('published_at', '>=', LmTime::dayStart($publish_start_date));
		}
		$publish_end_date = $request->input('publish_end_date');
		if ($publish_end_date) {
			$Db->where('published_at', '<', LmTime::dayEnd($publish_end_date));
		}
		if ($publish_start_date || $publish_end_date) {
			$Db->whereNotNull('published_at');
		}

		/* 合作方全文检索 [(Mark Zhao) 16-7-3]
		$publish_account = $request->input('publish_account');
		if ($publish_account) {
			$order->whereRaw(\DB::raw('match (publish_account) against (? IN BOOLEAN MODE)'), [',_' . $publish_account . '_,']);
		}
		----------- */


		$Db->with('platformStatus');
		$Db->with('platformAccept');
		$Db->with('platformAccount');

		$orderKey = SysSearch::key('created_at', [
			'created_at', 'published_at', 'order_left_hours',
		]);

		$Db->orderBy($orderKey, SysSearch::order());
		$items = $Db->paginate($this->pagesize);
		$items->appends($request->input());
		$handleAccount = PamAccount::where('account_type', PamAccount::ACCOUNT_TYPE_DESKTOP)->lists('account_name', 'account_id');

		$export = $request->input('export');
		if ($export) {
			$exports = [
				'order_title'      => '订单标题',
				'published_at'     => '发布时间',
				'order_status'     => [
					'订单状态',
					'App\Models\PlatformOrder::kvOrderStatus',
				],
				'game_account'     => '游戏账号',
				'game_actor'       => '角色名',
				'order_price'      => '发单价格',
				'server_big_title' => '服务器大区',
				'game_area'        => '服务器',
			];
			LmFile::exportPaginate($items, $exports);
		}

		return view('desktop.platform_employee.index', [
			'items'          => $items,
			'days'           => $days,
			'pay_status'     => $pay_status,
			'fields'         => $fields,
			'handle_account' => $handleAccount,
		]);
	}

	/**
	 * 员工专属资金管理
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getMoney()
	{
		$money_list   = PlatformOrder::where('employee', \Auth::user()->account_id)->get();
		$money_over   = 0;
		$money_handle = 0;
		foreach ($money_list as $v) {
			if ($v->order_status == PlatformOrder::ORDER_STATUS_OVER) {
				$money_over += $v->order_price;
			}
			else {
				$money_handle += $v->order_price;
			}
		}
		$money_total = $money_over + $money_handle;
		return view('desktop.platform_employee.money', [
			'money_total'  => $money_total,
			'money_over'   => $money_over,
			'money_handle' => $money_handle,
		]);
	}

	/**
	 * 资金详情列表
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getMoneyList()
	{
		$Db         = PlatformOrder::where('employee', \Auth::user()->account_id);
		$money_list = $Db->paginate($this->pagesize);
		return view('desktop.platform_employee.money_list', [
			'items' => $money_list,
		]);
	}

	/**
	 * 员工 显示游戏信息
	 * @param $order_id
	 * @return \Illuminate\View\View
	 */
	public function getEmployeeOrderDetail($order_id)
	{
		/** @type PlatformOrder $order */
		$order    = PlatformOrder::with('platformAccept')->findOrFail($order_id);
		$progress = PlatformLog::where('order_id', $order_id)
			->orderBy('created_at', 'Desc')
			->with('pam')
			->paginate(5);
		$canEdit  = \Gate::check('edit', $order);
		$data     = [
			'order_id'      => $order_id,
			'order'         => $order,
			'progress'      => $progress,
			'edit_disabled' => $canEdit ? '' : 'disabled',
		];
		if ($order->platformAccept) {
			$data['status'] = $order->platformAccept;
		}
		return view('desktop.platform_employee.detail', $data);
	}

	/**
	 * 不接手订单  删除订单
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postDelete($id)
	{
		$platformStatus = PlatformStatus::where('order_id', $id)
			->where('employee_id', \Auth::user()->account_id);
		if ($platformStatus->delete()) {
			$msg = '删除订单成功!';
		}
		else {
			$msg = '未能成功删除';
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
		$platformOrder                   = PlatformOrder::find($id);
		$platformOrder->employee_publish = 0;
		$platformOrder->order_status     = PlatformOrder::ORDER_STATUS_CREATE;
		$platformOrder->published_at     = Carbon::now();
		$platformOrder->employee         = 0;
		$platformOrder->save();
		$success = '成功发布给员工';
		PlatformOrder::logPublishReason($id, 'success', $success, $platformOrder);//$account_id

		return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
	}

	/**
	 * 将订单发布给某个指定的员工
	 * @param $order_id
	 * @param $account_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postAssignEmployee($order_id, $account_id)
	{
		$platformOrder = PlatformOrder::find($order_id);
		if ($platformOrder->employee) {
			return AppWeb::resp(AppWeb::ERROR, '订单已发布', 'reload|1');
		}
		$platformOrder->employee_publish = 1;
		$platformOrder->order_status     = PlatformOrder::ORDER_STATUS_PUBLISH;
		$platformOrder->published_at     = Carbon::now();
		$platformOrder->employee         = $account_id;
		PlatformStatus::firstOrCreate([
			'employee_id'           => $account_id,
			'order_id'              => $order_id,
			'platform'              => 'employee',
			'employee_order_status' => PlatformOrder::ORDER_STATUS_PUBLISH,
		]);
		$platformOrder->save();
		$success = '成功发布给员工';
		PlatformOrder::logPublishReason($account_id, 'success', $success, $platformOrder);
		return AppWeb::resp(AppWeb::SUCCESS, $success, 'reload_opener|workspace');
	}

	/**
	 * 订单分配给员工
	 * @param $order_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getPublishToEmployee($order_id)
	{
		/** @type PlatformOrder $order */
		$order = PlatformOrder::with('platformStatus')->find($order_id);
		// - 已经接单
		$assign['order'] = $order;
		if ($order->accept_id) {
			$assign['accept']         = true;
			$assign['accept_account'] = PlatformAccount::find($order->accept_platform_account_id);//有问题
		}
		else {
			$assign['accept'] = false;
		}
		// 显示当前的接单账号
		/** @type Collection $accountIds */
		$accountIds = PlatformBind::where('account_id', $this->pam->account_id)->lists('platform_account_id');

		$hasAccount       = false;
		$platformAccounts = new Collection();
		if (!$accountIds->isEmpty()) {
			$hasAccount = true;
			/** @type Collection $platformAccounts */
			$platformAccounts = PlatformAccount::whereIn('id', $accountIds)->get();
		}

		/* 未发布成功的提示  ----------- */
		$publishReason = $order->publish_reason ? unserialize($order->publish_reason) : [];
		$accountIds->each(function($account_id) use (& $publishReason) {
			if (!isset($publishReason[$account_id . '_error'])) {
				$publishReason[$account_id . '_error'] = '';
			}
			if (!isset($publishReason[$account_id . '_success'])) {
				$publishReason[$account_id . '_success'] = '';
			}
		});
		$order->publish_reason = serialize($publishReason);
		$order->save();
		$assign['un_accept_has_account']       = $hasAccount;
		$assign['un_accept_platform_accounts'] = $platformAccounts;
		$assign['un_accept_publish_reason']    = $publishReason;

		/* 获取已经发单  ----------- */
		/** @type Collection $colPlatformStatus */
		$colPlatformStatus = $order->platformStatus;
		$arrPlatformStatus = [];
		if (!$colPlatformStatus->isEmpty()) {
			$colPlatformStatus->each(function($item) use (& $arrPlatformStatus) {
				$arrPlatformStatus[$item->pt_account_id] = $item;
			});
		}
		$assign['platform_status'] = $arrPlatformStatus;

		$Db = PamAccount::where('account_type', PamAccount::ACCOUNT_TYPE_DESKTOP);

		$account_ids             = PamRoleAccount::where('role_id', site('pt_employee_id'))->lists('account_id');
		$employee_list           = $Db->whereIn('account_id', $account_ids)->get();
		$assign['employee_list'] = $employee_list;

		return view('desktop.platform_employee.publish_employee', $assign);
	}

	/**
	 * 员工接手订单
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postHandle($id)
	{
		PlatformStatus::where('order_id', $id)
			->where('employee_id', \Auth::user()->account_id)
			->update([
				'accepted_at'           => Carbon::now(),
				'employee_order_status' => PlatformOrder::ORDER_STATUS_ING,
			]);
		$platformStatus = PlatformStatus::where('order_id', $id)
			->where('employee_id', \Auth::user()->account_id)
			->first();

		$platformOrder                  = PlatformOrder::find($id);
		$platformOrder->order_status    = PlatformOrder::ORDER_STATUS_ING;
		$platformOrder->accept_id       = $platformStatus->id;
		$platformOrder->accept_platform = PlatformAccount::Employee;
		if ($platformOrder->save()) {
			return AppWeb::resp(AppWeb::SUCCESS, '成功接单', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '接单不成功,请重试', 'reload|1');
		}
	}

	public function postBranchHandle(Request $request)
	{
		$ids = $request->input('id');

		if (!$ids || !is_array($ids)) {
			return AppWeb::resp(AppWeb::ERROR, '您尚未选择订单!');
		}
		foreach ($ids as $id) {
			PlatformStatus::where('order_id', $id)
				->where('employee_id', \Auth::user()->account_id)
				->update([
					'accepted_at'           => Carbon::now(),
					'employee_order_status' => PlatformOrder::ORDER_STATUS_ING,
				]);
			$platformStatus = PlatformStatus::where('order_id', $id)
				->where('employee_id', \Auth::user()->account_id)
				->first();

			$platformOrder                  = PlatformOrder::find($id);
			$platformOrder->order_status    = PlatformOrder::ORDER_STATUS_ING;
			$platformOrder->accept_id       = $platformStatus->id;
			$platformOrder->accept_platform = PlatformAccount::Employee;
			$platformOrder->save();
		}
		return AppWeb::resp(AppWeb::SUCCESS, '成功接单', 'reload|1');
	}

	/**
	 * 提交完成订单
	 * @param $order_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postOver($order_id)
	{
		PlatformStatus::where('order_id', $order_id)
			->where('employee_id', \Auth::user()->account_id)
			->update([
				'employee_order_status' => PlatformOrder::ORDER_STATUS_EXAMINE,
			]);

		$platformOrder               = PlatformOrder::find($order_id);
		$platformOrder->order_status = PlatformOrder::ORDER_STATUS_EXAMINE;
		if ($platformOrder->save()) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单完成提交成功', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '订单完成提交失败', 'reload|1');
		}
	}

	/**
	 * 获取订单详情
	 * @param $order_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getDetail($order_id)
	{
		return view('desktop.platform_employee.detail', [
			'order_id' => $order_id,
		]);
	}

	/**
	 * 获取留言信息
	 * @param $order_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getMessage($order_id)
	{
		$order    = PlatformOrder::with('platformAccept')->findOrFail($order_id);
		$messages = GameLog::where('order_id', $order_id)
			->join('pam_account', 'game_log.account_id', '=', 'pam_account.account_id')
			->select('pam_account.account_name', 'game_log.created_at', 'game_log.log_content')
			->orderBy('created_at', 'desc')
			->get();
		return view('desktop.platform_employee.message', [
			'messages' => $messages,
			'order_id' => $order_id,
			'order'    => $order,
		]);
	}

	/**
	 * 存储留言信息
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postMessage(Request $request)
	{
		$order_id = $request->input('order_id');
		$content  = $request->input('reason');
		$data     = [
			'account_id'  => \Auth::user()->account_id,
			'log_content' => $content,
			'order_id'    => $order_id,
			'log_type'    => GameLog::LOG_TYPE_PROGRESS,
			'parent_id'   => \Auth::user()->account_id,
			'editor_id'   => \Auth::user()->account_id,
			'log_by'      => 'desktop',
		];
		if (GameLog::create($data)) {
			return AppWeb::resp(AppWeb::SUCCESS, '成功提交留言', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '提交留言失败,请重试!', 'reload|1');
		}
	}

	/**
	 * 获取进度图
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getPicShow($id)
	{
		$order    = PlatformOrder::with('platformAccept')->findOrFail($id);
		$pictures = GamePicture::where('order_id', $id)
			->join('pam_account', 'game_picture.account_id', '=', 'pam_account.account_id')
			->select('game_picture.created_at', 'game_picture.pic_desc', 'game_picture.pic_screen', 'pam_account.account_name')
			->orderBy('created_at', 'desc')
			->get();
		return view('desktop.platform_employee.pic_show', [
			'pictures' => $pictures,
			'order_id' => $id,
			'order'    => $order,
		]);
	}

	/**
	 * 获取更新进度页面
	 * @param $order_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getUpdateProgress($order_id)
	{
		return view('desktop.platform_employee.update_progress', [
			'order_id' => $order_id,
		]);
	}

	/**
	 * 更新进度图
	 * @param Request $request
	 * @param         $order_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postUpdateProgress(Request $request, $order_id)
	{
		$pic_screen = $request->input('pic_screen');
		$pic_desc   = $request->input('pic_desc');
		$data       = [
			'order_id'   => $order_id,
			'account_id' => \Auth::user()->account_id,
			'pic_screen' => $pic_screen,
			'pic_desc'   => $pic_desc,
			'pic_type'   => 'soldier_progress',
		];
		if (GamePicture::create($data)) {
			return AppWeb::resp(AppWeb::SUCCESS, '更新进度成功', 'json|1;reload_opener|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '更新失败,请重试!', 'json|1;reload_opener|1');
		}
	}

	/**
	 * 获取提交异常页面
	 * @param $order_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getException($order_id)
	{
		return view('desktop.platform_employee.exception', [
			'order_id' => $order_id,
		]);
	}

	/**
	 * 提交异常
	 * @param Request $request
	 * @param         $order_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postException(Request $request, $order_id)
	{
		$tpl_id   = $request->input('tpl_id');
		$message  = $request->input('message');
		$log_type = PlatformOrder::kvExceptionType($tpl_id);

		PlatformStatus::where('order_id', $order_id)
			->where('employee_id', \Auth::user()->account_id)
			->update([
				'employee_order_status' => PlatformOrder::ORDER_STATUS_EXCEPTION,
			]);

		$message             = '[提交异常] 异常类型:' . $log_type . ', 说明:' . $message;
		$order               = PlatformOrder::find($order_id);
		$order->order_status = 'exception';
		$data                = [
			'order_id'    => $order_id,
			'account_id'  => \Auth::user()->account_id,
			'log_content' => $message,
			'log_type'    => 'exception',
		];
		$game_log            = GameLog::create($data);
		if ($order->save() && $game_log) {
			return AppWeb::resp(AppWeb::SUCCESS, '成功提交异常', 'reload_opener|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '异常提交不成功,请重试!', 'reload_opener|1');
		}
	}

	/**
	 * 取消提交异常
	 * @param $order_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function getCancelException($order_id)
	{
		$message             = '[取消异常]';
		$order               = PlatformOrder::find($order_id);
		$order->order_status = 'ing';
		$data                = [
			'order_id'    => $order_id,
			'account_id'  => \Auth::user()->account_id,
			'log_content' => $message,
			'log_type'    => 'cancel_exception',
		];
		$game_log            = GameLog::create($data);
		if ($order->save() && $game_log) {
			return AppWeb::resp(AppWeb::SUCCESS, '成功取消异常', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '取消异常不成功,请重试!', 'reload|1');
		}
	}

	/**
	 * 确认完单页面
	 * @param $order_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getExamine($order_id)
	{
		return view('desktop.platform_employee.examine', [
			'order_id' => $order_id,
		]);
	}

	/**
	 * 确认完单
	 * @param Request $request
	 * @param         $order_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postExamine(Request $request, $order_id)
	{
		$pic_screen                          = $request->input('pic_screen');
		$pic_desc                            = $request->input('pic_desc');
		$data                                = [
			'order_id'   => $order_id,
			'account_id' => \Auth::user()->account_id,
			'pic_screen' => $pic_screen,
			'pic_desc'   => $pic_desc,
			'pic_type'   => 'soldier_over',
		];
		$order                               = PlatformOrder::find($order_id);
		$order->order_status                 = PlatformOrder::ORDER_STATUS_EXAMINE;
		$order_status                        = PlatformStatus::where('order_id', $order_id)
			->where('platform', 'employee')
			->first();
		$order_status->employee_order_status = PlatformOrder::ORDER_STATUS_EXAMINE;
		if (GamePicture::create($data) && $order->save() && $order_status->save()) {
			return AppWeb::resp(AppWeb::SUCCESS, '成功提交完成订单', 'json|1;reload_opener|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '提交完成订单失败,请重试!', 'json|1;reload_opener|1');
		}
	}

	public function postConfirmOrderOver($order_id, $status_id)
	{
		$order               = PlatformOrder::find($order_id);
		$order->order_status = PlatformOrder::ORDER_STATUS_OVER;

		$order_status                        = PlatformStatus::find($status_id);
		$order_status->employee_order_status = PlatformOrder::ORDER_STATUS_OVER;
		if ($order->save() && $order_status->save()) {
			return AppWeb::resp(AppWeb::SUCCESS, '成功完成订单', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '确认完单失败,请重试!', 'reload|1');
		}

	}

	public function postHandleCancel($order_id)
	{
		$order                = PlatformOrder::find($order_id);
		$order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
		$order->cancel_type   = PlatformOrder::CANCEL_TYPE_PUB_DEAL;
		$order->cancel_status = PlatformOrder::CANCEL_STATUS_DONE;

		$order_status = PlatformStatus::where('order_id', $order_id)
			->where('employee_id', \Auth::user()->account_id)
			->update([
				'employee_order_status'  => PlatformOrder::ORDER_STATUS_CANCEL,
				'employee_cancel_type'   => PlatformOrder::CANCEL_TYPE_PUB_DEAL,
				'employee_cancel_status' => PlatformOrder::CANCEL_STATUS_DONE,
			]);

		$data = [
			'account_id'  => \Auth::user()->account_id,
			'log_content' => '[同意撤单] 同意撤单!',
			'order_id'    => $order_id,
			'log_type'    => GameLog::LOG_TYPE_CANCEL,
			'editor_id'   => \Auth::user()->account_id,
			'log_by'      => 'desktop',
		];

		if (GameLog::create($data) && $order->save() && $order_status) {
			return AppWeb::resp(AppWeb::SUCCESS, '同意撤单申请', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '未能成功提交,请重试!', 'reload|1');
		}
	}

	public function getAssignPc($order_id)
	{
		return view('desktop.platform_employee.assign_pc', [
			'order_id' => $order_id,
		]);
	}

	public function postAssignPc(Request $request, $order_id)
	{
		$message       = trim($request->input('message'));
		$order         = PlatformOrder::find($order_id);
		$order->pc_num = $message;
		if ($order->save()) {
			return AppWeb::resp(AppWeb::SUCCESS, '更新进度成功', 'json|1;reload_opener|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '更新失败,请重试!', 'json|1;reload_opener|1');
		}
	}
}