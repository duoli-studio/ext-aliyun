<?php namespace Order\Application\Platform;

use App\Jobs\Platform\StaticsMamaOrderNum;
use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Lemon\Dailian\Application\Platform\Request\MamaReq;
use App\Lemon\Dailian\Application\Platform\Traits\BaseTraits;
use App\Lemon\Dailian\Contracts\Platform;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\PamAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformMaoLog;
use App\Models\PlatformMaoPic;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use OSS\OssClient;


class Mama implements Platform {

	use BaseTraits, PlatformTraits, DispatchesJobs;

	const PUB_CANCEL_APPLY  = 0;
	const PUB_CANCEL_CANCEL = 1;
	const PUB_CANCEL_AGREE  = 2;

	const LOCK_LOCK   = 1;
	const LOCK_UNLOCK = 0;

	const KF_KF   = 1;
	const KF_UNKF = 0;


	const ORDER_STATUS_CREATE      = 1;  //未接手
	const ORDER_STATUS_OFFLINE     = 2;  //已下架
	const ORDER_STATUS_ING         = 3;  //订单代练中
	const ORDER_STATUS_EXCEPTION   = 4;  //订单异常
	const ORDER_STATUS_WAIT_CANCEL = 5;  //
	const ORDER_STATUS_WAIT_VERIFY = 6;  //待验收
	const ORDER_STATUS_WAIT_PAY    = 7;  //待付款
	const ORDER_STATUS_OVER_PAY    = 8;  //已结算
	const ORDER_STATUS_CANCELING   = 9;  //订单正在撤销
	const ORDER_STATUS_OVER_CANCEL = 10; //已撤销
	const ORDER_STATUS_LOCK        = 11; //订单锁定

	const MODIFY_MONEY     = 0; //
	const MODIFY_TIME      = 1; //
	const MODIFY_GAME_WORD = 2; //

	const STAR_BEST   = 10; //
	const STAR_GOOD   = 11; //
	const STAR_NORMAL = 12; //
	const STAR_BAD    = 13; //

	const MAMA_LOCK                = 20002;      //锁定账号
	const MAMA_UNLOCK              = 20010;      //取消锁定
	const MAMA_CHECK               = 20005;      //申请验收
	const MAMA_CHECK_CANCEL        = 20017;      //取消验收
	const MAMA_CHECK_DONE          = 20013;      //验收完成
	const MAMA_ORDER_CANCEL        = 20006;      //订单申请撤销
	const MAMA_ORDER_CANCEL_CANCEL = 20012;      //取消订单撤销
	const MAMA_ORDER_CANCEL_OK     = 20009;      //同意订单撤销
	const MAMA_ARBITRATION         = 20007;      //申请仲裁
	const MAMA_ARBITRATION_CANCEL  = 20008;      //取消仲裁
	const MAMA_EXCEPTION           = 20004;      //提交异常
	const MAMA_EXCEPTION_CANCEL    = 20011;      //取消异常
	const MAMA_ADD_TIME            = 22001;      //补时
	const MAMA_ADD_MONEY           = 22002;      //补款
	const MAMA_CHANGE_PWD          = 22003;      //修改密码


	private $platform = PlatformAccount::PLATFORM_MAMA;

	/** @var MamaReq */
	private $req;

	/** @type  PlatformAccount */
	private $platformAccount;

	/** @type  PlatformOrder 平台订单 */
	private $order;

	/** @type  PamAccount 当前操作的账号 */
	private $handlePam;

	/** @type PlatformStatus */
	private $status;


	/**
	 * constructor.
	 * @param $pt_account_id   int 发单账号ID
	 * @param $platform_order  PlatformOrder
	 * @param $handle_pam      PamAccount 当前的操作用户
	 * @throws \Exception
	 */
	public function __construct($pt_account_id, $platform_order, $handle_pam = null) {
		$this->handlePam       = $handle_pam;
		$this->platformAccount = $this->getPtAccount($pt_account_id);
		$this->order           = $platform_order;
		$this->checkEnv();
		$this->status = $this->createStatus();
		$this->req    = new MamaReq($pt_account_id);
		$this->syncDetail();
	}


	/**
	 * 发布订单, 需要验证支付密码
	 * @return bool
	 * @throws \Exception
	 */
	public function publish() {

		if (!$this->checkPublish()) {
			return false;
		}

		$server = explode('/', $this->order->game_area);
		$param  = [
			'gamename'     => '英雄联盟',    // 游戏名称
			'areaname'     => $server[0],  // 区名称
			'servername'   => $server[1],  // 服名称
			'ordertitle'   => $this->order->order_title,  // 订单标题，最大长度50
			'orderorgin'   => 0,  // 订单发布区，0普通，1私有，2优质
			'unitprice'    => $this->order->order_price,  // 订单价格
			'safedeposit'  => $this->order->order_safe_money,  //  安全保证金，支持两位小数。
			'efficiency'   => $this->order->order_speed_money,  // 效率保证金，支持两位小数。
			'requiretime'  => $this->order->order_hours,  // 要求时间，单位小时
			'accountvalue' => $this->order->game_account,  // 账号
			'accountpwd'   => $this->order->game_pwd,  // 密码
			'rolename'     => $this->order->game_actor,  // 游戏角色名称
			'dlcontent'    => $this->order->order_content,  // 代练要求，长度不能超过600个字符
			// 'dltype'       => '排位',  // 代练类型，默认为“排位”。可选：定位、等级、陪练、匹配、晋级、级别、其他
			// 'orginuserid'  => '',  // 发布私有订单时使用，发送打手的ID，用英文逗号分隔
			'otherdesc'    => $this->order->order_current,  // 当前游戏说明
			// 'price'        => $this->$this->order->order_get_in_price,  // 备注的金额
			// 'title'        => '',  // 备注的标题
			// 'content'      => '',  // 备注的内容
			'linktel'      => $this->platformAccount->mobile,  // 发单人联系电话
			'qq'           => $this->platformAccount->qq,  // 发单人联系QQ
			// 'subid'        => 0,  // 子账号ID，可为空0

		];

		$validator = \Validator::make($param, [
			'areaname'     => 'required',
			'servername'   => 'required',
			'safedeposit'  => 'required|min:0.01|max:5000|numeric',
			'efficiency'   => 'required|min:0.01|max:5000|numeric',
			'unitprice'    => 'required|min:0.01|max:5000|numeric',
			'ordertitle'   => 'required|max:50|string',
			'accountvalue' => 'required|max:50|string',
			'rolename'     => 'required|max:50|string',
			'accountpwd'   => 'required|max:40|string',
			'dlcontent'    => 'required|max:600|string',
			'otherdesc'    => 'max:100|string',
		], [
			'areaname.required'   => '游戏大区不能为空',
			'servername.required' => '服务器名称不能为空',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		if (!$this->req->make('mod_pub', $param)) {
			return $this->setError($this->req->getError());
		}

		$this->status->pt_account_note = $this->platformAccount->note;
		$result                        = $this->req->getResp();
		if ($result['result'] == 1) {
			$success = '发布到代练妈妈成功';
			// 更新 订单号
			$this->status->mama_order_no   = $result['data']['orderid'];
			$this->status->mama_pt_message = $success;
			$this->status->mama_is_error   = 0;
			$this->status->mama_is_delete  = 0;
			$this->status->mama_is_publish = 1;
			$this->status->save();

			// 统计订单信息
			$this->staticsPlatformAccountOrder();

			// 订单状态更改为已发布
			$this->order->order_status = PlatformOrder::ORDER_STATUS_PUBLISH;
			$this->order->published_at = Carbon::now();
			if($this->order->first_published_at == NULL){
				$this->order->first_published_at = Carbon::now();
			}
			$this->order->save();

			$this->saveAssignAccount();
			return true;
		} elseif ($result['result'] == 2) {
			$this->status->mama_is_delete  = 0;
			$this->status->mama_is_publish = 0;
			$this->status->mama_is_error   = 1;
			$this->status->mama_pt_message = $result['data']['message'];
			$this->status->save();
			return $this->setError($result['data']['message']);
		}
		return $this->setError('代练妈妈服务器返回超出可识别范围');
	}


	/**
	 * 上架订单
	 * 此接口可以刷新接单大厅中的排序。
	 * 建议使用此接口刷新接单大厅排序
	 * 系统不提倡使用删除订单再重新发布的方法来刷新接单大厅排序
	 * 系统有接口调用次数限制，删除重发会消耗两次接口调用次数
	 * @return bool
	 * @throws \Exception
	 */
	public function up() {
		$mark = '[上架]';
		if (!$this->req->make('up', [
			'orderid' => $this->status->mama_order_no,
		])
		) {
			return $this->setError($this->req->getError());
		}

		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			return true;
		} elseif ($result['result'] == 2) {
			$error = $mark . $result['data'];
			// 记录同步订单错误
			$this->status->mama_is_error   = 1;
			$this->status->mama_pt_message = $error;
			$this->status->save();
			return $this->setError($error);
		}
		return $this->setError('代练妈妈服务器返回超出可识别范围');
	}

	/**
	 * 删除订单
	 * @return bool
	 * @throws \Exception
	 */
	public function delete() {
		if (!$this->status->mama_order_no) {
			return true;
		}
		if (!$this->req->make('delete', [
			'orderid' => $this->status->mama_order_no,
		])
		) {
			return $this->setError($this->req->getError());
		}

		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			$this->status->delete();
			return true;
		} elseif ($result['result'] == 2) {
			$error = $result['data'];
			// 记录同步订单错误
			$this->status->mama_is_error   = 1;
			$this->status->mama_pt_message = $error;
			$this->status->save();
			return $this->setError($error);
		}
		return $this->setError('代练妈妈服务器返回超出可识别范围');
	}

	/**
	 * 发单者撤销
	 * @param            $flag
	 * @param            $pub_pay
	 * @param            $sd_pay
	 * @param string     $reason
	 * @return bool
	 */
	public function pubCancel($flag, $pub_pay, $sd_pay, $reason = '') {
		if (!$this->checkTradePwd()) {
			return false;
		}
		switch ($flag) {
			case self::PUB_CANCEL_CANCEL:
				$param = [
					'orderid' => $this->status->mama_order_no,
					'control' => self::MAMA_ORDER_CANCEL_CANCEL,
				];
				break;
			case self::PUB_CANCEL_AGREE:
				$verify = self::checkTradePwd();
				$param  = [
					'orderid'          => $this->status->mama_order_no,
					'control'          => self::MAMA_ORDER_CANCEL_OK,
					'checkTradePwdRDM' => $verify,
					'reason'           => $reason,
					'refundment'       => $pub_pay,
					'bail'             => $sd_pay,
				];
				break;
			case self::PUB_CANCEL_APPLY:
			default:
				$pwdResult = $this->req->getResp();
				$verify    = $pwdResult['data'];
				$param     = [
					'orderid'          => $this->status->mama_order_no,
					'control'          => self::MAMA_ORDER_CANCEL,
					'checkTradePwdRDM' => $verify,
					'reason'           => $reason,
					'refundment'       => $pub_pay,
					'bail'             => $sd_pay,
				];
				break;
		}
		if (!$this->req->make('order_operation', $param)) {
			return $this->setError($this->req->getError());
		}
		$result   = $this->req->getResp();
		$flagDesc = self::kvPubCancel($flag);
		if ($result['result'] == 1) {
			// 取消撤销之后进行解锁操作
			if ($flag == self::PUB_CANCEL_CANCEL) {
				$paramUnlock = [
					'orderid' => $this->status->mama_order_no,
					'control' => self::MAMA_UNLOCK,
				];
				$this->req->make('order_operation', $paramUnlock);
			}
			$this->syncDetail();
			return true;
		} else {
			return $this->setError('[' . $flagDesc . ']' . $result['data']);
		}
	}

	/**
	 * 下架
	 * @return bool
	 */
	public function down() {
		if (!$this->req->make('down', [
			'orderid' => $this->status->mama_order_no,
		])
		) {
			return $this->setError($this->req->getError());
		}

		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			return true;
		} elseif ($result['result'] == 2) {
			$error = $result['data'];
			// 记录同步订单错误
			$this->status->mama_is_error   = 1;
			$this->status->mama_pt_message = $error;
			$this->status->save();
			return $this->setError($error);
		}
		return $this->setError('代练妈妈服务器返回超出可识别范围');
	}


	public function syncDetail() {
		if ($this->isDelete()) {
			return false;
		}
		$detail = $this->detail();
		if (!$detail) {
			return false;
		}

		$this->status->mama_left_hour     = (int) array_get($detail, 'data.info.surplustime');
		$this->status->mama_status_desc   = array_get($detail, 'data.info.orderstatusname');
		$this->status->mama_started_at    = array_get($detail, 'data.info.receivetime');
		$this->status->mama_order_hour    = array_get($detail, 'data.info.requiretime');
		$this->status->mama_sd_name       = array_get($detail, 'data.userinfo.nickname');
		$this->status->mama_sd_uid        = array_get($detail, 'data.userinfo.userid');
		$this->status->mama_sd_mobile     = array_get($detail, 'data.userinfo.linktel');
		$this->status->mama_sd_qq         = array_get($detail, 'data.userinfo.qq');
		$this->status->mama_close_day     = (int) array_get($detail, 'data.info.closeday');
		$this->status->mama_can_modify    = (int) array_get($detail, 'data.info.canmodify');
		$this->status->mama_created_at    = array_get($detail, 'data.info.createtime');
		$this->status->mama_type_title    = array_get($detail, 'data.info.dltype');
		$this->status->mama_game_id       = array_get($detail, 'data.info.gameid');
		$this->status->mama_updated_at    = array_get($detail, 'data.info.lastmodifytime');
		$this->status->mama_order_status  = (int) array_get($detail, 'data.info.orderstatus');
		$this->status->mama_review_status = (int) array_get($detail, 'data.info.reviewstatus');
		$this->status->mama_sell_id       = array_get($detail, 'data.info.sellerid');
		$this->status->mama_upload_result = (int) array_get($detail, 'data.info.uploadResult');
		$this->status->mama_order_price   = (int) array_get($detail, 'data.info.unitprice');
		$this->status->sync_at            = Carbon::now();

		if (in_array($this->status->mama_order_status, [self::ORDER_STATUS_ING]) ||
			in_array($this->status->mama_order_status, [self::ORDER_STATUS_EXCEPTION]) ||
			in_array($this->status->mama_order_status, [self::ORDER_STATUS_WAIT_VERIFY]) ||
			in_array($this->status->mama_order_status, [self::ORDER_STATUS_CANCELING]) ||
			in_array($this->status->mama_order_status, [self::ORDER_STATUS_LOCK])
		) {
			// 已经接手
			$this->status->mama_is_accept = 1;
			$this->status->accepted_at    = $this->status->mama_started_at;
		}
		$this->status->save();

		// 同步到平台主订单
		$this->syncToPlatformOrder();
		return true;

	}

	/**
	 *
	 */
	public function rePublish() {
		$this->delete();
		$this->status = $this->createStatus();
		return $this->publish();
	}


	/**
	 * 订单状态说明
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvOrderStatus($key = null) {
		$desc = [
			self::ORDER_STATUS_CREATE      => '未接手',
			self::ORDER_STATUS_OFFLINE     => '已下架',
			self::ORDER_STATUS_ING         => '代练中',
			self::ORDER_STATUS_EXCEPTION   => '异常',
			self::ORDER_STATUS_WAIT_CANCEL => '待撤销',
			self::ORDER_STATUS_WAIT_VERIFY => '待验证',
			self::ORDER_STATUS_WAIT_PAY    => '待结算',
			self::ORDER_STATUS_OVER_PAY    => '已结算',
			self::ORDER_STATUS_CANCELING   => '撤销中',
			self::ORDER_STATUS_OVER_CANCEL => '已撤单',
			self::ORDER_STATUS_LOCK        => '已锁定',
		];
		return kv($desc, $key);
	}


	private function staticsPlatformAccountOrder() {
		dispatch(new StaticsMamaOrderNum($this->platformAccount, $this->platform));
	}

	/**
	 * 是否已经删除订单
	 */
	private function isDelete() {
		if ($this->status->mama_is_delete) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * 获取代练猫订单详细
	 * @return bool|array
	 * @throws \Exception
	 */
	private function detail() {
		if (!$this->status->mama_order_no) {
			return $this->setError('未发单, 不进行同步');
		}
		// 删除不进行请求
		if ($this->status->mama_is_delete) {
			return $this->setError('已删除, 不进行同步');
		}

		if (!$this->req->make('detail', [
			'orderid' => $this->status->mama_order_no,
		])
		) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			return $result;
		} elseif ($result['result'] == 2) {
			return $this->setError($result['data']['message']);
		}
		return $this->setError('代练妈妈服务器返回超出可识别范围');
	}


	/**
	 * 同步到主订单状态
	 * @return bool
	 */
	private function syncToPlatformOrder() {
		if (!$this->order->accept_id) {
			return false;
		}
		if ($this->order->accept_id != $this->status->id) {
			return false;
		}

		$old_order = clone $this->order;
		// 已经接单, 同步到主订单
		$this->order->assigned_at      = $this->status->mama_started_at;
		$this->order->order_hours      = $this->status->mama_order_hour;
		$this->order->sd_username      = $this->status->mama_sd_name;
		$this->order->sd_uid           = $this->status->mama_sd_uid;
		$this->order->sd_qq            = $this->status->mama_sd_qq;
		$this->order->sd_mobile        = $this->status->mama_sd_mobile;
		$this->order->is_star          = $this->status->mama_review_status;
		$this->order->create_uid       = $this->status->mama_sell_id;
		$this->order->updated_at       = $this->status->mama_updated_at;
		$this->order->order_price      = $this->status->mama_order_price;

		switch ($this->status->mama_order_status) {
			case self::ORDER_STATUS_CREATE:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_PUBLISH;
				break;
			case self::ORDER_STATUS_ING :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_ING;
				break;
			case self::ORDER_STATUS_EXCEPTION :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_EXCEPTION;
				break;
			case self::ORDER_STATUS_WAIT_VERIFY :
			case self::ORDER_STATUS_WAIT_PAY :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_EXAMINE;
				break;
			case self::ORDER_STATUS_LOCK :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_ING;
				$this->order->order_lock   = 1;
				break;
			case self::ORDER_STATUS_OVER_PAY :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_OVER;
				break;
			case self::ORDER_STATUS_WAIT_CANCEL :
			case self::ORDER_STATUS_CANCELING :
				$this->order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
				$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_ING;
				$this->order->cancel_type   = PlatformOrder::CANCEL_TYPE_PUB_DEAL;
				if ($this->status->mama_status_desc == '仲裁中') {
					$this->order->cancel_status = PlatformOrder::CANCEL_CUSTOM_KF_DEAL_IN;
					$this->order->cancel_type   = PlatformOrder::CANCEL_TYPE_KF;
					$this->order->kf_status     = PlatformOrder::KF_STATUS_ING;
				}
				break;
			case self::ORDER_STATUS_OVER_CANCEL :
				$this->order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
				$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_DONE;
				$this->order->cancel_type   = PlatformOrder::CANCEL_TYPE_PUB_DEAL;
				break;
		}
		$this->order->pt_accept_sync_at = Carbon::now();
		$this->order->save();


		$this->sendMessage($old_order, $this->order);
		return true;
	}

	public function messageList($begin_id = null) {
		$param = [
			'orderid' => $this->status->mama_order_no,
		];
		if ($begin_id) {
			$param['beginid'] = $begin_id;
		}
		if (!$this->req->make('get_chat', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		return $result;
	}

	/**
	 * 同步最近50条信息
	 */
	public function syncMessage() {
		$messageList = $this->messageList();
		$list        = array_get($messageList, 'data.list');
		if ($list) {
			foreach ($list as $item) {
				PlatformMaoLog::updateOrCreate([
					'chatid'      => $item['chatid'],
					'order_id'    => $this->status->order_id,
					'nick_name'   => $item['nickname'],
					'msg_content' => $item['content'],
					'created_at'  => $item['createtime'],
					'order_no'    => $messageList['data']['orderid'],
					'mao_user_id' => $item['userid'],
					'msg_type'    => $item['type'],
				]);
			}
		}
	}

	/**
	 * 同步最近50条信息
	 */
	public function syncPic() {
		$param = [
			'orderid' => $this->status->mama_order_no,
		];
		if (!$this->req->make('get_pic', $param)) {
			return $this->setError($this->req->getError());
		}
		$picList = $this->req->getResp();
		$info    = array_get($picList, 'data.info2');
		if ($info) {
			foreach ($info as $item) {
				PlatformMaoPic::updateOrCreate([
					'order_id'    => $this->status->order_id,
					'order_no'    => $item['orderid'],
					'address'     => $item['address'],
					'nick_name'   => $item['nickname'],
					'description' => $item['description'],
					'created_at'  => $item['createtime'],
					'status'      => $item['status'],
					'user_id'     => $item['userid'],
				]);
			}
		}
		return true;
	}


	public function message($content) {
		$param = [
			'orderid' => $this->status->mama_order_no,
			'content' => $content,
		];
		if (!$this->req->make('add_chat', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			// 完单
			$PlatformOrder = new ActionPlatformOrder($this->status->order_id);
			$PlatformOrder->talk($content, $this->handlePam->account_id);
			return true;
		} else {
			$error = '[发布留言]' . $result['data'];
			return $this->setError($error);
		}
	}

	/**
	 * 锁定/解锁
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvLock($key = null) {
		$desc = [
			self::LOCK_LOCK   => '锁定',
			self::LOCK_UNLOCK => '解锁',
		];
		return kv($desc, $key);
	}

	/**
	 * 申请客服介入/取消客服介入
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvKf($key = null) {
		$desc = [
			self::KF_KF   => '申请客服介入',
			self::KF_UNKF => '取消客服介入',
		];
		return kv($desc, $key);
	}

	/**
	 * 评价
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvStar($key = null) {
		$desc = [
			self::STAR_BAD    => '很差',
			self::STAR_NORMAL => '一般',
			self::STAR_GOOD   => '满意',
			self::STAR_BEST   => '非常满意',
		];
		return kv($desc, $key);
	}

	/**
	 * 修改订单
	 * @param $type
	 * @param $value
	 * @return bool
	 */
	public function modify($type, $value) {
		$param = [];
		switch ($type) {
			case self::MAMA_ADD_TIME:
				$param = [
					'orderid'         => $this->status->mama_order_no,
					'control'         => $type,
					'requiretimehadd' => $value,
				];
				break;
			case self::MAMA_ADD_MONEY:
				$verify = self::checkTradePwd();
				$param  = [
					'orderid'          => $this->status->mama_order_no,
					'control'          => $type,
					'unitpriceadd'     => $value,
					'checkTradePwdRDM' => $verify,
				];
				break;
			case self::MAMA_CHANGE_PWD:
				$param = [
					'orderid'    => $this->status->mama_order_no,
					'control'    => $type,
					'accountpwd' => $value,
				];
				break;
		}
		if (!$param) {
			return $this->setError('参数错误');
		}

		if (!$this->req->make('order_operation', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			if ($type == self::MAMA_CHANGE_PWD) {
				$this->order->game_pwd = $value;
				$this->order->save();
			} else if ($type == self::MAMA_ADD_MONEY) {
				$this->order->order_price += $value;
				$this->order->save();
			}
			$this->status->mama_pt_message = $result['data'];
			$this->status->mama_is_error   = 0;
			$this->status->save();
			return true;
		} else {
			$this->status->mama_pt_message = $result['data'];
			$this->status->mama_is_error   = 1;
			$this->status->save();
			return $this->setError($result['data']);
		}
	}

	public static function kvModify($key = null) {
		$desc = [
			self::MODIFY_GAME_WORD => '修改游戏密码',
			self::MODIFY_TIME      => '时间(追加)',
			self::MODIFY_MONEY     => '金额(追加)',
		];
		return kv($desc, $key);
	}

	/**
	 * 获取支付密码验证码
	 */
	public function checkTradePwd() {
		$pay_password = md5(md5($this->platformAccount->mama_payword) . 'dlmapp');
		$arr          = [
			'tradePassword' => $pay_password,
			'type'          => 'md5',
		];
		if (!$this->req->make('check_pwd', $arr)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['result'] == 2) {
			return $this->setError($result['data']['msg']);
		} elseif ($result['result'] == 1) {
			return $result['data'];
		}
		return $this->setError('代练妈妈服务器返回超出可识别范围');
	}

	/**
	 * 发单者取消描述
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvPubCancel($key = null) {
		$desc = [
			self::PUB_CANCEL_CANCEL => '取消撤销',
			self::PUB_CANCEL_AGREE  => '同意撤销',
			self::PUB_CANCEL_APPLY  => '申请撤销',
		];
		return kv($desc, $key);
	}

	/**
	 * 订单完结
	 * @return bool
	 */
	public function overOrder() {
		$verify = self::checkTradePwd();
		$param  = [
			'orderid'          => $this->status->mama_order_no,
			'control'          => self::MAMA_CHECK_DONE,
			'checkTradePwdRDM' => $verify,
		];
		if (!$this->req->make('order_operation', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['result'] == 1) {
			// 完单
			$PlatformOrder = new ActionPlatformOrder();
			$PlatformOrder->over($this->status->order_id, $this->handlePam->account_id);
			return true;
		} else {
			return $this->setError($result['data']);
		}
	}

	/**
	 * 更新订单进度
	 * @param                                                     $message
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image
	 * @return bool
	 */
	public function progress($message, $image = null) {
		$url = '';
		if ($image) {
			if (!$this->getOss()) {
				return false;
			}
			$oss             = $this->req->getResp();
			$accessKeyId     = $oss['data']['AccessKeyId'];
			$accessKeySecret = $oss['data']['AccessKeySecret'];
			$securityToken   = $oss['data']['SecurityToken'];
			$bucket_name     = $oss['data']['bucket_name']; //cloundss
			$bucket_path     = $oss['data']['bucket_path']; //
			$endpoint        = $oss['data']['prefix_url'];  //$prefix_url
			$ossClient       = new OssClient($accessKeyId, $accessKeySecret, $endpoint, true, $securityToken);
			$time            = time();
			$imageName       = $image->getClientOriginalName();
			try {
				$ossClient->uploadFile($bucket_name, $bucket_path . $time . $imageName, $image->move(public_path('uploads/temp'), $time . $imageName));
			} catch (\Exception $e) {
				return $this->setError($e->getMessage());
			}
			$url = $endpoint . $bucket_path . $time . $imageName;
		}
		if (!$url) {
			return $this->setError('请上传图片');
		}
		$param = [
			'orderid'     => $this->status->mama_order_no,
			'imgurl'      => $url,
			'description' => $message,
		];

		if (!$this->req->make('save_pic', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			return true;
		} else {
			$error = $result['data'];
			return $this->setError($error);
		}
	}

	/**
	 * 获取OSS上传凭证
	 */
	private function getOss() {
		if (!$this->req->make('get_TempUploadKey')) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			return true;
		} else {
			$error = $result['data'];
			return $this->setError($error);
		}
	}


	/**
	 * 刷新
	 * @return bool
	 */
	public function refresh() {
		if (!$this->req->make('refresh')) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['result'] == 1) {
			return true;
		} else {
			return $this->setError($result['data']);
		}
	}


	/**
	 * 解锁/锁定订单OK
	 * @param        $flag
	 * @param string $reason
	 * @return bool
	 */
	public function lock($flag, $reason = '') {
		if ($flag == self::LOCK_LOCK) {
			$param = [
				'orderid' => $this->status->mama_order_no,
				'control' => self::MAMA_LOCK,
			];
		} else {
			$param = [
				'orderid' => $this->status->mama_order_no,
				'control' => self::MAMA_UNLOCK,
			];
		}

		if (!$this->req->make('order_operation', $param)) {
			return $this->setError($this->req->getError());
		}
		$result   = $this->req->getResp();
		$flagDesc = self::kvLock($flag);
		if ($result['result'] == 1) {
			// 完单
			$PlatformOrder = new ActionPlatformOrder();
			if ($flag == self::LOCK_LOCK) {
				$PlatformOrder->lock($this->status->order_id, $reason, $this->handlePam->account_id);
				$this->status->mama_is_lock      = 1;
				$this->status->mama_order_status = self::ORDER_STATUS_LOCK;
			} else if ($flag == self::LOCK_UNLOCK) {
				$PlatformOrder->unlock($this->status->order_id, $reason, $this->handlePam->account_id);
				$this->status->mama_is_lock      = 0;
				$this->status->mama_order_status = self::ORDER_STATUS_ING;
			}

			$this->status->save();
			return true;
		} else {
			$error = '[' . $flagDesc . ']' . $result['data'];
			return $this->setError($error);
		}
	}

	/**
	 * 申请客服介入/取消客服介入
	 * @param $kf
	 * @param $reason
	 * @return bool
	 */
	public function kf($kf, $reason) {
		if ($kf) {
			$param = [
				'orderid' => $this->status->mama_order_no,
				'control' => self::MAMA_ARBITRATION,
				'reason'  => $reason,
			];
		} else {
			$param = [
				'orderid' => $this->status->mama_order_no,
				'control' => self::MAMA_ARBITRATION_CANCEL,
				'reason'  => $reason,
			];
		}
		if (!$this->req->make('order_operation', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($kf) {
			if ($result['result'] == 1) {
				// 完单
				$PlatformOrder = new ActionPlatformOrder();
				$PlatformOrder->kf($this->status->order_id, $this->handlePam->account_id);
				$this->status->mama_is_kf = 1;
				$this->status->save();
				return true;
			} else {
				$error = $result['data'];
				return $this->setError($error);
			}
		} else {
			if ($result['result'] == 1) {
				// 完单
				$PlatformOrder = new ActionPlatformOrder();
				$PlatformOrder->unkf($this->status->order_id, $this->handlePam->account_id);
				$this->status->mama_is_kf = 0;
				$this->status->save();
				return true;
			} else {
				$error = $result['data'];
				return $this->setError($error);
			}
		}
	}

	public function getReqResp() {
		return $this->req->getReqResp();
	}


	public function getReqLog() {
		return $this->req->getReqLog();
	}

	public function getStatus()
	{
		return $this->status;
	}
}