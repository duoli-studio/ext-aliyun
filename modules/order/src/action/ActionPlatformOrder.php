<?php namespace Order\Action;

use App\Http\Controllers\Support\TgpController;
use App\Lemon\Dailian\Application\Platform\Factory\PlatformFactory;
use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Mao;
use App\Lemon\Dailian\Application\Platform\Tong;
use App\Lemon\Dailian\Application\Platform\Yi;
use App\Lemon\Dailian\Application\Platform\Baozi;
use App\Lemon\Dailian\Application\Platform\Yq;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\AccountDesktop;
use App\Models\PamAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformLog;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ActionPlatformOrder extends ActionBasic
{

	use PlatformTraits;

	/** @type PlatformOrder 订单 */
	private $order;

	private $reqLog;

	public function __construct($id = null)
	{
		$this->setOrder($id);
	}


	/**
	 * 创建并发布订单
	 * @param $input
	 * @param $desktop AccountDesktop
	 * @return bool
	 */
	public function create($input, $desktop)
	{
		$validator = \Validator::make($input, [
			'order_title'           => 'required',
			'server_id'             => 'required',
			'order_get_in_price'    => 'required',
			'order_get_in_number'   => 'required',
			'game_actor'            => 'required',
			'game_account'          => 'required',
			'game_pwd'              => 'required',
			'order_get_in_wangwang' => 'required',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		// 验证淘宝订单号
		$getInNumberExists = PlatformOrder::where('order_get_in_number', $input['order_get_in_number'])
			->whereNotIn('order_status', [PlatformOrder::ORDER_STATUS_CANCEL, PlatformOrder::ORDER_STATUS_DELETE])->exists();
		if ($getInNumberExists) {
			return $this->setError('淘宝订单号重复');
		}

		\DB::transaction(function () use ($input, $desktop) {
			$input['publish_id']           = $desktop->account_id;
			$input['publish_account_name'] = $desktop->pam->account_name;
			$input['order_status']         = PlatformOrder::ORDER_STATUS_CREATE;
			// create

			$order = PlatformOrder::create($input);
			// progress log
			$log = '[订单创建] 订单ID: ' . $order->order_id . ', 录入账号: ' . $desktop->pam->account_name;
			PlatformLog::record($order, $desktop->account_id, $log);

			event('platform.order_create', [$order]);
		});
		return true;
	}


	/**
	 * 编辑订单
	 * @param $input
	 * @param $desktop AccountDesktop
	 * @return bool
	 */
	public function edit($input, $desktop)
	{
		try {
			\DB::transaction(function () use ($input, $desktop) {
				$old = clone $this->order;


				$this->order->fill($input);

				if (!$this->order->isDirty()) {
					throw new \Exception('订单信息未改动');
				}

				// price
				$this->order->update($input);

				// progress log
				$log        = '[订单更新] 订单号: ' . $this->order->order_id;
				$compare    = PlatformOrder::compareOrderMoney($old, $this->order);
				$compareStr = '';
				if ($compare) {
					foreach ($compare as $p) {
						$compareStr .= $p['desc'] . ' , ';
					}
					$compareStr = rtrim($compareStr, ' , ');
				}

				PlatformLog::record($this->order, $desktop->account_id, $log . ' ' . $compareStr);

				event('platform.order_change', [$old, $this->order]);
			});
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}

		return true;
	}

	/**
	 * 标记为退款订单
	 * @param AccountDesktop $desktop
	 * @return bool
	 */
	public function refund($desktop)
	{
		\DB::transaction(function () use ($desktop) {
			$this->order->order_status = PlatformOrder::ORDER_STATUS_REFUND;
			$this->order->refund_at    = Carbon::now();
			$this->order->save();
			PlatformLog::record($this->order, $desktop->account_id, '操作退款');
		});
		return true;
	}

	/**
	 * 订单重新发布
	 * @param PamAccount $desktop
	 * @return bool
	 */
	public function orderRePublish($desktop)
	{
		$order = $this->order;
		\DB::transaction(function () use ($order, $desktop) {
			// 创建订单
			$data = [
				'order_note'            => $order->order_note,
				'order_safe_money'      => $order->order_safe_money,
				'order_speed_money'     => $order->order_speed_money,
				'order_price'           => $order->order_price,
				'order_get_in_wangwang' => $order->order_get_in_wangwang,
				'order_get_in_mobile'   => $order->order_get_in_mobile,
				'order_get_in_number'   => $order->order_get_in_number,
				'source_id'             => $order->source_id,
				'order_get_in_price'    => $order->order_get_in_price,
				'game_actor'            => $order->game_actor,
				'game_pwd'              => $order->game_pwd,
				'game_account'          => $order->game_account,
				'order_content'         => $order->order_content,
				'order_current'         => $order->order_current,
				'type_id'               => $order->type_id,
				'server_id'             => $order->server_id,
				'game_id'               => $order->game_id,
				'order_title'           => $order->order_title,
				'game_area'             => $order->game_area,
				'source_title'          => $order->source_title,
				'server_big_title'      => $order->server_big_title,
				'type_title'            => $order->type_title,
				'order_tags'            => $order->order_tags,
				'order_hours'           => $order->order_hours,
				'is_urgency'            => $order->is_urgency,
				'created_at'            => $order->created_at,
				'updated_at'            => Carbon::now(),
			];


			$order = PlatformOrder::create($data);

			// 处理关联订单ID
			$relOrderId    = $this->order->rel_order_id . ',' . $order->order_id . ',' . $this->order->order_id;
			$arrRelOrderId = new Collection(explode(',', $relOrderId));
			$arrRelOrderId->unique();
			$filtered = $arrRelOrderId->filter(function ($item) {
				return trim($item);
			});
			$filtered->sort();
			$relOrderId = implode(',', $filtered->toArray());

			// 更新关联订单信息和创建信息
			$order->created_at   = $this->order->created_at;
			$order->is_re_order  = 1;
			$order->rel_order_id = $relOrderId;
			$order->save();

			// 标识当前订单状态为已经重新发布
			// 更新关联订单
			$this->order->is_re_publish = 1;
			$this->order->rel_order_id  = $relOrderId;
			$this->order->save();

			PlatformLog::record($order, $desktop->account_id, '订单重新发布');
		});
		return true;
	}


	/**
	 * 设置订单颜色
	 * @param $color
	 * @return bool
	 */
	public function setColor($color)
	{
		$this->order->order_color = $color;
		$this->order->save();
		return true;
	}


	/**
	 * 留言, 不开放管理员评价
	 * @param $message
	 * @param $editor_id
	 * @return bool
	 */
	public function talk($message, $editor_id)
	{
		$validator = \Validator::make([
			'content' => $message,
		], [
			'content' => 'required',
		], [
			'content.required' => '请填写内容',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}
		PlatformLog::record($this->order, $editor_id, $message);
		return true;
	}


	/**
	 * 常规完成并付款
	 * @param $order_id
	 * @param $account_id
	 * @return bool
	 */
	public function over($order_id, $account_id)
	{
		\DB::transaction(function () use ($order_id, $account_id) {
			$order     = PlatformOrder::find($order_id);
			$old_order = clone $order;
			// 更新订单状态
			$order->ended_at     = Carbon::now();
			$order->order_status = PlatformOrder::ORDER_STATUS_OVER;

			// 计算代练金赚取
			$order->fee_earn = PlatformOrder::calcFeeEarn($order);
			$order->save();

			// log
			PlatformLog::record($order, $account_id, '[订单完成]审核并支付代练金!');

			// 完成订单, 发送短信
			$this->sendMessage($old_order, $order);
		});
		return true;
	}

	/**
	 * 锁定订单
	 * @param $order_id
	 * @param $reason
	 * @param $account_id
	 * @return bool
	 */
	public function lock($order_id, $reason, $account_id)
	{
		$order = PlatformOrder::find($order_id);
		$order->update([
			'order_lock' => PlatformOrder::ORDER_LOCK_LOCK,
			'locked_at'  => Carbon::now(),
		]);
		$lockLog = '[锁定订单] ' . $reason;
		PlatformLog::record($order, $account_id, $lockLog);

		return true;
	}


	/**
	 * 解除锁定
	 * @param $order_id
	 * @param $reason
	 * @param $account_id
	 */
	public function unlock($order_id, $reason, $account_id)
	{
		$order = PlatformOrder::find($order_id);
		$order->update([
			'order_lock'  => PlatformOrder::ORDER_LOCK_UNLOCK,
			'unlocked_at' => Carbon::now(),
		]);
		$unlockLog = '[解除订单锁定] ' . $reason;
		PlatformLog::record($order, $account_id, $unlockLog);

	}


	/**
	 * 申请客服介入
	 * @param $order_id
	 * @param $editor_id
	 */
	public function kf($order_id, $editor_id)
	{
		$order = PlatformOrder::find($order_id);
		$log   = '[申请客服介入] ';
		if ($order->publish_id == $editor_id) {
			$kfApplyBy = PlatformOrder::KF_APPLY_BY_PUB;
			$log       .= '发单者申请客服介入!';
		}
		else {
			$kfApplyBy = PlatformOrder::KF_APPLY_BY_SD;
			$log       .= '接单者申请客服介入!';
		}
		$order = PlatformOrder::find($order_id);
		$order->update([
			'cancel_type'   => PlatformOrder::CANCEL_TYPE_KF,
			'kf_status'     => PlatformOrder::KF_STATUS_WAIT,
			'kf_apply_by'   => $kfApplyBy,
			'kf_applied_at' => Carbon::now(),
		]);
		PlatformLog::record($order, $editor_id, $log);
	}

	/**
	 * 取消客服介入
	 * @param $order_id
	 * @param $editor_id
	 */
	public function unkf($order_id, $editor_id)
	{
		$order = PlatformOrder::find($order_id);
		$log   = '[取消客服介入] ';
		$order->update([
			'cancel_type'   => PlatformOrder::CANCEL_TYPE_NONE,
			'kf_status'     => PlatformOrder::KF_STATUS_NONE,
			'kf_apply_by'   => 'none',
			'kf_applied_at' => Carbon::now(),
		]);
		PlatformLog::record($order, $editor_id, $log);
	}


	/**
	 * @param                $question_type
	 * @param                $handle_account_id
	 * @param                $description
	 * @param string         $picture
	 * @param AccountDesktop $desktop
	 * @return bool
	 */
	public function question($question_type, $handle_account_id, $description, $picture = '', $desktop)
	{
		// 设置基础的order
		if ($this->order->is_question) {
			return $this->setError('当前有问题待处理, 请处理后在进行提交');
		}
		$input = [
			'question_type'              => $question_type,
			'question_handle_account_id' => $handle_account_id,
			'question_account_id'        => $desktop->account_id,
			'question_description'       => $description,
			'question_thumb'             => $picture,
		];

		$validator = \Validator::make($input, [
			'question_type'        => 'required',
			'question_description' => 'required',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}
		$this->order->is_question                = 1;
		$this->order->question_type              = $question_type;
		$this->order->question_handle_account_id = $handle_account_id;
		$this->order->question_account_id        = $desktop->account_id;
		$this->order->question_description       = $description;
		$this->order->question_thumb             = $picture;
		$this->order->question_status            = PlatformOrder::QUESTION_STATUS_CREATE;
		$this->order->question_at                = Carbon::now();
		$this->order->save();

		$log = '[提交问题]' . $desktop->pam->account_name . '提交 `' . $question_type . '`, 详细: ' . $description;

		PlatformLog::record($this->order, $desktop->account_id, $log);
		return true;
	}

	/**
	 * 关闭问题
	 * @param $question_status
	 * @param $message
	 * @param $desktop AccountDesktop
	 * @return bool
	 */
	public function handleQuestion($question_status, $message, $desktop)
	{
		if (!$this->order->is_question) {
			return $this->setError('当前订单无问题, 不需要关闭');
		}

		// 确认问题处理
		if ($question_status == PlatformOrder::QUESTION_STATUS_DONE) {
			$this->order->is_question = 0;
			$log                      = '[关闭问题] : 问题已经关闭!' . $message;
			PlatformLog::record($this->order, $desktop->account_id, $log);
		}
		else {
			$this->order->question_status = $question_status;

			// 订单状态留言
			$log = '[处理问题] : ' . PlatformOrder::kvQuestionStatus($question_status) . ' ,' . $message;
			$this->talk($log, $desktop->account_id);
		}
		$this->order->save();
		return true;
	}


	/**
	 * 删除发单
	 * @param $desktop AccountDesktop
	 * @return bool
	 */
	public function destroy($desktop)
	{
		\DB::transaction(function () use ($desktop) {
			// log
			PlatformLog::record($this->order, $desktop->account_id, '[撤单] 用户删除订单');
			$this->order->update([
				'order_status' => PlatformOrder::ORDER_STATUS_DELETE,
				'deleted_at'   => Carbon::now(),
			]);
		});
		return true;
	}

	/**
	 * 同步接单
	 * @return bool
	 */
	public function syncAccept()
	{
		/** @type Collection $accept */
		$accept = PlatformStatus::where(function (Builder $query) {
			$query->orWhere('tong_is_accept', 1);
			$query->orWhere('mao_is_accept', 1);
			$query->orWhere('yi_is_accept', 1);
			$query->orWhere('baozi_is_accept', 1);
			$query->orWhere('mama_is_accept', 1);
			$query->orWhere('yq_is_accept', 1);
		})->where('order_id', $this->order->order_id)
			->orderBy('accepted_at', 'asc')
			->get();

		// over
		if ($this->order->order_status == PlatformOrder::ORDER_STATUS_CREATE) {
			return false;
		}
		if ($this->order->accept_id) {
			return false;
		}
		if (count($accept) == 0) {
			return false;
		}
		else {
			if (!$this->order->accept_id) {
				// 接单的资金操作
				event('platform.order_handle', [$this->order]);
			}


			/* 第一个  ----------- */
			/** @type PlatformStatus $first */
			$first                                   = $accept->shift();
			$this->order->accept_id                  = $first->id;
			$this->order->accept_platform_account_id = $first->pt_account_id;
			$this->order->accept_platform            = $first->platform;
			$this->order->pt_accept_sync_at          = Carbon::now();

			// 同步接单时间到主订单
			if ($first->platform == PlatformAccount::PLATFORM_YI) {
				$this->order->assigned_at = $first->yi_assigned_at;
			}
			if ($first->platform == PlatformAccount::PLATFORM_BAOZI) {
				$this->order->assigned_at = $first->baozi_assigned_at;
			}
			if ($first->platform == PlatformAccount::PLATFORM_YQ) {
				$this->order->assigned_at = $first->yq_started_at;
			}
			if ($first->platform == PlatformAccount::PLATFORM_MAO) {
				$this->order->assigned_at = $first->mao_started_at;
			}
			if ($first->platform == PlatformAccount::PLATFORM_TONG) {
				$this->order->assigned_at = $first->tong_started_at;
			}
			if ($first->platform == PlatformAccount::PLATFORM_MAMA) {
				$this->order->assigned_at = $first->mama_started_at;
			}

			$this->order->save();


			/* 其他平台的订单删除操作  ----------- */
			/** @type Collection $needDeleted */
			$needDeleted = PlatformStatus::where('order_id', $this->order->order_id)
				->where('id', '!=', $first->id)
				->get();

			$order = $this->order;
			// 先对各个平台进行订单删除请求
			$needDeleted->each(function ($status) use ($order) {
				$platform = PlatformFactory::create($status, $order, $this->pam);
				if (!$platform->delete()) {
					// save log
					if ($platform->getReqLog()) {
						\Log::debug('删单失败!');
						\Log::debug($platform->getReqLog());
					}
					else {
						\Log::debug('删单请求失败!');
						\Log::debug('疑似重单:' . $order->order_id);
					}
				}
			});


			// 未删除成功的平台的撤单操作
			$accept->each(function ($status) use ($order) {
				$platform = PlatformFactory::create($status, $order, $this->pam);
				if ($platform instanceof Tong) {
					if (!$platform->pubCancel(Tong::PUB_CANCEL_APPLY, 0, 0, '申请撤销')) {
						// save log
						\Log::debug('撤单失败 @ Tong');
						\Log::debug($platform->getReqLog());
					}
				}
				if ($platform instanceof Yi) {
					if (!$platform->pubCancel(Yi::PUB_CANCEL_APPLY, 0, 0, '申请撤销')
					) {
						// save log
						\Log::debug('撤单失败 @ Yi');
						\Log::debug($platform->getReqLog());
					}
				}
				if ($platform instanceof Baozi) {
					if (!$platform->pubCancel(Baozi::PUB_CANCEL_APPLY, 0, 0, '申请撤销')
					) {
						// save log
						\Log::debug('撤单失败 @ Baozi');
						\Log::debug($platform->getReqLog());
					}
				}
				if ($platform instanceof Yq) {
					if (!$platform->pubCancel(Yq::PUB_CANCEL_APPLY, 0, 0, '申请撤销')
					) {
						// save log
						\Log::debug('撤单失败 @ Yq');
						\Log::debug($platform->getReqLog());
					}
				}
				if ($platform instanceof Mao) {
					if (!$platform->pubCancel(Mao::PUB_CANCEL_APPLY, 0, 0, '申请撤销')) {
						// save log
						\Log::debug('撤单失败 @ Mao');
						\Log::debug($platform->getReqLog());
					}
				}
				if ($platform instanceof Mama) {
					if (!$platform->pubCancel(Mama::PUB_CANCEL_APPLY, 0, 0, '申请撤销')) {
						// save log
						\Log::debug('撤单失败 @ Mama');
						\Log::debug($platform->getReqLog());
					}
				}
			});
		}
		return true;
	}

	/**
	 * 同步本订单的所有详情
	 * @return bool
	 */
	public function syncDetail()
	{
		$status = $this->order->platformStatus;


		foreach ($status as $s) {

			// 创建便是同步了详情了.
			$platform = PlatformFactory::create($s, $this->order);

			// save log
			$this->writeReqLog($platform->getReqLog()); //原来的一直报错
			// $this->writeReqLog(333);
		}

		$this->order->updated_at = Carbon::now();
		$this->order->save();
		return true;
	}

	/**
	 * 获取战绩
	 * @param $order_id
	 */
	public function getRecord($order_id)
	{
		$tpl  = new TgpController();
		$data = $tpl->getPlayer($order_id, 'zj', $bt_list = true);
		$data = json_decode(substr($data, 45, -12));
		// var_dump($data->data[0]);
		//game_type:4 排位 5 灵活排位  battle_map 战斗地图 win : 2输了 1 赢了
		$win_num    = 0;
		$lost_num   = 0;
		$continuity = 0;
		if (isset($data->data[0]->battle_list)) {
			foreach ($data->data[0]->battle_list as $item) {
				if ($this->order->assigned_at && $item->battle_time > $this->order->assigned_at) {
					$continuity .= $item->win;
					if ($item->win == 1) {
						$win_num += 1;
					}
					if ($item->win == 2) {
						$lost_num += 1;
					}
				}
			}
			if (strpos($continuity, '222')) {
				$this->order->is_tgp_question = 1;
			}

		}
		// 存入数据
		$this->order->tgp_win        = $win_num;
		$this->order->tgp_num        = $win_num + $lost_num;
		$this->order->tpl_updated_at = Carbon::now();
		$this->order->save();
	}

	public function changePwd($pwd, $account_id)
	{
		$pwd = trim($pwd);
		if (!$pwd) {
			return $this->setError('密码不允许为空');
		}
		if ($pwd == $this->order->game_pwd) {
			return $this->setError('密码相同, 无需修改');
		}
		$this->order->game_pwd = $pwd;
		$this->order->save();

		$this->talk('修改密码', $account_id);
		return true;
	}


	public function urgency($type = 'disable')
	{
		if ($type == 'disable') {
			$this->order->is_urgency = 0;
		}
		else {
			$this->order->is_urgency = 1;
		}
		$this->order->save();
		return true;
	}

	public function renew($type = 'disable')
	{
		if ($type == 'disable') {
			$this->order->is_renew = 0;
		}
		else {
			$this->order->is_renew = 1;
		}
		$this->order->save();
		return true;
	}

	/**
	 * 批量重发
	 * @return bool
	 */
	public function batchRePublish()
	{
		if ($this->order->accept_id) {
			return $this->setError('已有接单, 不得重发');
		}
		if ($this->order->platformStatus) {
			foreach ($this->order->platformStatus as $status) {
				$pt = PlatformFactory::create($status, $this->order, $this->pam);
				$pt->rePublish();
			}
			return true;

		}
		else {
			return $this->setError('您尚未发布订单, 不得重新发布');
		}
	}

	/**
	 * @param PamAccount $pam
	 * @return bool
	 */
	public function rePublish($pam = null)
	{
		$this->syncDetail();
		$this->syncAccept();
		// 重新获取订单信息
		$this->setOrder($this->order->order_id);
		// 是否已经接单
		if (!$this->order->accept_id) {
			// 未接单, 进行订单的重发
			$status  = $this->order->platformStatus;
			$publish = true;
			foreach ($status as $s) {
				// 关闭易代练重发
				if ($s->platform == PlatformAccount::PLATFORM_YI) {
					continue;
				}
				// 关闭电竞包子重发
				if ($s->platform == PlatformAccount::PLATFORM_BAOZI) {
					continue;
				}

				//关闭17代练重发
				if ($s->platform == PlatformAccount::PLATFORM_YQ) {
					continue;
				}

				// 关闭代练猫重发
				if ($s->platform == PlatformAccount::PLATFORM_MAO) {
					continue;
				}

				//  关闭代练妈妈重发
				if ($s->platform == PlatformAccount::PLATFORM_MAMA) {
					continue;
				}

				// 启动重发
				$platform = PlatformFactory::create($s, $this->order, $pam);
				if (!$platform->rePublish()) {
					$publish = false;
					$this->setError($platform->getError());
				}
			}
			if (!$publish) {
				return false;
			}
			$this->order->published_at = Carbon::now();
			$this->order->save();

		}
		return true;
	}


	/**
	 * 重新获取本订单所有详情, 同步之后继续调用, 否则会出现数据不同步的情况
	 * @param null $id
	 */
	private function setOrder($id = null)
	{
		if ($id) {
			$this->order = PlatformOrder::with('platformStatus')->find($id);
			if (isset($this->order->accept_id)) {
				$this->order->load('platformAccept');
			}
			if (isset($this->order->accept_platform_account_id)) {
				$this->order->load('platformAccount');
			}
		}
	}

	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @return mixed
	 */
	public function getReqLog()
	{
		return $this->reqLog;
	}

	/**
	 * @param mixed $reqLog
	 */
	public function writeReqLog($reqLog)
	{
		$this->reqLog[] = $reqLog;
	}
}