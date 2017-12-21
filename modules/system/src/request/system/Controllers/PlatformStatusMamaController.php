<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Mao;
use App\Lemon\Dailian\Application\Platform\Traits\BaseTraits;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\PlatformMaoPic;
use App\Models\PlatformStatus;
use Illuminate\Http\Request;

/**
 * 代练妈妈状态控制器
 * Class PlatformStatusMaoController
 * @package App\Http\Controllers\Desktop
 */
class PlatformStatusMamaController extends InitController {

	use PlatformTraits;
	use BaseTraits;

	/** @type  PlatformStatus */
	private $status;
	/** @type  Mama */
	private $mama;

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
		return view('desktop.platform_status_mama.show');
	}


	public function getDetail($status_id) {
		$this->setStatusId($status_id);
		return view('desktop.platform_status_mama.detail');
	}

	public function postPublish($status_id) {
		$status = $this->platformStatus($status_id);
		$msg    = '';
		$mama   = new Mao($status->platformAccount->id, $status->platformOrder, $this->pam);
		if ($mama->publish()) {
			$msg .= '发布成功';
			return AppWeb::resp(AppWeb::SUCCESS, $msg, 'reload|1');
		} else {
			$msg .= $mama->getError();
			return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
		}
	}

	/**
	 * 删除代练妈妈订单
	 * @param $status_id
	 * @return mixed
	 */
	public function postDelete($status_id) {
		$status = $this->platformStatus($status_id);
		$mama   = new Mama($status->platformAccount->id, $status->platformOrder, $this->pam);
		if ($mama->delete()) {
			$msg = '删除订单成功!';
		} else {
			$msg = $mama->getError();
		}
		return AppWeb::resp(AppWeb::ERROR, $msg, 'reload|1');
	}

	/**
	 * 留言
	 * @param $status_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getMessage($status_id) {
		$this->setStatusId($status_id);
		$items    = $this->mama->messageList();
		$data     = $items['data']['list'];
		$begin_id = $items['data']['beginid'];
		return view('desktop.platform_status_mama.message', [
			'messages' => $data,
			'begin_id' => $begin_id,
		]);
	}
	public function getMessageNext(Request $request) {
		$status_id = $request->input('status_id');
		$begin_id  = $request->input('begin_id');
			$this->setStatusId($status_id);
			$items    = $this->mama->messageList($begin_id);
			$data     = $items['data']['list'];
			$begin_id = $items['data']['beginid'];
			return view('desktop.platform_status_mama.message', [
				'messages' => $data,
				'begin_id' => $begin_id,
			]);

	}

	public function postMessage(Request $request) {
		$status_id = $request->input('status_id');
		$begin_id  = $request->input('begin_id');
		if ($begin_id) {
			$this->setStatusId($status_id);
			$items    = $this->mama->messageList($begin_id);
			$data     = $items['data']['list'];
			$begin_id = $items['data']['beginid'];
			return view('desktop.platform_status_mama.message', [
				'messages' => $data,
				'begin_id' => $begin_id,
			]);
		}
		$this->setStatusId($status_id);
		$content = $request->input('reason');
		$this->mama->message($content);
		$this->mama->syncMessage();
		return AppWeb::resp(AppWeb::SUCCESS, '发布信息成功', 'reload|1');
	}

	public function postProgress(Request $request) {
		$message = $request->input('message');
		if (!$message) {
			return AppWeb::resp(AppWeb::ERROR, '请输入信息!');
		}
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$file = \Input::file('picture');
		if ($this->mama->progress($message, $file)) {
			return AppWeb::resp(AppWeb::SUCCESS, '操作成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
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
		if ($this->mama->lock($lock, $reason)) {
			return AppWeb::resp(AppWeb::SUCCESS, Mama::kvLock($lock) . '成功', 'reload_opener|workspace');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
		}
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
			case Mao::MODIFY_TIME:
				if (!$this->mama->modify(Mao::MAO_ADD_TIME, $time)) {
					return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
				}
				break;
			case Mao::MODIFY_MONEY:
				if (!$this->mama->modify(Mao::MAO_ADD_MONEY, $money)) {
					return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
				}
				break;
			case Mao::MODIFY_GAME_WORD:
				if (!$this->mama->modify(Mao::MAO_CHANGE_PWD, $game_pwd)) {
					return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
				}
				break;
		}
		$this->mama->syncDetail();
		return AppWeb::resp(AppWeb::SUCCESS, Mao::kvModify($type) . '成功', 'reload|1');
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
		if ($this->mama->star($flag, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, '评价完成', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
		}
	}

	/**
	 * 发单者申请撤销
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
		if ($this->mama->pubCancel($flag, $pub_pay, $sd_pay, $message)) {
			return AppWeb::resp(AppWeb::SUCCESS, Mao::kvPubCancel($flag) . '操作成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
		}
	}

	/**
	 * 取消撤销申请
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postCancelCancel(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$flag = $request->input('flag');
		if ($this->mama->pubCancel($flag, 0, 0)) {
			return AppWeb::resp(AppWeb::SUCCESS, Mama::kvPubCancel($flag) . '操作成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
		}
	}

	/**
	 * 同意接单者撤销订单的申请
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postCancelAgree(Request $request) {
		$status_id = $request->input('status_id');
		$this->setStatusId($status_id);
		$message = $request->input('message');  //同意撤销说明
		$flag    = $request->input('flag');
		$pub_pay = $request->input('pub_pay');  //支付代练费用
		$sd_pay  = $request->input('sd_pay');   //赔偿保证金
		if ($this->mama->pubCancel($flag, $message, $pub_pay, $sd_pay)) {
			return AppWeb::resp(AppWeb::SUCCESS, Mao::kvPubCancel($flag) . '操作成功', 'reload|1');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
		}
	}

	/**
	 * 申请客服介入
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postKf(Request $request) {
		$status_id = $request->input('status_id');
		$kf        = $request->input('kf');
		$reason    = $request->input('reason');
		$this->setStatusId($status_id);
		if ($this->mama->kf($kf, $reason)) {
			return AppWeb::resp(AppWeb::SUCCESS, mama::kvKf($kf) . '成功', 'reload_opener|workspace');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
		}
	}

	/**
	 * 确认完单
	 * @param $status_id
	 * @return mixed
	 */
	public function postOver($status_id) {
		$this->setStatusId($status_id);
		if ($this->mama->overOrder()) {
			return AppWeb::resp(AppWeb::SUCCESS, '确认完单!');
		} else {
			return AppWeb::resp(AppWeb::ERROR, $this->mama->getError());
		}
	}


	public function getPicShow($status_id) {
		$this->setStatusId($status_id);
		$this->mama->syncPic();
		$messages = PlatformMaoPic::take(200)->where('order_no', $this->status->mama_order_no)->orderBy('created_at', 'desc')->get();
		return view('desktop.platform_status_mama.pic_show', [
			'message' => $messages,
		]);
	}

	/**
	 * 设置状态ID
	 * @param $status_id
	 */
	private function setStatusId($status_id) {
		$this->status = $this->platformStatus($status_id);
		$this->mama   = new Mama($this->status->platformAccount->id, $this->status->platformOrder, $this->pam);
		\View::share([
			'account' => $this->status->platformAccount,
			'order'   => $this->status->platformOrder,
			'status'  => $this->status,
		]);
	}
}
