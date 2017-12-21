<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\PlatformStatus;
use Illuminate\Http\Request;

/**
 * 易代练状态控制器
 * Class PlatformStatusYiController
 * @package App\Http\Controllers\Desktop
 */
class PlatformStatusYiController extends InitController {

	use PlatformTraits;

	/** @type  PlatformStatus */
	private $status;
	/** @type  Yi */
	private $yi;

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}


	/**
	 * 尚未接单的首页
	 * @param $status_id
	 * @return mixed
	 */
	public function getShow($status_id) {
		$this->setStatusId($status_id);
		return view('desktop.platform_status_yi.show');
	}

	/**
	 * 获取详细
	 * @param $status_id
	 * @return mixed
	 */
	public function getDetail($status_id) {
		$this->setStatusId($status_id);
		return view('desktop.platform_status_yi.detail');
	}

	/**
	 * 确认完单
	 * @param $status_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postOver($status_id) {
		$this->setStatusId($status_id);
		if ($this->yi->overOrder()) {
			return AppWeb::resp(AppWeb::SUCCESS, '确认完单!', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	public function postLock(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$note = $request->input('note');
		if ($this->yi->lock($note)) {
			return AppWeb::resp(AppWeb::SUCCESS, '锁定订单!', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	public function postUnLock(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$note = $request->input('note');
		if ($this->yi->unlock($note)) {
			return AppWeb::resp(AppWeb::SUCCESS, '解锁订单!', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	/**
	 * 补时
	 * @param Request $request
	 * @return mixed
	 */
	public function postAddTime(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$hour = $request->input('hour');
		if ($this->yi->addTime($hour)) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单补时!');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	/**
	 * 补款
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postAddMoney(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$money = $request->input('money');
		if ($this->yi->addMoney($money)) {
			return AppWeb::resp(AppWeb::SUCCESS, '订单补款!');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	public function postCancel(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$flag    = $request->input('flag');
		$pub_pay = $request->input('pub_pay');
		$sd_pay  = $request->input('sd_pay');
		$message = $request->input('message');
		if ($this->yi->pubCancel($flag, $pub_pay, $sd_pay, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, Yi::kvPubCancel($flag), 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	/**
	 * 申请客服介入
	 * @param $status_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postKf($status_id) {
		$this->setStatusId($status_id);
		if ($this->yi->kf()) {
			return AppWeb::resp(AppWeb::SUCCESS, '申请客服介入');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	public function postStar(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$flag    = $request->input('flag');
		$message = $request->input('message');
		if ($this->yi->star($flag, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, '评价订单', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	/**
	 * 修改游戏密码
	 * @param Request $request
	 * @return mixed
	 */
	public function postGamePwd(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$pwd = $request->input('game_pwd');
		if ($this->yi->modifyGamePwd($pwd)) {

			// 保存密码, 仅仅有易代练有这个功能
			$this->status->platformOrder->game_pwd = $pwd;
			$this->status->platformOrder->save();

			return AppWeb::resp(AppWeb::SUCCESS, '修改游戏密码!');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
	}

	public function postPublish($status_id) {
		$this->setStatusId($status_id);
		$msg = '';
		if ($this->yi->publish()) {
			$msg .= '发布成功';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		} else {
			$msg .= $this->yi->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
	}

	/**
	 * 删除易代练订单
	 * @param $status_id
	 * @return mixed
	 */
	public function postDelete($status_id) {
		$this->setStatusId($status_id);
		if ($this->yi->delete()) {
			$msg = '删除订单成功!';
		} else {
			$msg = $this->yi->getError();
		}
		return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
	}


	/**
	 * 获取信息/同步
	 * @param $status_id
	 * @return mixed
	 */
	public function getMessage($status_id) {
		$this->setStatusId($status_id);
		$messages = $this->yi->syncMessage();
		if (!$messages) {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}
		return view('desktop.platform_status_yi.message', [
			'messages' => $messages,
		]);
	}

	public function postMessage(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$reason = $request->input('reason');
		if ($this->yi->message($reason)) {
			$msg = '发布成功';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		} else {
			$msg = $this->yi->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
	}

	/**
	 * 当前进度
	 * @param $status_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function getProgress($status_id) {
		$this->setStatusId($status_id);
		$result = $this->yi->syncProgress();
		if ($result) {
			return view('desktop.platform_status_yi.progress', [
				'pictures' => $result,
			]);
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->yi->getError());
		}

	}

	public function getProgressItem($status_id) {
		return view('desktop.platform_status_yi.progress_item', [
			'status_id' => $status_id,
		]);
	}

	/**
	 * 更新进度
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postProgress(Request $request) {
		$status_id = $request->input('status_id');
		$note      = $request->input('message');
		$picture   = \Input::file('picture');
		$this->setStatusId($status_id);
		if ($this->yi->progress($note, $picture)) {
			$msg = '更新订单进度成功!';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		} else {
			$msg = $this->yi->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg);
		}
	}

	/**
	 * 设置状态ID
	 * @param $status_id
	 */
	private function setStatusId($status_id) {
		$this->status = $this->platformStatus($status_id);
		$this->yi     = new Yi($this->status->platformAccount->id, $this->status->platformOrder, $this->pam);
		\View::share([
			'status' => $this->status,
			'order'  => $this->status->platformOrder,
		]);
	}
}
