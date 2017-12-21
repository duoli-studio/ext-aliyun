<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Dailian\Application\Platform\Yq;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\PlatformStatus;
use Illuminate\Http\Request;

/**
 * 代练状态控制器
 * Class PlatformStatusYiController
 * @package App\Http\Controllers\Desktop
 */
class PlatformStatusYqController extends InitController
{

	use PlatformTraits;

	/** @type  PlatformStatus */
	private $status;
	/** @type  Yq */
	private $yq;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * 尚未接单的首页
	 * @param $status_id
	 * @return mixed
	 */
	public function getShow($status_id)
	{
		$this->setStatusId($status_id);
		return view('desktop.platform_status_yq.show');
	}

	/**F
	 * 获取详细
	 * @param $status_id
	 * @return mixed
	 */
	public function getDetail($status_id)
	{
		$this->setStatusId($status_id);
		return view('desktop.platform_status_yq.detail');
	}

	/**
	 * 确认完单
	 * @param $status_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postOver($status_id)
	{
		$this->setStatusId($status_id);
		if ($this->yq->overOrder()) {
			return AppWeb::resp(AppWeb::SUCCESS, '确认完单!', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	/**
	 * 锁定订单
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postLock(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$note = $request->input('note');
		if ($this->yq->lock($note)) {
			return AppWeb::resp(AppWeb::SUCCESS, '锁定订单!', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}


	/**
	 * 解锁订单
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postUnLock(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$note = $request->input('note');
		if ($this->yq->unlock($note)) {
			return AppWeb::resp(AppWeb::SUCCESS, '解锁订单!', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	/**
	 * 补时
	 * @param Request $request
	 * @return mixed
	 */
	public function postAddTime(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$hour = $request->input('time');
		if ($this->yq->addTime($hour)) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单加时!');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	/**
	 * 补款
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postAddMoney(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$money = $request->input('money');
		if ($this->yq->addMoney($money)) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单加价!');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	/**
	 * @param Request $request 上传上来的代练金 赔付费 取消撤销理由
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postCancel(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$flag    = $request->input('flag');
		$pub_pay = $request->input('pub_pay');
		$sd_pay  = $request->input('sd_pay');
		$message = $request->input('message');
		if ($this->yq->pubCancel($flag, $pub_pay, $sd_pay, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, Yq::kvPubCancel($flag), 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}


	/**
	 * 申请客服介入
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postKf(Request $request)
	{
		$status_id = $request->input('status_id');
		$kf        = $request->input('kf');
		$reason    = $request->input('reason');
		$this->setStatusId($status_id);
		if ($this->yq->kf($kf, $reason)) {
			return AppWeb::resp(AppWeb::SUCCESS, yq::kvKf($kf) . '成功', 'reload_opener|workspace');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	public function postStar(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$flag    = $request->input('flag');
		\Log::debug($flag);
		$message = $request->input('message');
		if ($this->yq->star($flag, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, '评价订单', 'reload|1');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	public function postSpecial(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);

		// 类型处理
		$type     = $request->input('type');
		$time     = $request->input('time');
		$money    = $request->input('money');
		$game_pwd = $request->input('game_pwd');
		switch ($type) {
			case yq::MODIFY_TIME:
				if (!$this->yq->modify(Yq::MODIFY_TIME, $time)) {
					return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
				}
				break;
			case yq::MODIFY_MONEY:
				if (!$this->yq->modify(Yq::MODIFY_MONEY, $money)) {
					return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
				}
				break;
			case yq::MODIFY_GAME_WORD:
				if (!$this->yq->modify(Yq::MODIFY_GAME_WORD, $game_pwd)) {
					return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
				}
				break;
		}
		$this->yq->syncDetail();
		return AppWeb::resp(AppWeb::SUCCESS, yq::kvModify($type) . '成功', 'reload|1');
	}


	/**
	 * 修改游戏密码
	 * @param Request $request
	 * @return mixed
	 */
	public function postGamePwd(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$pwd = $request->input('game_pwd');
		if ($this->yq->modifyGamePwd($pwd)) {
			// 保存密码, 仅仅有易代练有这个功能
			$this->status->platformOrder->game_pwd = $pwd;
			$this->status->platformOrder->save();

			return AppWeb::resp(AppWeb::SUCCESS, '修改游戏密码!');
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	public function postPublish($status_id)
	{
		$this->setStatusId($status_id);
		$msg = '';
		if ($this->yq->publish()) {
			$msg .= '发布成功';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		}
		else {
			$msg .= $this->yq->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
	}

	/**
	 * 删除代练订单
	 * @param $status_id
	 * @return mixed
	 */
	public function postDelete($status_id)
	{
		$this->setStatusId($status_id);
		if ($this->yq->delete()) {
			$msg = '删除订单成功!';
		}
		else {
			$msg = $this->yq->getError();
		}
		return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
	}

	/**
	 * 获取信息/同步
	 * @param $status_id
	 * @return mixed
	 */
	public function getMessage($status_id)
	{
		$this->setStatusId($status_id);
		$messages = $this->yq->syncMessage();
		if (!$messages) {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
		return view('desktop.platform_status_yq.message', [
			'messages' => $messages,
		]);
	}

	public function postMessage(Request $request)
	{
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$reason = $request->input('reason');
		if ($this->yq->message($reason)) {
			$msg = '发布成功';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		}
		else {
			$msg = $this->yq->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
	}

	/**
	 * 当前进度
	 * @param $status_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function getProgress($status_id)
	{
		$this->setStatusId($status_id);
		$result = $this->yq->syncProgress();
		if ($result) {
			return view('desktop.platform_status_yq.progress', [
				'pictures' => $result,
			]);
		}
		else {
			return AppWeb::resp(AppWeb::ERROR, $this->yq->getError());
		}
	}

	public function getProgressItem($status_id)
	{
		return view('desktop.platform_status_yq.progress_item', [
			'status_id' => $status_id,
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
		$note      = $request->input('message');
		$picture   = \Input::file('picture');

		$this->setStatusId($status_id);
		if ($this->yq->progress($note, $picture)) {
			$msg = '更新订单进度成功!';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		}
		else {
			$msg = $this->yq->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg);
		}
	}

	/**
	 * 设置状态ID
	 * @param $status_id
	 */
	private function setStatusId($status_id)
	{
		$this->status = $this->platformStatus($status_id);
		$this->yq     = new Yq($this->status->platformAccount->id, $this->status->platformOrder, $this->pam);
		\View::share([
			'status' => $this->status,
			'order'  => $this->status->platformOrder,
		]);
	}
}

