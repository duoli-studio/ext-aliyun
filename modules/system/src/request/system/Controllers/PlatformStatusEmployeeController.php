<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\GameLog;
use App\Models\GamePicture;
use App\Models\PlatformLog;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use Illuminate\Http\Request;

/**
 * 员工订单状态控制器
 * Class PlatformStatusEmployeeController
 * @package App\Http\Controllers\Desktop
 */
class PlatformStatusEmployeeController extends InitController
{

	use PlatformTraits;

	/** @type  PlatformStatus */
	private $status;
	/** @type  Yi */
	private $yi;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * 获取详细
	 * @param $order_id
	 * @return mixed
	 */
	public function getDetail($order_id)
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
		return view('desktop.platform_status_employee.detail', $data);
	}

	/**
	 * 申请订单撤销
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postCancel(Request $request)
	{
		$status_id = $request->input('status_id');
		$message   = $request->input('message');

		$order_status                         = PlatformStatus::find($status_id);
		$order_id                             = $order_status->order_id;
		$order_status->employee_order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
		$order_status->employee_cancel_type   = PlatformOrder::CANCEL_TYPE_PUB_DEAL;
		$order_status->employee_cancel_status = PlatformOrder::CANCEL_STATUS_ING;

		$order                = PlatformOrder::find($order_id);
		$order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
		$order->cancel_type   = PlatformOrder::CANCEL_TYPE_PUB_DEAL;
		$order->cancel_status = PlatformOrder::CANCEL_STATUS_ING;

		$data = [
			'account_id'  => \Auth::user()->account_id,
			'log_content' => '[申请撤单] 发单者申请撤单! [理由]:' . $message,
			'order_id'    => $order_id,
			'log_type'    => GameLog::LOG_TYPE_PUB_CANCEL,
			'editor_id'   => \Auth::user()->account_id,
			'log_by'      => 'desktop',
		];

		if ($order_status->save() && GameLog::create($data) && $order->save()) {
			return AppWeb::resp(AppWeb::SUCCESS, '撤单成功!', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '撤单失败,请重试!', 'reload|1');
		}
	}

	/**
	 * 取消订单撤销
	 * @param $order_id
	 * @param $status_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postCancelCancel($order_id, $status_id)
	{
		$order                = PlatformOrder::find($order_id);
		$order->order_status  = PlatformOrder::ORDER_STATUS_ING;
		$order->cancel_type   = PlatformOrder::CANCEL_TYPE_NONE;
		$order->cancel_status = PlatformOrder::CANCEL_STATUS_NONE;
		$order->save();

		$order_status                         = PlatformStatus::find($status_id);
		$order_status->employee_order_status  = PlatformOrder::ORDER_STATUS_ING;
		$order_status->employee_cancel_type   = PlatformOrder::CANCEL_TYPE_NONE;
		$order_status->employee_cancel_status = PlatformOrder::CANCEL_STATUS_NONE;
		$order_status->save();

		$data = [
			'account_id'  => \Auth::user()->account_id,
			'log_content' => '[取消退单] 取消退单!',
			'order_id'    => $order_id,
			'log_type'    => GameLog::LOG_TYPE_PUB_CANCEL,
			'editor_id'   => \Auth::user()->account_id,
			'log_by'      => 'desktop',
		];
		if (GameLog::create($data)) {
			return AppWeb::resp(AppWeb::SUCCESS, '取消撤单成功!', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '取消撤单失败!', 'reload|1');
		}

	}

	/**
	 * 修改游戏密码
	 * @param Request $request
	 * @return mixed
	 */
	public function postGamePwd(Request $request)
	{
		$order_id     = $request->input('order_id');
		$pwd          = $request->input('game_pwd');
		$game_account = $request->input('game_account');
		if (!$order_id) {
			return AppWeb::resp(AppWeb::ERROR, '修改账号密码出错了,请重试');
		}
		$order = PlatformOrder::find($order_id);
		if ($game_account) {
			$order->game_account = $game_account;
		}
		if ($pwd) {
			$order->game_pwd = $pwd;
		}

		if ($order->save()) {
			return AppWeb::resp(AppWeb::SUCCESS, '修改游戏密码!', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, '修改游戏密码失败,请重试!', 'reload|1');
		}
	}

	/**
	 * 删除易代练订单
	 * @param $status_id
	 * @return mixed
	 */
	public function postDelete($status_id)
	{
		$this->setStatusId($status_id);
		if ($this->yi->delete()) {
			$msg = '删除订单成功!';
		}
		else {
			$msg = $this->yi->getError();
		}
		return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
	}


	/**
	 * 获取信息/同步
	 * @param $order_id
	 * @return mixed
	 */
	public function getMessage($order_id)
	{
		$order    = PlatformOrder::with('platformAccept')->findOrFail($order_id);
		$messages = GameLog::where('order_id', $order_id)
			->join('pam_account', 'game_log.account_id', '=', 'pam_account.account_id')
			->select('pam_account.account_name', 'game_log.created_at', 'game_log.log_content')
			->orderBy('created_at', 'desc')
			->get();
		return view('desktop.platform_status_employee.message', [
			'messages' => $messages,
			'order_id' => $order_id,
			'order'    => $order,
		]);
	}

	/**
	 * 发布留言
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
	 * 当前进度图
	 * @param $order_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getProgress($order_id)
	{
		$order    = PlatformOrder::with('platformAccept')->findOrFail($order_id);
		$pictures = GamePicture::where('order_id', $order_id)
			->join('pam_account', 'game_picture.account_id', '=', 'pam_account.account_id')
			->select('game_picture.created_at', 'game_picture.pic_desc', 'game_picture.pic_screen', 'pam_account.account_name')
			->orderBy('created_at', 'desc')
			->get();
		return view('desktop.platform_status_employee.progress', [
			'pictures' => $pictures,
			'order_id' => $order_id,
			'order'    => $order,
		]);
	}

	/**
	 * 更新进度
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postProgress(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$note    = $request->input('message');
		$picture = $request->input('picture');
		if ($this->yi->progress($note, $picture)) {
			$msg = '更新订单进度成功!';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		}
		else {
			$msg = $this->yi->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
	}

	/**
	 * 设置状态ID
	 * @param $status_id
	 */
	private function setStatusId($status_id)
	{
		$this->status = $this->platformStatus($status_id);
		$this->yi     = new Yi($this->status->platformAccount->id, $this->status->platformOrder, $this->pam);
		\View::share([
			'status' => $this->status,
			'order'  => $this->status->platformOrder,
		]);
	}
}
