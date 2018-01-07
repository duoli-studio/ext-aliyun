<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Dailian\Application\Platform\Tong;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\PlatformStatus;
use App\Models\PlatformTongLog;
use Illuminate\Http\Request;

/**
 * 代练通状态控制器
 * Class PlatformStatusTongController
 * @package App\Http\Controllers\Desktop
 */
class PlatformStatusTongController extends InitController {

	use PlatformTraits;

	/** @type  PlatformStatus */
	private $status;
	/** @type  Tong */
	private $tong;

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
		return view('desktop.platform_status_tong.show');
	}

	/**
	 * 发布订单成功
	 * @param $status_id
	 * @return mixed
	 */
	public function postPublish($status_id) {
		$this->setStatusId($status_id);
		$msg = '';
		if ($this->tong->publish()) {
			$msg .= '发布成功';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		} else {
			$msg .= $this->tong->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
	}


	/**
	 * 锁定/解锁
	 * @param Request $request
	 * @return mixed
	 */
	public function postLock(Request $request) {
		$status_id = $request->input('status_id');
		$lock      = $request->input('lock');
		$reason    = $request->input('reason');
		$this->setStatusId($status_id);
		if ($this->tong->lock($lock, $reason)) {
			return AppWeb::resp(AppWeb::SUCCESS, Tong::kvLock($lock) . '成功', 'reload_opener|workspace');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
		}
	}

	/**
	 * 获取信息/同步
	 * @param $status_id
	 * @return mixed
	 */
	public function getMessage($status_id) {
		$this->setStatusId($status_id);
		$this->tong->syncMessage();
		$messages = PlatformTongLog::take(200)->where('order_id', $this->status->order_id)->orderBy('created_at', 'desc')->get();
		return view('desktop.platform_status_tong.message', [
			'messages' => $messages,
		]);
	}

	/**
	 * 发布消息
	 * @param Request $request
	 * @return mixed
	 */
	public function postMessage(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$content = $request->input('reason');
		$this->tong->message($content);
		$this->tong->syncMessage();
		return AppWeb::resp(AppWeb::SUCCESS, '发布信息成功', 'reload|1');
	}

	/**
	 * 删除订单
	 * @param $status_id
	 * @return mixed
	 */
	public function postDelete($status_id) {
		$this->setStatusId($status_id);
		if ($this->tong->delete()) {
			return AppWeb::resp(AppWeb::SUCCESS, '删除订单成功!', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->tong->getError(), 'reload|1');
		}
	}

	/**
	 * 获取详细
	 * @param $status_id
	 * @return mixed
	 */
	public function getDetail($status_id) {
		$this->setStatusId($status_id);
		return view('desktop.platform_status_tong.detail');
	}

	public function postSpecial(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);

		// 类型处理
		$type     = $request->input('type');
		$time     = $request->input('time');
		$money    = $request->input('money');
		$game_pwd = $request->input('game_pwd');
		switch ($type) {
			case Tong::MODIFY_TIME:
				if (!$this->tong->modify(Tong::MODIFY_TIME, $time)) {
					return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
				}
				break;
			case Tong::MODIFY_MONEY:
				if (!$this->tong->modify(Tong::MODIFY_MONEY, $money)) {
					return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
				}
				break;
			case Tong::MODIFY_GAME_WORD:
				if (!$this->tong->modify(Tong::MODIFY_GAME_WORD, $game_pwd)) {
					return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
				}
				break;
		}
		$this->tong->syncDetail();
		return AppWeb::resp(AppWeb::SUCCESS, Tong::kvModify($type) . '成功', 'reload|1');
	}

	/**
	 * 更新进度
	 * @param Request $request
	 * @return mixed
	 */
	public function postProgress(Request $request) {
		$message = $request->input('message');
		if (!$message) {
			return AppWeb::resp(AppWeb::ERROR, '请输入信息!');
		}
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);

		$file = \Input::file('picture');
		if ($this->tong->progress($message, $file)) {
			return AppWeb::resp(AppWeb::SUCCESS, '操作成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
		}
	}

	/**
	 * 加入撤销
	 * @param Request $request
	 * @return mixed
	 */
	public function postCancel(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$message = $request->input('message');
		$flag    = $request->input('flag');
		$pub_pay = $request->input('pub_pay');
		$sd_pay  = $request->input('sd_pay');
		if ($this->tong->pubCancel($flag, $pub_pay, $sd_pay, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, Tong::kvPubCancel($flag) . '操作成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
		}
	}


	/**
	 * 申请客服介入
	 * @param         $status_id
	 * @return mixed
	 */
	public function postKf($status_id) {
		$this->setStatusId($status_id);
		if ($this->tong->kf()) {
			return AppWeb::resp(AppWeb::SUCCESS, '申请客服介入成功');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
		}
	}

	/**
	 * 确认完单
	 * @param $status_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postOver($status_id) {
		$this->setStatusId($status_id);
		if ($this->tong->overOrder()) {
			return AppWeb::resp(AppWeb::SUCCESS, '确认完单!', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
		}
	}


	/**
	 * 评价
	 * @param Request $request
	 * @return mixed
	 */
	public function postStar(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$flag    = $request->input('flag');
		$message = $request->input('message');
		if ($this->tong->star($flag, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, '评价完成', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->tong->getError());
		}
	}

	/**
	 * 设置状态ID
	 * @param $status_id
	 */
	private function setStatusId($status_id) {
		$this->status = $this->platformStatus($status_id);
		$this->tong   = new Tong($this->status->platformAccount->id, $this->status->platformOrder, $this->pam);
		\View::share([
			'account'      => $this->status->platformAccount,
			'order'        => $this->status->platformOrder,
			'status'       => $this->status,
			'snapshot_url' => $this->tong->getSnapshotUrl(),
		]);
		\Cw::debug($this->status->platformOrder);
		\Cw::debug($this->status);
	}
}

