<?php namespace Order\Application\Platform;

use App\Jobs\Platform\StaticsTongOrderNum;
use App\Lemon\Dailian\Action\ActionPlatformOrder;
use App\Lemon\Dailian\Application\Platform\Request\TongReq;
use App\Lemon\Dailian\Application\Platform\Traits\BaseTraits;
use App\Lemon\Dailian\Contracts\Platform;
use App\Lemon\Dailian\Traits\PlatformTraits;
use App\Lemon\Repositories\Sour\LmUtil;
use App\Models\GameServer;
use App\Models\PamAccount;
use App\Models\PlatformAccount;
use App\Models\PlatformOrder;
use App\Models\PlatformStatus;
use App\Models\PlatformTongLog;
use Carbon\Carbon;
use OSS\OssClient;

class Tong implements Platform
{

	use BaseTraits, PlatformTraits;

	const PUB_CANCEL_APPLY  = 0;
	const PUB_CANCEL_CANCEL = 1;
	const PUB_CANCEL_AGREE  = 2;

	const LOCK_LOCK   = 1;
	const LOCK_UNLOCK = 0;

	const STAR_BEST   = 10;
	const STAR_GOOD   = 11;
	const STAR_NORMAL = 12;
	const STAR_BAD    = 13;

	const ORDER_STATUS_TEMP      = 10;
	const ORDER_STATUS_PUBLISH   = 11;
	const ORDER_STATUS_ING       = 12;
	const ORDER_STATUS_EXAMINE   = 13;
	const ORDER_STATUS_EXCEPTION = 14;
	const ORDER_STATUS_LOCK      = 15;
	const ORDER_STATUS_CANCEL    = 16;
	const ORDER_STATUS_OVER      = 17;

	const CANCEL_STATUS_NONE         = 10;
	const CANCEL_STATUS_TREATY       = 11;
	const CANCEL_STATUS_HANDLED      = 12;
	const CANCEL_STATUS_CANCEL       = 13;
	const CANCEL_STATUS_KF_IN        = 14;
	const CANCEL_STATUS_KF_OVER      = 15;
	const CANCEL_STATUS_FORCE_HANDLE = 16;


	const IS_PUB_YES = 1;
	const IS_PUB_NO  = 0;

	const MODIFY_MONEY     = 0;
	const MODIFY_TIME      = 1;
	const MODIFY_GAME_WORD = 2;

	/** @type PlatformStatus */
	private $status;

	private $platform = PlatformAccount::PLATFORM_TONG;

	/** @type  PlatformAccount */
	private $platformAccount;


	/** @type string 代练通对应的商户USERID */
	private $userid;


	/** @type string 商户支付密码 */
	private $appPayword;


	/** @type  PlatformOrder 平台订单 */
	private $order;


	/** @type string 图片调用地址 */
	private $serviceUrl = 'http://open.dailiantong.com/API/Service.ashx';

	/** @type  PamAccount */
	private $handlePam;


	/** @var  TongReq */
	private $req;


	/**
	 * constructor.
	 * @param $pt_account_id        int 同步的账号ID
	 * @param $platform_order       PlatformOrder
	 * @param $handle_pam           PamAccount 当前的操作用户
	 * @throws \Exception
	 */
	public function __construct($pt_account_id, $platform_order, $handle_pam = null)
	{
		// 赋值当前平台帐号
		$this->req             = new TongReq($pt_account_id);
		$this->handlePam       = $handle_pam;
		$this->platformAccount = $this->getPtAccount($pt_account_id);
		$this->order           = $platform_order;
		$this->checkEnv();

		$md5Payword = LmUtil::isMd5($this->platformAccount->tong_payword)
			? $this->platformAccount->tong_payword
			: md5($this->platformAccount->tong_payword);

		$this->userid     = $this->platformAccount->tong_userid;
		$this->appPayword = md5($md5Payword . $this->userid);

		$this->status = $this->createStatus();
		$this->syncDetail();
	}


	/**
	 * 删除订单
	 * @return bool
	 */
	public function delete()
	{
		$mark = '[删除]';
		if (!$this->isPublish()) {
			return $this->setError($mark . '本地代练通订单尚未发布， 不得删除');
		}
		if ($this->isDelete()) {
			return $this->setError($mark . '代练通订单已经在服务器被删除');
		}
		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
		];
		if (!$this->req->make('LevelOrderDelSelf', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		// 删除成功
		if ($result['Result'] == 1) {
			$this->status->delete();
			return true;
		}
		else {
			// 删除失败, 原因是已经删除
			if ($result['Result'] == '-4') {
				$error                         = $result['Err'];
				$this->status->tong_pt_message = $error;
				$this->status->tong_is_error   = 1;
				$this->status->tong_is_publish = 1;
				$this->status->tong_is_delete  = 1;
				$this->status->tong_order_no   = '';
				$this->status->save();
				return true;
			}
			// 删除失败, 请求错误
			return $this->setError($mark . '网络或者其他原因导致数据不正常');
		}
	}


	public function syncDetail()
	{
		if ($this->isDelete()) {
			return false;
		}

		if (!$this->detail()) {
			return false;
		}
		$result = $this->req->getResp();

		if (isset($result['SerialNo'])) {
			$this->status->tong_order_no         = $result['SerialNo'];
			$this->status->tong_order_price      = $result['Price'];
			$this->status->tong_order_hour       = $result['TimeLimit'];
			$this->status->tong_stamp            = $result['Stamp'];
			$this->status->tong_created_uid      = $result['CreateUID'];
			$this->status->tong_created_at       = $result['CreateDate'];
			$this->status->tong_updated_at       = $result['UpdateDate'];
			$this->status->tong_is_pub           = (int) $result['IsPub'];
			$this->status->tong_created_username = $result['CreateUserName'];
			$this->status->tong_contact_name     = $result['ContactName'];
			$this->status->tong_label1           = $result['Label1'];
			$this->status->tong_label2           = $result['Label2'];
			$this->status->tong_memo             = $result['Memo'];
			$this->status->tong_order_status     = $result['Status'];
			$this->status->tong_cancel_status    = $result['CancelStatus'];
			$this->status->tong_started_at       = $result['StartDate'] ?: null;
			$this->status->tong_ended_at         = $result['EndDate'] ?: null;
			$this->status->tong_is_lock          = $result['OrderLocked'];
			$this->status->tong_sd_uid           = $result['AcceptUID'];
			$this->status->tong_sd_username      = $result['AcceptUserName'];
			$this->status->tong_sd_qq            = $result['AcceptQQ'];
			$this->status->tong_sd_mobile        = $result['AcceptMobile'];
			$this->status->tong_cancel_note      = $result['CancelCmt'];
			$this->status->tong_is_star          = $result['IsValue'];
			$this->status->tong_left_hour        = $result['LeaveTime'];
			$this->status->tong_is_accept        = $result['AcceptUID'] ? 1 : 0;
			$this->status->tong_is_over          = $result['Status'] == self::ORDER_STATUS_OVER ? 1 : 0;
			// sync order status
			$this->status->sync_at = Carbon::now();


			if (in_array($this->status->tong_order_status, [self::ORDER_STATUS_ING])) {
				// 已经接手
				$this->status->tong_is_accept = 1;
				$this->status->accepted_at    = $this->status->tong_started_at;
			}
			$this->status->save();
			// 同步到平台主订单
			$this->syncToPlatformOrder();
			return true;
		}
		else {
			if (isset($result['Result']) && $result['Result'] == "-1") {
				$message                       = '[获取信息]订单已经被删除';
				$this->status->tong_is_publish = 1;
				$this->status->tong_is_delete  = 1;
				$this->status->tong_order_no   = '';
				$this->status->tong_pt_message = $message;
				$this->status->sync_at         = Carbon::now();
				$this->status->save();
				return $this->setError($message);
			}
			else {
				return $this->setError('[获取信息]获取信息失败');
			}
		}
	}


	/**
	 * 发布订单, 需要验证支付密码
	 * {"Result":1,"Err":"ORD20170215180515001"}
	 * @return bool
	 */
	public function publish()
	{
		$mark = '[发布订单]';
		if (!$this->checkPublish()) {
			return false;
		}

		// server code
		$serverCode = GameServer::tongServerCode($this->order->server_id);
		// $labels         = LmStr::matchDecode($this->$this->order->order_tags, true);
		$param     = [
			'ZoneServerID' => $serverCode,
			'Title'        => $this->order->order_title,
			'TimeLimit'    => $this->order->order_hours,
			'Price'        => $this->order->order_price,
			'Ensure1'      => $this->order->order_safe_money,
			'Ensure2'      => $this->order->order_speed_money,
			'Groups'       => $this->order->tong_group == PlatformOrder::TONG_GROUP_STAR ? 'GOODCHANNEL' : 'PUBLIC',
			'Mobile'       => $this->platformAccount->mobile,
			'QQ'           => $this->platformAccount->qq,

			// 此项目已经锁定, 不会影响到发单的变更
			'ContactName'  => '',
			//	'BasePrice'    => $this->$this->$this->order->get_in_price,
			//	'Label1'       => isset($labels[0]) ? $labels[0] : '',
			//	'Label2'       => isset($labels[1]) ? $labels[1] : '',
			//	'Memo'         => '',
			'Actors'       => $this->order->game_account . '|*|' . $this->order->game_pwd . '|*|' . $this->order->game_actor . '|*|' . $this->order->order_current . '|*|' . site('pt_tong_order_content'),
			'PayPass'      => $this->appPayword,
		];
		$validator = \Validator::make($param, [
			'ZoneServerID' => 'required',
			'Title'        => 'required',
			'TimeLimit'    => 'required',
			'Price'        => 'required',
			'Ensure1'      => 'required',
			'Ensure2'      => 'required',
		], [
			'ZoneServerID.required' => '服务器编码不能为空',
			'Title.required'        => '订单标题不能为空',
			'TimeLimit.required'    => '代练时长不能为空',
			'Price.required'        => '发单价格不能为空',
			'Ensure1.required'      => '安全保证金不能为空',
			'Ensure2.required'      => '效率保证金不能为空',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		if (!$this->req->make('LevelOrderAdd', $param)) {
			return $this->setError($this->req->getError());
		}

		$result = $this->req->getResp();

		$this->status->pt_account_note = $this->platformAccount->note;

		if ($result['Result'] == 1) {
			$success = '发布到代练通成功';

			// 更新 代练通订单号
			$this->status->tong_order_no   = $result['Err'];
			$this->status->tong_is_publish = 1;
			$this->status->tong_is_error   = 0;
			$this->status->tong_is_delete  = 0;
			$this->status->tong_pt_message = $success;
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
			$error = $mark . $result['Err'];

			// 记录同步订单错误
			$this->status->tong_is_publish = 0;
			$this->status->tong_is_error   = 1;
			$this->status->tong_pt_message = $error;
			$this->status->save();
			return $this->setError($error);
		}
	}


	public function rePublish()
	{
		if ($this->isIng()) {
			return $this->setError('订单代练中, 无法重新发布!');
		}
		if ($this->isOver()) {
			return $this->setError('订单已经完成, 不需要重新发布!');
		}
		if ($this->delete()) {
			// 重新获取 status 状态
			$this->status = $this->createStatus();
			return $this->publish();
		}
		else {
			return $this->setError($this->getError());
		}
	}

	/**
	 * 修改订单
	 * @param $type
	 * @param $value
	 * @return bool
	 */
	public function modify($type, $value)
	{
		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
			'Kind'       => $type,
			'Val'        => $value,
			'PayPass'    => $this->appPayword,
		];

		if (!$this->req->make('LevelOrderModify_Special', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['Result'] == 1) {
			if ($type == self::MODIFY_GAME_WORD) {
				$this->order->game_pwd = $value;
				$this->order->save();
			}
			$this->status->tong_pt_message = '修改订单成功';
			$this->status->tong_is_error   = 0;
			$this->status->save();
			return true;
		}
		else {
			$this->status->tong_pt_message = $result['Err'];
			$this->status->tong_is_error   = 1;
			$this->status->save();
			return $this->setError($result['Err']);
		}
	}

	/**
	 * 订单留言
	 * @param $content
	 * @return bool
	 */
	public function message($content)
	{
		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
			'Msg'        => $content,
		];

		if (!$this->req->make('LevelOrderMessageAdd', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['Result'] == 1) {
			// 完单
			$PlatformOrder = new ActionPlatformOrder($this->status->order_id);
			$PlatformOrder->talk($content, $this->handlePam->account_id);
			return true;
		}
		else {
			$error = '[发布留言]' . $result['Err'];
			return $this->setError($error);
		}
	}

	public function messageList($max = 500)
	{
		$param  = [
			'ODSerialNo' => $this->status->tong_order_no,
			'Max'        => $max,
		];
		$result = $this->req->make('LevelOrderMessageList', $param);
		return $result;
	}

	/**
	 * 同步最近50条信息
	 */
	public function syncMessage()
	{
		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
			'Max'        => 50,
		];

		if (!$this->req->make('LevelOrderMessageList', $param)) {
			return $this->setError($this->req->getError());
		}
		$messageList = $this->req->getResp();

		$list = array_get($messageList, 'LevelOrderMessageList');
		if ($list) {
			foreach ($list as $item) {
				PlatformTongLog::updateOrCreate([
					'md5_created_at' => md5($item['ID']),
				], [
					'msg_type'    => $item['MsgType'],
					'tong_uid'    => $item['UID'],
					'order_id'    => $this->status->order_id,
					'nick_name'   => $item['NickName'],
					'msg_content' => $item['Msg'],
					'created_at'  => $item['CreateDate'],
				]);
			}
		}
	}


	/**
	 * 对订单进行评价
	 * @param        $flag
	 * @param string $reason
	 * @return bool
	 */
	public function star($flag, $reason = '')
	{
		$detail = $this->detail();
		$param  = [
			'ODSerialNo'  => $this->status->tong_order_no,
			'OtherUserID' => $detail['AcceptUID'],   // 接手者用户ID
			'EvalType'    => $flag,   // 接手者用户ID
			'Comment'     => $reason,
		];

		if (!$this->req->make('LevelOrderValue', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		$flagDesc = self::kvStar($flag);
		if ($result['Result'] == 1) {
			$this->order->is_star = 'Y';
			$this->order->save();
			return true;
		}
		else {
			$error = '[' . $flagDesc . ']' . $result['Err'];
			return $this->setError($error);
		}
	}

	/**
	 * 解锁/锁定订单OK
	 * @param        $flag
	 * @param string $reason
	 * @return bool
	 */
	public function lock($flag, $reason = '')
	{
		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
			'Locked'     => $flag,
			'Reason'     => $reason,
		];

		if (!$this->req->make('LevelOrderLock', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();


		$flagDesc = self::kvLock($flag);
		if ($result['Result'] == 1) {
			// 完单
			$PlatformOrder = new ActionPlatformOrder();
			if ($flag == self::LOCK_LOCK) {
				$PlatformOrder->lock($this->status->order_id, $reason, $this->handlePam->account_id);
				$this->status->tong_is_lock = 1;
			}
			else if ($flag == self::LOCK_UNLOCK) {
				$PlatformOrder->unlock($this->status->order_id, $reason, $this->handlePam->account_id);
				$this->status->tong_is_lock = 0;
			}
			$this->status->save();
			return true;
		}
		else {
			$error = '[' . $flagDesc . ']' . $result['Err'];
			return $this->setError($error);
		}
	}

	/**
	 * 发单者取消订单
	 * @param            $flag
	 * @param            $pub_pay
	 * @param            $sd_pay
	 * @param string     $reason
	 * @return bool
	 */
	public function pubCancel($flag, $pub_pay, $sd_pay, $reason = '')
	{
		$param = [
			'ODSerialNo'   => $this->status->tong_order_no,
			'Flag'         => $flag,
			'PayLevelBal'  => $pub_pay,
			'RepEnsureBal' => $sd_pay,
			'Comment'      => $reason,
			'PayPass'      => $this->appPayword,
		];

		if (!$this->req->make('LevelOrderCancel', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		$flagDesc = self::kvPubCancel($flag);
		if ($result['Result'] == 1) {
			$this->syncDetail();
			return true;
		}
		else {
			$error = '[' . $flagDesc . ']' . $result['Err'];
			return $this->setError($error);
		}
	}


	/**
	 * 申请客服介入
	 * @return bool
	 */
	public function kf()
	{
		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
		];

		if (!$this->req->make('LevelOrderRequestArbitration', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['Result'] == 1) {
			// 完单
			$PlatformOrder = new ActionPlatformOrder();
			$PlatformOrder->kf($this->status->order_id, $this->handlePam->account_id);
			return true;
		}
		else {
			$error = '[申请客服介入]' . $result['Err'];
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
			'ODSerialNo' => $this->status->tong_order_no,
			'PayPass'    => $this->appPayword,
		];

		if (!$this->req->make('LevelOrderOver', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();

		if ($result['Result'] == 1) {
			// 完单
			$PlatformOrder = new ActionPlatformOrder();
			$PlatformOrder->over($this->status->order_id, $this->handlePam->account_id);
			// 记录同步信息
			return true;
		}
		else {
			return $this->setError($result['Err']);
		}
	}


	/**
	 * 更新订单进度
	 * @param                                                     $message
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image
	 * @return bool
	 */
	public function progress($message, $image = null)
	{
		$image_url = '';
		if ($image) {
			if ($image) {
				return $this->setError('代练通接口不支持上传图片!');
			}
			if (!$this->getOss()) {
				return false;
			}
			$oss             = $this->req->getResp();
			$accessKeyId     = $oss['AccessKeyId'];
			$accessKeySecret = $oss['AccessKeySecret'];
			$securityToken   = $oss['SecurityToken'];
			$endpoint        = "http://oss-cn-hangzhou.aliyuncs.com";
			$ossClient       = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false, $securityToken);
			$image_url       = $ossClient->uploadFile('1dailian', $image->getClientOriginalName(), $image->move(public_path('uploads/temp'), 1));
		}

		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
			'Msg'        => $message,
			'Img'        => $image_url,
		];
		if (!$this->req->make('LevelOrderProgressAdd', $param)) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if ($result['Result'] == 1) {
			return true;
		}
		else {
			$error = $result['Err'];
			return $this->setError($error);
		}
	}

	/**
	 * 获取订单截图URL
	 * @return string
	 */
	public function getSnapshotUrl()
	{
		if (!$this->status->tong_order_no) {
			return '';
		}
		$param = [
			'Action'     => 'LevelOrderProgressImg',
			'ODSerialNo' => $this->status->tong_order_no,
		];
		return $this->serviceUrl . '?' . http_build_query($param);
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
	 * 发单者取消描述
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvPubCancel($key = null)
	{
		$desc = [
			self::PUB_CANCEL_CANCEL => '取消撤销',
			self::PUB_CANCEL_AGREE  => '同意撤销',
			self::PUB_CANCEL_APPLY  => '申请撤销',
		];
		return kv($desc, $key);
	}


	/**
	 *
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
			self::STAR_BEST   => '非常满意',
		];
		return kv($desc, $key);
	}


	public static function kvOrderStatus($key = null)
	{
		$desc = [
			self::ORDER_STATUS_TEMP      => '临时订单',
			self::ORDER_STATUS_PUBLISH   => '未接手',
			self::ORDER_STATUS_ING       => '代练中',
			self::ORDER_STATUS_EXAMINE   => '等待验收',
			self::ORDER_STATUS_EXCEPTION => '订单异常',
			self::ORDER_STATUS_LOCK      => '账号锁定',
			self::ORDER_STATUS_CANCEL    => '撤销中',
			self::ORDER_STATUS_OVER      => '已结算',
		];
		return kv($desc, $key);
	}

	public static function kvCancelStatus($key = null)
	{
		$desc = [
			self::CANCEL_STATUS_NONE         => '未撤销',
			self::CANCEL_STATUS_TREATY       => '协商撤销中',
			self::CANCEL_STATUS_HANDLED      => '协商已处理',
			self::CANCEL_STATUS_CANCEL       => '撤销已取消',
			self::CANCEL_STATUS_KF_IN        => '客服介入中',
			self::CANCEL_STATUS_KF_OVER      => '客服已处理',
			self::CANCEL_STATUS_FORCE_HANDLE => '客服强制撤销',
		];
		return kv($desc, $key);
	}

	public static function kvModify($key = null)
	{
		$desc = [
			self::MODIFY_GAME_WORD => '修改游戏密码',
			self::MODIFY_TIME      => '时间(追加)',
			self::MODIFY_MONEY     => '金额(追加)',
		];
		return kv($desc, $key);
	}

	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvIsPub($key = null)
	{
		$desc = [
			self::IS_PUB_NO  => '否',
			self::IS_PUB_YES => '是',
		];
		return kv($desc, $key);
	}


	private function staticsPlatformAccountOrder()
	{
		dispatch(new StaticsTongOrderNum($this->platformAccount, $this->platform));
	}

	/**
	 * 获取OSS上传凭证
	 */
	private function getOss()
	{
		if (!$this->req->make('GetSTS')) {
			return $this->setError($this->req->getError());
		}
		$result = $this->req->getResp();
		if (isset($result['AccessKeyId'])) {
			return true;
		}
		else {
			$error = $result['Err'];
			return $this->setError($error);
		}
	}

	/**
	 * 获取订单的详细
	 * @return mixed
	 */
	private function detail()
	{
		if (!$this->status->tong_order_no) {
			return $this->setError('[获取详情]本地不存在代练通订单号， 无法获取详情');
		}
		if ($this->status->tong_is_delete) {
			return $this->setError('[获取详情]订单已经被删除');
		}

		$param = [
			'ODSerialNo' => $this->status->tong_order_no,
		];
		if (!$this->req->make('LevelOrderDetail', $param)) {
			return $this->setError($this->req->getError());
		}
		return true;
	}

	/**
	 * 是否已经删除订单
	 */
	private function isDelete()
	{
		if ($this->status->tong_is_publish && $this->status->tong_is_delete) {
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
		return $this->status->tong_is_publish;
	}

	private function isIng()
	{
		return $this->status->tong_is_accept;
	}

	private function isOver()
	{
		return $this->status->tong_is_over;
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
		$this->order->order_hours = $this->status->tong_order_hour;
		$this->order->order_price = $this->status->tong_order_price;
		$this->order->create_uid  = $this->status->tong_created_uid;
		$this->order->updated_at  = $this->status->updated_at;

		// 同步订单状态
		switch ($this->status->tong_order_status) {
			case self::ORDER_STATUS_PUBLISH:
				$this->order->order_status = PlatformOrder::ORDER_STATUS_PUBLISH;
				break;
			case self::ORDER_STATUS_ING :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_ING;
				break;
			case self::ORDER_STATUS_EXCEPTION :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_EXCEPTION;
				break;
			case self::ORDER_STATUS_CANCEL :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_CANCEL;
				break;
			case self::ORDER_STATUS_OVER :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_OVER;
				break;
			case self::ORDER_STATUS_EXAMINE :
				$this->order->order_status = PlatformOrder::ORDER_STATUS_EXAMINE;
				break;
		}

		// 撤销的时候同步撤销的状态
		if ($this->status->tong_order_status == self::ORDER_STATUS_CANCEL ||
			$this->status->tong_order_status == self::ORDER_STATUS_LOCK ) {
			switch ($this->status->tong_cancel_status) {
				case self::CANCEL_STATUS_NONE:     // 无撤销
					$this->order->order_status  = PlatformOrder::ORDER_STATUS_ING;
					$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_NONE;
					break;
				case self::CANCEL_STATUS_TREATY :  // 协商撤销中
					$this->order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
					$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_ING;
					break;
				case self::CANCEL_STATUS_HANDLED :
					$this->order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
					$this->order->cancel_type   = PlatformOrder::CANCEL_TYPE_DEAL;
					$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_DONE;
					break;
				case self::CANCEL_STATUS_CANCEL :
					$this->order->order_status  = PlatformOrder::ORDER_STATUS_ING;
					$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_NONE;
					break;
				case self::CANCEL_STATUS_KF_IN :
					$this->order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
					$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_ING;
					$this->order->cancel_type   = PlatformOrder::CANCEL_TYPE_KF;
					$this->order->kf_status     = PlatformOrder::KF_STATUS_ING;
					break;
				case self::CANCEL_STATUS_KF_OVER :
					$this->order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
					$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_DONE;
					$this->order->cancel_type   = PlatformOrder::CANCEL_TYPE_KF;
					$this->order->kf_status     = PlatformOrder::KF_STATUS_DONE;
					break;
				case self::CANCEL_STATUS_FORCE_HANDLE :
					$this->order->order_status  = PlatformOrder::ORDER_STATUS_CANCEL;
					$this->order->cancel_status = PlatformOrder::CANCEL_STATUS_DONE;
					$this->order->cancel_type   = PlatformOrder::CANCEL_TYPE_KF;
					$this->order->kf_status     = PlatformOrder::KF_STATUS_DONE;
					break;
			}
		}
		$this->order->assigned_at       = $this->status->tong_started_at;
		$this->order->overed_at         = $this->status->tong_ended_at;
		$this->order->order_lock        = $this->status->tong_is_lock;
		$this->order->sd_uid            = $this->status->tong_sd_uid;
		$this->order->sd_username       = $this->status->tong_sd_username;
		$this->order->sd_qq             = $this->status->tong_sd_qq;
		$this->order->sd_mobile         = $this->status->tong_sd_mobile;
		$this->order->cancel_note       = $this->status->tong_cancel_note;
		$this->order->is_star           = $this->status->tong_is_star;
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