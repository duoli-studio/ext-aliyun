<?php namespace Order\Application\Platform;

use App\Jobs\Platform\StaticsYqOrderNum;
use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Lemon\Dailian\Application\Platform\Request\YqReq;
use App\Lemon\Dailian\Application\Platform\Traits\BaseTraits;
use App\Lemon\Dailian\Contracts\Platform;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Models\GameServer;
use App\Models\PamAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use OSS\OssClient;

class Yq implements Platform
{

	use BaseTraits, DispatchesJobs, PlatformTraits;

	const STAR_GOOD   = '0';
	const STAR_NORMAL = '1';
	const STAR_BAD    = '2';

	const LOCK_LOCK   = 1;  //锁上
	const LOCK_UNLOCK = 0;  //解锁

	const KF_KF   = 1;   //申请客服
	const KF_UNKF = 0;   //取消申请客服

	const PUB_CANCEL_APPLY  = 0;
	const PUB_CANCEL_CANCEL = 1;
	const PUB_CANCEL_AGREE  = 2;
	const PUB_CANCEL_REJECT = 3;
	const ORDER_STATUS_LOCK = 4;

	const MODIFY_MONEY     = 0;
	const MODIFY_TIME      = 1;
	const MODIFY_GAME_WORD = 2;

	const ORDER_STATUS_ING          = '300';       // 代练进行中
	const ORDER_STATUS_CREATE       = '';          // 代练进行中
	const ORDER_STATUS_LOCKAFTERING = '400';       // 代练进行中(锁定之后的代练)
	const ORDER_STATUS_PUBLISH      = '200';       // 等待代练
	const ORDER_STATUS_EXAMINE      = '500';       // 等待验收
	const ORDER_STATUS_OVER         = '700';       // 订单完成
	const ORDER_CANCEL_OVER         = '741';       // 订单撤销完成
	const ORDER_STATUS_EXCEPTION    = '600';       // 订单异常
	const ORDER_STATUS_CANCEL       = '410';       // 撤销,退单
	const ORDER_STATUS_DELETE       = '720';       // 删除

	const EXCEPTION_TYPE_NONE          = 'none';
	const EXCEPTION_TYPE_ACCOUNT_ERROR = 'account_error';
	const EXCEPTION_TYPE_OTHER         = 'other';

	const HANDLE_CANCEL_AGREE  = 'agree';
	const HANDLE_CANCEL_REJECT = 'reject';
	const HANDLE_CANCEL_KF     = 'kf';

	const KF_APPLY_BY_PUB  = 'pub';
	const KF_APPLY_BY_NONE = 'none';
	const KF_APPLY_BY_SD   = 'sd';

	const ORDER_LOCK_LOCK   = 1;
	const ORDER_LOCK_UNLOCK = 0;

	const SYNC_PLATFORM_YI   = 'yi';
	const SYNC_PLATFORM_MAO  = 'mao';
	const SYNC_PLATFORM_TONG = 'tong';
	const SYNC_PLATFORM_YQ   = 'yq';

	const CANCEL_TYPE_NONE     = 'none';
	const CANCEL_TYPE_SD_DEAL  = 'sd_deal';
	const CANCEL_TYPE_PUB_DEAL = 'pub_deal';
	const CANCEL_TYPE_KF       = 'kf';

	const CANCEL_STATUS_DONE   = 'done';
	const CANCEL_STATUS_NONE   = 'none';
	const CANCEL_STATUS_ING    = 'ing';
	const CANCEL_STATUS_REJECT = 'reject';

	const KF_STATUS_DONE = 'done';
	const KF_STATUS_NONE = 'none';
	const KF_STATUS_WAIT = 'wait';
	const KF_STATUS_ING  = 'ing';

	const CANCEL_STATUS_TREATY     = 410;       // 发单者申请撤销
	const kf_STATUS_420            = 420;       // 发单者申请撤销
	const CANCEL_STATUS_330        = 330;       // 发单者锁定中
	const CANCEL_STATUS_430        = 430;       // 发单者锁定中
	const ORDER_STATUS_OVER_CANCEL = 730;       //已撤销
	const CANCEL_STATUS_431        = 431;       //打手申请撤销
	const ORDER_STATUS_OVER_PAY    = 12;        //验收完成支付
	const ORDER_STATUS_CANCELING   = 331;       //订单打手正在撤销
	const YQ_ARBITRATION           = 20007;     //申请仲裁
	const YQ_ARBITRATION_CANCEL    = 20008;     //取消仲裁

	private $platform = PlatformAccount::PLATFORM_YQ;


	/** @type  PlatformAccount */
	private $platformAccount;

	/** @type  PlatformOrder 平台订单 */
	private $order;

	/** @type  PamAccount */
	private $handlePam;

	/** @var YqReq */
	private $req;

	/** @var string */
	private $appPayword;


	/** @type PlatformStatus */
	private $status;


	/**
	 * Yq constructor.
	 * @param $pt_account_id       int              发单账号id
	 * @param $platformOrder       PlatformOrder    订单信息
	 * @param $handle_pam          null|PamAccount  操作的用户信息
	 */
	public function __construct($pt_account_id, $platformOrder, $handle_pam = null)
	{
		$this->handlePam       = $handle_pam;
		$this->order           = $platformOrder;
		$this->platformAccount = $this->getPtAccount($pt_account_id);
		$this->checkEnv();
		$this->req        = new YqReq($pt_account_id);
		$this->appPayword = $this->rsaCalc($this->platformAccount->yq_payword); //RSA加密支付密码

		$this->status = $this->createStatus();
		$this->syncDetail();
	}


	/**
	 * 发布订单
	 * @return bool
	 */
	public function publish()
	{
		$gameServer = GameServer::yqServerCode($this->order->server_id);//整条查出来 例：英雄联盟/电信/艾欧尼亚
		$server     = explode('/', $gameServer);
		$param      = [
			'OperationStatus'    => '发布订单',
			'ODSerialNo'         => $this->order->order_get_in_number,  // 来源的订单号
			'password'           => $this->appPayword,                  //支付密码
			'account'            => $this->order->game_account,         // 账号
			'pwd'                => $this->order->game_pwd,             // 密码
			'role_name'          => $this->order->game_actor,           // 游戏角色名称
			'content'            => $this->order->order_content,        // 代练要求，长度不能超过600个字符
			'game_description'   => $this->order->order_note,           //其他说明
			'game_name'          => $server[0],                         //游戏标示
			'game_server'        => $server[1],                         //游戏服标示
			'game_zone'          => $server[2],                         //游戏区标示
			'order_type'         => 1,                                  //订单类型 公共还是私有 现在给一个固定参数‘公共’ 后期调整
			'leveling_type'      => $this->order->type_id,              //代练等级代练等级:排位 0,等级 1,匹配 2,陪练 3,晋级 4,级别 5,其他 6
			'price'              => $this->order->order_price,          // 订单价格
			'efficiency_deposit' => $this->order->order_safe_money,     //  安全保证金，支持两位小数。
			'security_deposit'   => $this->order->order_speed_money,    // 效率保证金，支持两位小数。
			'time_h'             => $this->order->order_hours,          //   单位小时
			'title'              => $this->order->order_title,          // 订单标题，最大长度50
		];

		$validator = \Validator::make($param, [
			'ODSerialNo'         => 'required',
			'password'           => 'required',
			'account'            => 'required',
			'pwd'                => 'required',
			'role_name'          => 'required',
			'content'            => 'required',
			'game_name'          => 'required',
			'game_server'        => 'required',
			'game_zone'          => 'required',
			'price'              => 'required',
			'efficiency_deposit' => 'required|numeric',
			'security_deposit'   => 'required|numeric',
			'time_h'             => 'required|numeric',
			'title'              => 'required',
		], [
			'ODSerialNo.required'         => '订单号不能为空',
			'password.required'           => '支付密码不能为空',
			'account.required'            => '游戏账号不能为空',
			'pwd.required'                => '游戏密码不能为空',
			'content.required'            => '代练要求不能为空',
			'game-description.required'   => '其他说明不能为空',
			'game_name.required'          => '游戏id不能为空',
			'game_server.required'        => '游戏服id不能为空',
			'game_zone.required'          => '游戏区id不能为空',
			'order_type.required'         => '订单类型不能为空',
			'leveling_type.required'      => '代练等级不能为空',
			'price.required'              => '订单价格不能为空',
			'efficiency_deposit.required' => '安全保证金不能为空',
			'security_deposit.required'   => '效率保证金不能为空',
			'time_h.required'             => '代练小时不能为空',
			'title.required'              => '订单标题不能为空',
		]);

		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}


		if (!$this->req->make('send', $param)) {
			return $this->setError($this->req->getError());
		}
		$this->status->pt_account_note = $this->platformAccount->note;

		$resp = $this->req->getResp();

		if ($resp['status'] == 1) {
			$success = '发布到17代练成功';
			// 更新 订单号
			$this->status->yq_order_no   = $resp['data']['order_id'];
			$this->status->yq_pt_message = $success;
			$this->status->yq_is_error   = 0;
			$this->status->yq_is_delete  = 0;
			$this->status->yq_is_publish = 1;
			$this->status->save();


			// 统计订单信息
			$this->staticsPlatformAccountOrder();

			// 订单状态更改为已发布
			$this->order->order_status = PlatformOrder::ORDER_STATUS_PUBLISH;
			$this->order->published_at = Carbon::now();
			if ($this->order->first_published_at == null) {
				$this->order->first_published_at = Carbon::now();
			}
			$this->order->save();

			$this->saveAssignAccount();
			return true;
		}
		else {
			$error = $resp['alert'];
			// 记录同步订单错误
			$this->status->yq_is_delete  = 0;
			$this->status->yq_is_publish = 0;
			$this->status->yq_is_error   = 1;
			$this->status->yq_pt_message = $error;
			$this->status->save();
			return $this->setError($error);
		}
	}


	/**
	 * 修改订单  (一般不会对订单进行修改，是删除之后重新发送)  方法名:rePublish
	 * @param $type
	 * @param $value
	 * @return bool
	 */
	public function modify($type, $value)
	{
		$param = [
			'SerialNo' => $this->status->yq_order_no,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 1) {
			if ($type == self::MODIFY_GAME_WORD) {
				$this->order->game_pwd = $value;
				$this->order->save();
			}
			$this->status->yq_pt_message = '修改订单成功';
			$this->status->yq_is_error   = 0;
			$this->status->save();
			return true;
		}
		else {
			$this->status->yq_pt_message = $result['data']['order_id'];
			$this->status->yq_is_error   = 1;
			$this->status->save();
			return $this->setError($result['data']['order_id']);
		}
	}

	/**
	 * 重新发送接口
	 * @return bool
	 */
	public function rePublish()
	{
		$this->delete();
		$this->status = $this->createStatus();
		return $this->publish();
	}


	/**
	 * 详情同步到status表
	 * @return bool
	 */
	public function syncDetail()
	{
		$result = $this->detail();
		if (!$result) {
			return false;
		}
		if ($result['status'] == 1) {
			// success
			if ($result['data']['receiver_time'] != null) {
				$this->status->yq_receiver_at = date('Y-m-d H:i:s', $result['data']['receiver_time']);
				$this->status->yq_is_accept   = 1; //是否代练中
				$this->status->yq_left_hour   = $result['data']['leave_time'];

				//判断订单是否完成
				if ($result['data']['finish_time'] != 0) {
					$this->status->yq_is_over   = 1;
					$this->status->yq_overed_at = date('Y-m-d H:i:s', $result['data']['finish_time']);
				}

				/** @type PamAccount $user */
				$this->status->yq_sd_id     = $result['data']['receiver_id'];   //17代练接单者id
				$this->status->yq_sd_name   = $result['data']['nickname'];    //17代练接单者
				$this->status->yq_sd_mobile = $result['data']['phone'];
				$this->status->yq_sd_qq     = $result['data']['qq'];
			}

			switch ($result['data']['status']) {
				case 330:
					$this->status->yq_is_lock = 1;
					break;
				case 410:
					$this->status->yq_cancel_status = $result['data']['status'];
					break;
				case 630:
					$this->status->yq_is_lock = 1;
					break;
				case 430:
					$this->status->yq_is_lock = 1;
					break;
				case 331:
					$this->status->yq_cancel_status = $result['data']['status'];
					break;
				case 400:
					$this->status->yq_cancel_status = $result['data']['status'];
					break;
			}

			/* 删除订单状态同步 (Mark Zhao) ----------- */
			if (isset($result['data']['status']) && $result['data']['status'] == 720) {
				$this->status->yq_is_delete  = 1;
				$this->status->yq_pt_message = '订单已删除!';
				$this->status->deleted_at    = $result['data']['deleted_at'] ? $result['data']['deleted_at'] : 0;
			}

			$this->status->yq_order_title   = $result['data']['title'];              //订单标题
			$this->status->yq_order_price   = $result['data']['price'];              //订单价格
			$this->status->yq_order_status  = $result['data']['status'];             // 订单状态
			$this->status->yq_add_price     = $result['data']['add_price'];          //添加价格
			$this->status->yq_speed_money   = $result['data']['security_deposit'];   //效率保证金
			$this->status->yq_safe_money    = $result['data']['efficiency_deposit']; //安全保证金
			$this->status->yq_order_hour    = $result['data']['requirement_time'];   //订单要求完成时间
			$this->status->yq_created_at    = $result['data']['create_time'];        //订单创建时间
			$this->status->yq_sd_id         = $result['data']['receiver_id'];        //接单者id
			$this->status->yq_game_account  = $result['data']['game_account'];       //游戏账号
			$this->status->yq_game_password = $result['data']['game_password'];      //游戏密码
			$this->status->yq_cancel_type   = $result['data']['cancel_uid'];         //撤销类型
			$this->status->sync_at          = Carbon::now();

			$this->status->yq_part_train_fee = isset($result['data']['status_record']['part_train_fee']) ? $result['data']['status_record']['part_train_fee'] : 0;   //商家需支付赔付的代练费

			$this->status->yq_part_deposit = isset($result['data']['status_record']['part_train_fee']) ? $result['data']['status_record']['part_deposit'] : 0;    //打手需赔付的保证金
			// merchant_arbitration_money   : 0, 客服仲裁商家获得的费用
			// beater_arbitration_money     : 0, 商家仲裁打手获得的费用


			// 当出现撤销 仲裁 等异常情况时 表示 订单已经接手利用下面的函数来改变is_accept 的状态由0变为1（基本用不到）
			if (in_array($this->status->yq_order_status, [self::ORDER_STATUS_ING]) ||
				in_array($this->status->yq_order_status, [self::ORDER_STATUS_EXCEPTION]) ||
				in_array($this->status->yq_order_status, [self::ORDER_STATUS_OVER]) ||
				in_array($this->status->yq_order_status, [self::ORDER_STATUS_EXCEPTION]) ||
				in_array($this->status->yq_order_status, [self::ORDER_STATUS_CANCEL])
			) {
				// 已经接手
				$this->status->yq_is_accept = 1;
				$this->status->accepted_at  = $this->status->yq_assigned_at;
			}

			$this->status->save();
			$this->order->save();
			// 同步到平台主订单
			$this->syncToPlatformOrder();

			return true;
		}
		return false;
	}

	/**
	 * 获取截图接口
	 * @param int $page     页码
	 * @param int $pageSize 每页显示最大条数
	 * @return bool
	 */
	public function syncProgress($page = 1, $pageSize = 1000)
	{
		$param = [
			'OperationStatus' => '获取截图',
			'SerialNo'        => $this->status->yq_order_no,
			'page'            => $page,
			'pageSize'        => $pageSize,
		];
		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 1) {
			$this->syncDetail();
			if (isset($result['data'])) {
				return $result['data'];
			}
			else {
				return $this->setError('暂无截图');
			}
		}
		else {
			return $this->setError($result['alert']);
		}
	}

	/**
	 * 获取留言接口
	 * @param int $page     显示页数
	 * @param int $pageSize 每页显示最大条数
	 * @return bool
	 */
	public function syncMessage($page = 1, $pageSize = 500)
	{
		$param = [
			'OperationStatus' => '获取留言',
			'SerialNo'        => $this->status->yq_order_no,
			'page'            => $page,
			'pageSize'        => $pageSize,
		];
		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 1) {
			$this->syncDetail();
			if (isset($result['data'])) {
				return $result['data'];
			}
			else {
				return $this->setError('暂无留言');
			}
		}
		else {
			return $this->setError($result['alert']);
		}
	}

	/**
	 * 删除订单
	 * @return bool
	 */
	public function delete()
	{
		$mark = '[删除]';
		if (!$this->isPublish()) {
			return $this->setError($mark . '本地代练订单尚未发布， 不得删除');
		}
		if ($this->isDelete()) {
			return $this->setError($mark . '代练订单已经在服务器被删除');
		}

		$param = [
			'OperationStatus' => '删除订单',
			'SerialNo'        => $this->status->yq_order_no,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();


		if ($result['status'] == 1) {
			$this->status->delete();
			return true;
		}
		else {
			$error                       = $result['alert'];
			$this->status->yq_pt_message = $error;
			$this->status->yq_is_error   = 1;
			$this->status->yq_is_publish = 1;
			$this->status->yq_is_delete  = 0;
			$this->status->save();
			return $this->setError($result['alert']);
		}

	}

	/**
	 * 发单者对撤单的操作
	 * @param        $flag       用来判断是撤销那种类型
	 * @param        $pub_pay    发单者赔付
	 * @param        $sd_pay     接单者赔付
	 * @param string $reason     理由
	 * @return bool
	 */
	public function pubCancel($flag, $pub_pay, $sd_pay, $reason = '')
	{
		switch ($flag) {
			case Yq::PUB_CANCEL_CANCEL:
				$param = [
					'OperationStatus' => '取消申请撤销',
					'SerialNo'        => $this->status->yq_order_no,
				];
				break;
			case Yq::PUB_CANCEL_AGREE:
				$param = [
					'OperationStatus' => '同意撤销',
					'SerialNo'        => $this->status->yq_order_no,
					'password'        => $this->appPayword,
				];
				break;
			case Yq::PUB_CANCEL_APPLY:
			default :
				$param = [
					'OperationStatus' => '申请撤销',
					'SerialNo'        => $this->status->yq_order_no,
					'part_train_fee'  => $pub_pay,  //商家需支付的代练费
					'part_deposit'    => $sd_pay,   //打手需赔付的保证金
					'content'         => $reason,   //撤销原因
					'password'        => $this->appPayword,
				];
				if ($param['part_train_fee'] > $this->order->order_price) {
					return $this->setError('支付代练费不得大于' . $this->order->order_price);
				}
				if ($param['part_deposit'] > bcadd($this->order->order_safe_money, $this->order->order_speed_money, 2)) {
					return $this->setError('赔偿保证金不得大于' . bcadd($this->order->order_safe_money, $this->order->order_speed_money, 2));
				}
				break;
		}

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}

		$result = $this->req->getResp();

		if ($result['status'] == 1) {
			$this->syncDetail();
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}

	}


	/**
	 * 申请客服介入/取消客服介入
	 * @param $kf        仲裁的类型
	 * @param $reason    理由
	 * @return bool
	 */
	public function kf($kf, $reason = '')
	{
		if ($kf) {
			$param = [
				'OperationStatus' => '申请仲裁',
				'SerialNo'        => $this->status->yq_order_no,
				'content'         => $reason,
			];
		}
		else {
			$param = [
				'OperationStatus' => '取消申请仲裁',
				'SerialNo'        => $this->status->yq_order_no,
			];
		}
		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($kf) {
			if ($result['status'] == 1) {
				// 完单
				$PlatformOrder = new ActionPlatformOrder();
				$PlatformOrder->kf($this->status->order_id, $this->handlePam->account_id);
				$this->status->yq_is_kf = 1;
				$this->status->save();
				return true;
			}
			else {
				$error = $result['alert'];
				return $this->setError($error);
			}
		}
		else {
			if ($result['status'] == 1) {
				// 完单
				$PlatformOrder = new ActionPlatformOrder();
				$PlatformOrder->unkf($this->status->order_id, $this->handlePam->account_id);
				$this->status->yq_is_kf = 0;
				$this->status->save();
				return true;
			}
			else {
				$error = $result['alert'];
				return $this->setError($error);
			}
		}
	}

	/**
	 * 更新订单进度
	 * @param                                                     $message    上传截图的说明
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image      需要上传的图片
	 * @return bool
	 */
	public function progress($message, $image = null)
	{
		$url = '';
		if ($image) {
			if (!$this->getOss()) {
				return false;
			}
			$oss             = $this->req->getResp();
			$accessKeyId     = $oss['data']['accessKeyId'];
			$accessKeySecret = $oss['data']['accessKeySecret'];
			$securityToken   = $oss['data']['securityToken'];
			$bucket          = $oss['data']['bucket']; //flashfish
			$endpoint        = 'http://oss-cn-hangzhou.aliyuncs.com';  //$prefix_url
			$ossClient       = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false, $securityToken);
			$time            = time();
			$imageName       = $image->getClientOriginalName();
			try {
				$ossClient->uploadFile($bucket, $time . $imageName, $image->move(public_path('uploads/temp'), $time . $imageName));
			} catch (\Exception $e) {
				return $this->setError($e->getMessage());
			}
			$url = $time . $imageName;
		}

		if (!$url) {
			return $this->setError('请上传图片');
		}
		$param = [
			'OperationStatus' => '上传截图',
			'SerialNo'        => $this->status->yq_order_no,
			'path'            => $url,
			'reason'          => $message,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 1) {
			return true;
		}
		else {
			$error = $result['alert'];
			return $this->setError($error);
		}
	}

	/**
	 * 获取OSS上传凭证
	 */
	private function getOss()
	{
		$param = [
			'OperationStatus' => '获取凭证',
			'SerialNo'        => $this->status->yq_order_no,
		];
		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 1) {
			if (isset($result['data']['accessKeyId'])) {
				return true;
			}
			else {
				$error = $result['alert'];
				return $this->setError($error);
			}
		}
	}

	/**
	 *  验收完成
	 * @return bool
	 */
	public function overOrder()
	{
		$param = [
			'OperationStatus' => '验收完成',
			'SerialNo'        => $this->status->yq_order_no,
			'password'        => $this->appPayword,
		];
		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 1) {
			$PlatformOrder = new ActionPlatformOrder();
			$PlatformOrder->over($this->status->order_id, $this->handlePam->account_id);
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}
	}

	/**
	 * 锁定订单
	 * @param string $reason
	 * @return bool
	 */
	public function lock()
	{
		$param = [
			'OperationStatus' => '锁定账号',
			'SerialNo'        => $this->status->yq_order_no,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 1) {
			$this->syncDetail();
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}
	}


	/**
	 * @return bool
	 * 解锁接口
	 */
	public function unlock()
	{
		$param = [
			'OperationStatus' => '取消锁定',
			'SerialNo'        => $this->status->yq_order_no,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 1) {
			$this->status->yq_is_lock = 0;
			$this->syncDetail();
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}
	}

	/**
	 * 对订单进行留言
	 * @param $note     留言内容
	 * @return bool
	 */
	public function message($note)
	{
		$param = [
			'OperationStatus' => '发送留言',
			'SerialNo'        => $this->status->yq_order_no,
			'comment'         => $note,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 1) {
			$this->syncDetail();
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}
	}

	/**
	 * 补时
	 * @param $hour
	 * @return bool
	 */
	public function addTime($hour)
	{
		$param = [
			'OperationStatus' => '订单加时',
			'SerialNo'        => $this->status->yq_order_no,
			'add_time'        => $hour, //单位小时
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 1) {
			$this->syncDetail();
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}

	}

	/**
	 * 补款
	 * @param $money
	 * @return bool
	 */
	public function addMoney($money)
	{
		$param = [
			'OperationStatus' => '订单加价',
			'SerialNo'        => $this->status->yq_order_no,
			'password'        => $this->appPayword,
			'add_price'       => $money,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 1) {
			$this->syncDetail();
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}
	}


	/**
	 * 修改游戏密码
	 * @param $password
	 * @param game_account
	 * @return bool
	 */
	public function modifyGamePwd($password)
	{
		$param = [
			'OperationStatus' => '修改游戏密码',
			'SerialNo'        => $this->status->yq_order_no,
			'pwd'             => $password,
		];

		if (!$this->req->make('manage', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 1) {
			$this->syncDetail();
			return true;
		}
		else {
			return $this->setError($result['alert']);
		}
	}

	/**
	 * @param $flag
	 * @param $message
	 */
	public function star($flag, $message)
	{
		$this->status->yq_is_star       = $flag;
		$this->status->yq_star_messages = $message;
	}

	/**
	 * 发单者取消描述
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvPubCancel($key = null)
	{
		$desc = [
			self::PUB_CANCEL_CANCEL => '发单者取消撤销',
			self::PUB_CANCEL_AGREE  => '发单者同意撤销',
			self::PUB_CANCEL_APPLY  => '发单者申请撤销',
			self::PUB_CANCEL_REJECT => '发单者拒绝撤销',
		];
		return kv($desc, $key);
	}

	/**
	 * 申请客服介入/取消客服介入
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvKf($key = null)
	{
		$desc = [
			self::KF_KF   => '申请客服介入',
			self::KF_UNKF => '取消客服介入',
		];
		return kv($desc, $key);
	}


	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public
	static function kvOrderLock($key = null)
	{
		$desc = [
			self::ORDER_LOCK_LOCK   => '锁定',
			self::ORDER_LOCK_UNLOCK => '未锁定',
		];
		return kv($desc, $key);
	}


	/**
	 * 是否公共的描述
	 * @param null $key
	 * @return string
	 */
	public static function kvIsPublic($key = null)
	{
		$desc = [
			1 => '公共',
			0 => '指定用户/组',
		];
		return kv($desc, $key);
	}

	/**
	 * 取消类型的描述
	 * @param null $key
	 * @return string
	 */
	public static function kvCancelType($key = null)
	{
		$desc = [
			self::CANCEL_TYPE_NONE     => '无撤单',
			self::CANCEL_TYPE_SD_DEAL  => '接单者',
			self::CANCEL_TYPE_PUB_DEAL => '发单者',
			self::CANCEL_TYPE_KF       => '客服介入',
		];
		return kv($desc, $key);
	}


	/**
	 * 取消状态描述
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvCancelStatus($key = null)
	{
		$desc = [
			self::CANCEL_STATUS_DONE   => '完成',
			self::CANCEL_STATUS_NONE   => '无',
			self::CANCEL_STATUS_ING    => '进行中',
			self::CANCEL_STATUS_REJECT => '拒绝',
		];
		return kv($desc, $key);
	}


	public static function kvKfStatus($key = null)
	{
		$desc = [
			self::KF_STATUS_NONE => '无须客服处理',
			self::KF_STATUS_DONE => '客服处理完成',
			self::KF_STATUS_WAIT => '等待客服处理',
			self::KF_STATUS_ING  => '客服处理中',
		];
		return kv($desc, $key);
	}


	/**
	 * 订单状态
	 * @param null $key
	 * @return string
	 */
	public static function kvOrderStatus($key = null)
	{
		$desc = [
			self::ORDER_CANCEL_OVER         => '撤销完成',
			self::ORDER_STATUS_LOCKAFTERING => '代练中',
			self::CANCEL_STATUS_330         => '锁定账号中',
			self::CANCEL_STATUS_431         => '打手申请撤销中',
			self::ORDER_STATUS_CANCELING    => '接单者申请撤销中',
			self::CANCEL_STATUS_TREATY      => '发单者申请撤销中',
			self::ORDER_STATUS_CREATE       => '订单创建',
			self::ORDER_STATUS_PUBLISH      => '待接单',
			self::ORDER_STATUS_ING          => '代练中',
			self::ORDER_STATUS_CANCEL       => '退单',
			self::ORDER_STATUS_EXAMINE      => '等待验收',
			self::ORDER_STATUS_OVER         => '订单完成',
			self::ORDER_STATUS_EXCEPTION    => '订单异常',
			self::ORDER_STATUS_DELETE       => '订单已删除',
		];
		return kv($desc, $key);
	}

	/**
	 * 异常类型说明
	 * @param $key
	 * @return string
	 */
	public static function kvExceptionType($key = null)
	{
		$desc = [
			self::EXCEPTION_TYPE_ACCOUNT_ERROR => '账号错误',
			self::EXCEPTION_TYPE_OTHER         => '其他错误',
		];
		return kv($desc, $key);
	}

	/**
	 * 锁定/解锁
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvLock($key = null)
	{
		$desc = [
			self::LOCK_LOCK   => '锁定',
			self::LOCK_UNLOCK => '解锁',
		];
		return kv($desc, $key);
	}

	/**
	 * 获取订单的详细
	 * @return mixed
	 */
	private function detail()
	{
		if (!$this->status->yq_order_no) {
			return $this->setError('[获取详情]本地不存在17代练订单号， 无法获取详情');
		}

		if (!$this->req->make('manage', [
			'OperationStatus' => '获取订单详情',
			'SerialNo'        => $this->status->yq_order_no,
		])
		) {
			return $this->setError($this->req->getError());
		}
		return $this->req->getResp();
	}


	private function staticsPlatformAccountOrder()
	{
		dispatch(new StaticsYqOrderNum($this->platformAccount, $this->platform));
	}


	/**
	 * 是否已经删除订单
	 */
	private function isDelete()
	{
		if ($this->status->yq_is_publish && $this->status->yq_is_delete) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 订单是否发布过
	 * @return bool|mixed
	 */
	private function isPublish()
	{
		return $this->status->yq_is_publish;
	}


	/**
	 * 同步信息到主订单
	 */
	private function syncToPlatformOrder()
	{

		if (!$this->order->accept_id) {
			return false;
		}
		if ($this->order->accept_id != $this->status->id) {
			return false;
		}
		$old_order = clone $this->order;

		switch ($this->status->yq_order_status) {
			case 300:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_ING;  //代练中
				break;
			case 600:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_EXCEPTION;  //异常中
				break;
			case 400:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_ING; //锁定之后或者异常状态之后重新开始代练
				break;
			case 430:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_LOCK;//商家锁定中
				break;
			case 500:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_EXAMINE; //订单已经完成等待验收中
				break;
			case 410:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_CANCEL;  //商家撤销订单
				break;
			case 431:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_QUASH; //打手撤单中
				break;
			case 700:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_OVER;  //订单完成
				break;
			case 740:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_CANCELOVER;//已经撤销
				break;
		}

		// 已经接单, 同步到主订单
		$this->order->updated_at        = $this->status->updated_at;
		$this->order->is_exception      = $this->status->yq_is_exception;
		$this->order->cancel_status     = $this->status->yq_cancel_status;
		$this->order->cancel_type       = $this->status->yq_cancel_type;
		$this->order->kf_status         = $this->status->yq_kf_status;
		$this->order->assigned_at       = $this->status->yq_assigned_at;
		$this->order->overed_at         = $this->status->yq_overed_at;
		$this->order->order_lock        = $this->status->yq_is_lock;
		$this->order->sd_uid            = $this->status->yq_sd_id;
		$this->order->sd_username       = $this->status->yq_sd_name;
		$this->order->sd_qq             = $this->status->yq_sd_qq;
		$this->order->sd_mobile         = $this->status->yq_sd_mobile;
		$this->order->order_price       = ($this->status->yq_order_price) + ($this->status->yq_add_price);
		$this->order->order_hours       = $this->status->yq_order_hour;
		$this->order->is_star           = $this->status->yq_is_star;
		$this->order->pt_accept_sync_at = Carbon::now();
		$this->order->save();

		$this->sendMessage($old_order, $this->order);

		return true;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getReqResp()
	{
		return $this->req->getReqResp();
	}


	public function getReqLog()
	{
		return $this->req->getReqLog();
	}

	/**
	 * @param null $key
	 * @return array|string
	 */
	public static function kvStar($key = null)
	{
		$desc = [
			self::STAR_BAD    => '很差',
			self::STAR_NORMAL => '一般',
			self::STAR_GOOD   => '满意',
		];
		return kv($desc, $key);
	}


	/**
	 * @param $pay_word
	 * @return bool|string
	 * 支付密码的RSA加密
	 */
	private function rsaCalc($pay_word)
	{
		if (env('APP_ENV', 'local') === 'production') {
			$pemFile = base_path('resources/key/17dl/production_public.pem');
		}
		else {
			$pemFile = base_path('resources/key/17dl/dev_public.pem');//这个指的是  你给的加密那个长串字符
		}
		if (!file_exists($pemFile)) {
			return $this->setError('公钥文件 `' . $pemFile . '` 不存在');
		}
		$publicKey = openssl_pkey_get_public(file_get_contents($pemFile));

		$encrypted = '';
		// 公钥加密
		openssl_public_encrypt($pay_word, $encrypted, $publicKey);
		$encrypted = base64_encode($encrypted);// base64传输
		return $encrypted;
	}
}

