<?php namespace Order\Application\Platform;

use App\Jobs\Platform\StaticsYiOrderNum;
use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Lemon\Dailian\Application\Platform\Request\YiReq;
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
class Yi implements Platform
{

	use BaseTraits, DispatchesJobs, PlatformTraits;

	const STAR_GOOD   = 'good';
	const STAR_NORMAL = 'normal';
	const STAR_BAD    = 'bad';

	const LOCK_LOCK   = 1;
	const LOCK_UNLOCK = 0;

	const PUB_CANCEL_APPLY  = 0;
	const PUB_CANCEL_CANCEL = 1;
	const PUB_CANCEL_AGREE  = 2;
	const PUB_CANCEL_REJECT = 3;

	const ORDER_STATUS_ING       = 'ing';        // 代练进行中
	const ORDER_STATUS_CREATE    = 'create';     // 等待付款
	const ORDER_STATUS_PUBLISH   = 'publish';    // 等待代练
	const ORDER_STATUS_EXAMINE   = 'examine';    // 等待验收
	const ORDER_STATUS_OVER      = 'over';       // 订单完成
	const ORDER_STATUS_EXCEPTION = 'exception';  // 订单异常
	const ORDER_STATUS_CANCEL    = 'cancel';     // 撤销,退单
	const ORDER_STATUS_QUASH     = 'quash';      // 撤销,退单
	const ORDER_STATUS_DELETE    = 'delete';     // 删除

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

	private $platform = PlatformAccount::PLATFORM_YI;


	/** @type  PlatformAccount */
	private $platformAccount;

	/** @type  PlatformOrder 平台订单 */
	private $order;

	/** @type  PamAccount */
	private $handlePam;

	/** @var YiReq */
	private $req;

	/** @var string */
	private $appPayword;


	/** @type PlatformStatus */
	private $status;


	/**
	 * Yi constructor.
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
		$this->req        = new YiReq($pt_account_id);
		$this->appPayword = $this->platformAccount->yi_payword;

		$this->status = $this->createStatus();
		$this->syncDetail();

	}


	/**
	 * 发布订单, 需要验证支付密码
	 * @return bool
	 */
	public function publish()
	{

		if (!$this->checkPublish()) {
			return false;
		}

		$param = [
			'type_id'         => $this->order->type_id,    // 段位
			'order_title'     => $this->order->order_title,  // 订单标题，最大长度50
			'payword'         => $this->appPayword,    //支付密码
			'order_price'     => $this->order->order_price,  // 订单价格
			'safe_money'      => $this->order->order_safe_money,  //  安全保证金，支持两位小数。
			'speed_money'     => $this->order->order_speed_money,  // 效率保证金，支持两位小数。
			'order_hours'     => $this->order->order_hours,  //   单位小时
			'game_account'    => $this->order->game_account,  // 账号
			'game_pwd'        => $this->order->game_pwd,  // 密码
			'game_actor'      => $this->order->game_actor,  // 游戏角色名称
			'order_content'   => $this->order->order_content,  // 代练要求，长度不能超过600个字符
			'order_current'   => $this->order->order_current,  // 代练前游戏信息
			'order_contact'   => $this->platformAccount->contact,  // 订单联系人
			'order_mobile'    => $this->platformAccount->mobile,  // 订单联系信息
			'order_qq'        => $this->order->order_qq,  // 订单qq
			'source_id'       => $this->order->source_id,  // 来源id
			'order_number'    => $this->order->order_get_in_number,  // 来源的订单号
			'order_note'      => '',  // 订单备注
			'get_in_price'    => $this->order->order_get_in_price,  // 接单价格
			// 'subid'        => 0,  // 子账号ID，可为空0
			'order_add_price' => $this->order->order_add_price, //奖金

		];
		if (strlen(GameServer::yiServerCode($this->order->server_id)) == 6) {
			$param['plat_yidailian'] = GameServer::yiServerCode($this->order->server_id);
		} else if (strlen(GameServer::yiServerCode($this->order->server_id)) == 12) {
			$param['server_code'] = GameServer::yiServerCode($this->order->server_id);
		}
		if (!isset($param['plat_yidailian']) && !isset($param['server_code'])) {
			return $this->setError('服务器编码不能为空');
		}

		$validator = \Validator::make($param, [
			'type_id'       => 'required',
			'order_title'   => 'required',
			'order_price'   => 'required',
			'payword'       => 'required',
			'safe_money'    => 'required|numeric',
			'speed_money'   => 'required|numeric',
			'order_hours'   => 'required|numeric',
			'game_account'  => 'required',
			'game_pwd'      => 'required',
			'order_content' => 'required',
			'order_mobile'  => 'required',
			'get_in_price'  => 'required',
			'order_number'  => 'required',
		], [
			'type_id.required'       => '游戏段位不能为空',
			'order_title.required'   => '订单标题不能为空',
			'order_price.required'   => '发单价格不能为空',
			'payword.required'       => '支付密码不能为空',
			'safe_money.required'    => '安全保证金不能为空',
			'speed_money.required'   => '效率保证金不能为空',
			'order_hours.required'   => '订单时限不能为空',
			'game_account.required'  => '游戏账号不能为空',
			'game_pwd.required'      => '游戏密码不能为空',
			'game_actor.required'    => '角色名不能为空',
			'order_mobile.required'  => '手机号不能为空',
			'order_content.required' => '代练要求内容不能为空',
			'get_in_price.required'  => '接单价格不能为空',
			'order_number.required'  => '来源订单号不能为空',
		]);


		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}


		if (!$this->req->make('create', $param)) {
			return $this->setError($this->req->getError());
		}

		$this->status->pt_account_note = $this->platformAccount->note;

		$resp = $this->req->getResp();

		if ($resp['status'] == 0) {
			$success = '发布到易代练成功';
			// 更新 订单号
			$this->status->yi_order_no   = $resp['data']['order_no'];
			$this->status->yi_pt_message = $success;
			$this->status->yi_is_error   = 0;
			$this->status->yi_is_delete  = 0;
			$this->status->yi_is_publish = 1;
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
		} else {
			$error = $resp['message'];
			// 记录同步订单错误
			$this->status->yi_is_delete  = 0;
			$this->status->yi_is_publish = 0;
			$this->status->yi_is_error   = 1;
			$this->status->yi_pt_message = $error;
			$this->status->save();
			return $this->setError($error);
		}
	}


	public function rePublish()
	{
		$this->delete();
		$this->status = $this->createStatus();
		return $this->publish();
	}


	/**
	 * 订单详细
	 * @return bool
	 */
	public function syncDetail()
	{
		$result = $this->detail();
		if (!$result) {
			return false;
		}
		if ($result['status'] == 0) {
			// success
			if (isset($result['data']['assigned_at'])) {
				$this->status->yi_assigned_at = $result['data']['assigned_at'];
				$this->status->yi_is_accept   = $result['data']['sd_account_id'] ? 1 : 0;
				$this->status->yi_order_no    = $result['data']['order_no'];
				$this->status->yi_sd_uid      = $result['data']['sd_account_id'];
				// left_hour
				$hourDiff = Carbon::createFromFormat('Y-m-d H:i:s', $result['data']['assigned_at'])->diffInHours(Carbon::now());

				$this->status->yi_left_hour = (($result['data']['order_hours'] - $hourDiff) > 0) ? $result['data']['order_hours'] - $hourDiff : 0;

				/** @type PamAccount $user */
				$this->status->yi_sd_username     = $result['data']['sd_account_name'];
				$this->status->yi_sd_mobile       = $result['data']['sd_mobile'];
				$this->status->yi_sd_qq           = $result['data']['sd_qq'];
				$this->status->yi_order_add_price = $result['data']['order_add_price'];
			}

			if (isset($result['data']['ended_at'])) {
				$this->status->yi_is_over  = 1;
				$this->status->yi_ended_at = $result['data']['ended_at'];
			}
			/* 删除订单状态同步 (Mark Zhao) ----------- */
			if (isset($result['data']['order_status']) && $result['data']['order_status'] == self::ORDER_STATUS_DELETE) {
				$this->status->yi_is_delete  = 1;
				$this->status->yi_pt_message = '订单已删除!';
				$this->status->deleted_at    = $result['data']['deleted_at'];
			}


			/* 同步发单人信息 (Mark Zhao) ----------- */
			/** @type PlatformAccount $publisher */
			if (isset($result['data']['publisher_id'])) {
				$publisher = PlatformAccount::where('yi_userid', $result['data']['publisher_id'])->first();
			}

			if (!isset($publisher)) {
				$this->status->yi_pub_username = '';
			} else {
				$this->status->yi_pub_uid      = $publisher->yi_userid;
				$this->status->yi_pub_username = $publisher->yi_nickname;
				$this->status->yi_pub_mobile   = $publisher->mobile;
				$this->status->yi_pub_qq       = $publisher->qq;
				$this->status->yi_pub_contact  = $publisher->contact;
			}

			$this->status->yi_is_star          = $result['data']['is_pub_star'] == 'Y' ? 1 : 0;
			$this->status->yi_cancel_type      = $result['data']['cancel_type'];
			$this->status->yi_kf_status        = $result['data']['kf_status'];
			$this->status->yi_order_price      = $result['data']['order_price'];
			$this->status->yi_order_hour       = $result['data']['order_hours'];
			$this->status->yi_exception_status = $result['data']['exception_status'];
			$this->status->yi_exception_type   = $result['data']['exception_type'];
			$this->status->yi_is_public        = $result['data']['is_public'] == 'Y' ? 1 : 0;
			$this->status->yi_cancel_status    = $result['data']['cancel_status'];
			$this->status->yi_order_status     = $result['data']['order_status'];
			$this->status->yi_is_exception     = $result['data']['is_exception'] == 'Y' ? 1 : 0;
			$this->status->yi_is_lock          = $result['data']['order_lock'] == 'Y' ? 1 : 0;
			$this->status->yi_pub_pay          = $result['data']['pub_pay'];
			$this->status->yi_sd_pay           = $result['data']['sd_pay'];
			$this->status->yi_created_at       = $result['data']['created_at'];
			$this->status->yi_overed_at        = $result['data']['overed_at'];
			$this->status->yi_ended_at         = $result['data']['ended_at'];
			$this->status->yi_order_add_price  = $result['data']['order_add_price'];
			$this->status->sync_at             = Carbon::now();


			if (in_array($this->status->yi_order_status, [self::ORDER_STATUS_QUASH]) ||
				in_array($this->status->yi_order_status, [self::ORDER_STATUS_ING]) ||
				in_array($this->status->yi_order_status, [self::ORDER_STATUS_EXCEPTION]) ||
				in_array($this->status->yi_order_status, [self::ORDER_STATUS_OVER]) ||
				in_array($this->status->yi_order_status, [self::ORDER_STATUS_EXCEPTION]) ||
				in_array($this->status->yi_order_status, [self::ORDER_STATUS_CANCEL])
			) {
				// 已经接手
				$this->status->yi_is_accept = 1;
				$this->status->accepted_at  = $this->status->yi_assigned_at;
			}
			$this->status->save();
			$this->order->save();
			// 同步到平台主订单
			$this->syncToPlatformOrder();


			return true;
		}
		/*
		else {

			$this->status->yi_is_publish = 1;
			$this->status->yi_is_delete  = 1;
			$this->status->yi_order_no   = '';
			$this->status->yi_pt_message = $result['message'];
			$this->status->sync_at       = Carbon::now();
			$this->status->save();

			return $this->setError($result['message']);
		}
		*/
		return false;
	}

	public function syncProgress()
	{
		$param = [
			'order_no' => $this->status->yi_order_no,
		];
		if (!$this->req->make('gain_picture', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 0) {
			$this->syncDetail();
			if ($result['data']['lists']) {
				return $result['data']['lists'];
			} else {
				return $this->setError('暂无截图');
			}

		} else {
			return $this->setError($result['message']);
		}
	}

	public function syncMessage()
	{
		$param = [
			'order_no' => $this->status->yi_order_no,
		];
		if (!$this->req->make('left_word', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 0) {
			$this->syncDetail();
			return $result['data']['lists'];
		} else {
			return $this->setError($result['message']);
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
			return $this->setError($mark . '本地易代练订单尚未发布， 不得删除');
		}
		if ($this->isDelete()) {
			return $this->setError($mark . '易代练订单已经在服务器被删除');
		}

		$param = [
			'order_no' => $this->status->yi_order_no,
		];

		if (!$this->req->make('del', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();


		if ($result['status'] == 0) {
			$this->status->delete();
			return true;
		} else {
			$error                       = $result['message'];
			$this->status->yi_pt_message = $error;
			$this->status->yi_is_error   = 1;
			$this->status->yi_is_publish = 1;
			$this->status->yi_is_delete  = 0;
			$this->status->save();
			return $this->setError($result['message']);
		}

	}

	/**
	 * 发单者对撤单的操作
	 * @param        $flag
	 * @param        $pub_pay
	 * @param        $sd_pay
	 * @param string $reason
	 * @return bool
	 */
	public function pubCancel($flag, $pub_pay, $sd_pay, $reason = '')
	{
		$param = [
			'order_no'      => $this->status->yi_order_no,
			'publisher_pay' => $pub_pay,
			'soldier_pay'   => $sd_pay,
			'reason'        => $reason,
			'flag'          => $flag,
			'pay_pwd'       => $this->appPayword,
		];
		if ($param['publisher_pay'] > $this->order->order_price) {
			return $this->setError('支付代练费不得大于' . $this->order->order_price);
		}
		if ($param['soldier_pay'] > bcadd($this->order->order_safe_money, $this->order->order_speed_money, 2)) {
			return $this->setError('赔偿保证金不得大于' . bcadd($this->order->order_safe_money, $this->order->order_speed_money, 2));
		}
		if (!$this->req->make('cancel', $param)) {
			return $this->setError($this->req->getError());
		}

		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
		}
	}

	/**
	 * 更新订单进度
	 * @param                                                     $note
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $picture
	 * @return bool
	 */
	public function progress($note, $picture = null)
	{
		$url = '';
		if ($picture) {
			if (!$this->getOss()) {
				return false;
			}
			$oss = $this->req->getResp();
			$endpoint        = $oss['data']['prefix_url'];          //组合上传的URL
			$accessKeyId     = $oss['data']['access_key_id'];       //access_key_id
			$accessKeySecret = $oss['data']['access_key_secret'];   //access_key_secret
			$securityToken   = $oss['data']['security_token'];      //安全token
			$bucket_name     = $oss['data']['bucket'];              //存储名称
			$directory       = $oss['data']['directory'];           //允许上传的目录
			$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, true, $securityToken);
			$time      = time();
			$imageName = $picture->getClientOriginalName();
			\Log::debug($imageName);
			try {
				$ossClient->uploadFile($bucket_name, $directory . $time . $imageName, $picture->move(public_path('uploads/temp'), $time . $imageName));
			} catch (\Exception $e) {
				return $this->setError($e->getMessage());
			}
			$url = $endpoint . '/' . $directory . $time . $imageName;
		}
		if (!$url) {
			return $this->setError('请上传图片');
		}
		$param = [
			'order_no'   => $this->status->yi_order_no,
			'pic_desc'   => $note,
			'pic_screen' => $url
		];

		if (!$this->req->make('picture', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
		}
	}


	/**
	 * 临时获取oss凭证
	 * @return bool
	 */
	private function getOss()
	{
		if (!$this->req->make('temp_oss')) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['status'] == 0) {
			return true;
		} else {
			$error = $result['message'];
			return $this->setError($error);
		}
	}
	/**
	 * 订单完结
	 * @return bool
	 */
	public function overOrder()
	{
		$param = [
			'order_no' => $this->status->yi_order_no,
			'payword'  => $this->appPayword,
		];
		if (!$this->req->make('over', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$PlatformOrder = new ActionPlatformOrder();
			$PlatformOrder->over($this->status->order_id, $this->handlePam->account_id);
			return true;
		} else {
			return $this->setError($result['message']);
		}

	}

	/**
	 * 锁定订单
	 * @param string $reason
	 * @return bool
	 */
	public function lock($reason = '')
	{
		$param = [
			'order_no' => $this->status->yi_order_no,
			'reason'   => $reason,
		];


		if (!$this->req->make('lock', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
		}
	}

	/**
	 * 解锁订单
	 * @param string $reason
	 * @return bool
	 */
	public function unlock($reason = '')
	{
		$param = [
			'order_no' => $this->status->yi_order_no,
			'reason'   => $reason,
		];

		if (!$this->req->make('unlock', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();


		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
		}

	}


	/**
	 * 对订单进行留言
	 * @param $note
	 * @return bool
	 */
	public function message($note)
	{
		$param = [
			'order_no'    => $this->status->yi_order_no,
			'log_content' => $note,
		];

		if (!$this->req->make('send_message', $param)) {
			return $this->setError($this->req->getError());
		}

		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
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
			'order_no' => $this->status->yi_order_no,
			'hour'     => $hour,
		];

		if (!$this->req->make('add_time', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
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
			'order_no' => $this->status->yi_order_no,
			'money'    => $money,
			'payword'  => $this->appPayword,
		];

		if (!$this->req->make('add_money', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();


		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
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
			'order_no' => $this->status->yi_order_no,
			'game_pwd' => $password,
		];

		if (!$this->req->make('change_account', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
		}
	}

	/**
	 * 申请客服
	 */
	public function kf()
	{
		$param = [
			'order_no' => $this->status->yi_order_no,
		];

		if (!$this->req->make('kf', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
		}
	}

	/**
	 * 评价订单
	 * @param        $type
	 * @param string $message
	 * @return bool
	 */

	public function star($type, $message = '')
	{
		$param = [
			'order_no' => $this->status->yi_order_no,
			'star'     => $type,
			'comment'  => $message,
		];

		if (!$this->req->make('star', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['status'] == 0) {
			$this->syncDetail();
			return true;
		} else {
			return $this->setError($result['message']);
		}
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
	 * 评价
	 * @param null $key
	 * @return array|mixed
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
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvOrderLock($key = null)
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
			self::ORDER_STATUS_CREATE    => '订单创建',
			self::ORDER_STATUS_PUBLISH   => '待接单',
			self::ORDER_STATUS_ING       => '代练中',
			self::ORDER_STATUS_CANCEL    => '退单',
			self::ORDER_STATUS_EXAMINE   => '等待验收',
			self::ORDER_STATUS_OVER      => '订单完成',
			self::ORDER_STATUS_EXCEPTION => '订单异常',
			self::ORDER_STATUS_QUASH     => '已撤销',
			self::ORDER_STATUS_DELETE    => '订单已删除',
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
		if (!$this->status->yi_order_no) {
			return $this->setError('[获取详情]本地不存在易代练订单号， 无法获取详情');
		}


		if (!$this->req->make('detail', [
			'order_no' => $this->status->yi_order_no,
		])
		) {
			return $this->setError($this->req->getError());
		}
		return $this->req->getResp();
	}


	private function staticsPlatformAccountOrder()
	{
		dispatch(new StaticsYiOrderNum($this->platformAccount, $this->platform));
	}


	/**
	 * 是否已经删除订单
	 */
	private function isDelete()
	{
		if ($this->status->yi_is_publish && $this->status->yi_is_delete) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 订单是否发布过
	 * @return bool|mixed
	 */
	private function isPublish()
	{
		return $this->status->yi_is_publish;
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

		// 已经接单, 同步到主订单
		$this->order->updated_at        = $this->status->updated_at;
		$this->order->order_status      = $this->status->yi_order_status;
		$this->order->is_exception      = $this->status->yi_is_exception;
		$this->order->cancel_status     = $this->status->yi_cancel_status;
		$this->order->cancel_type       = $this->status->yi_cancel_type;
		$this->order->kf_status         = $this->status->yi_kf_status;
		$this->order->assigned_at       = $this->status->yi_assigned_at;
		$this->order->overed_at         = $this->status->yi_overed_at;
		$this->order->order_lock        = $this->status->yi_is_lock;
		$this->order->sd_uid            = $this->status->yi_sd_uid;
		$this->order->sd_username       = $this->status->yi_sd_username;
		$this->order->sd_qq             = $this->status->yi_sd_qq;
		$this->order->sd_mobile         = $this->status->yi_sd_mobile;
		$this->order->order_price       = $this->status->yi_order_price;
		$this->order->order_hours       = $this->status->yi_order_hour;
		$this->order->is_star           = $this->status->yi_is_star;
		$this->order->pt_accept_sync_at = Carbon::now();
		$this->order->save();

		$this->sendMessage($old_order, $this->order);

		return true;
	}

	public function getStatus()
	{
		return $this->status;
	}
}

