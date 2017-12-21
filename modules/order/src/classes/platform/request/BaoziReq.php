<?php namespace Order\Application\Platform\Request;

use App\Jobs\Platform\SyncDiffDetail;
use App\Lemon\Dailian\Application\Platform\Baozi;
use App\Lemon\Dailian\Application\Platform\Traits\ReqTraits;
use App\Lemon\Repositories\Application\Traits\AppTrait;
use App\Lemon\Repositories\Sour\LmArr;
use App\Models\PlatformAccount;
use App\Models\PlatformStatus;
use Carbon\Carbon;
use Curl\Curl;
use Illuminate\Foundation\Bus\DispatchesJobs;

class BaoziReq
{

	use AppTrait, DispatchesJobs, ReqTraits;

	private $platformAccount;

	private $appId;

	private $appSecret;

	private $timestamp;

	private $appPayword;


	/** @type array 电竞包子代练接口调用地址 */
	private $actions = [
		'create'         => '/api/up_order/create',         // 创建订单接口
		'del'            => '/api/up_order/delete',         // 删除订单接口
		'detail'         => '/api/up_order/detail',         // 订单详情接口
		'add_time'       => '/api/up_order/add_time',       // 补时操作
		'add_money'      => '/api/up_order/add_money',      // 补款操作
		'change_account' => '/api/up_order/change_account', // 修改账号密码
		'lock'           => '/api/up_order/lock',           // 锁定
		'unlock'         => '/api/up_order/unlock',         // 解锁
		'cancel'         => '/api/up_order/cancel',         // 申请撤单
		'picture'        => '/api/up_order/picture',        // 更新进度
		'temp_oss'       => '/api/up_order/temp_oss',       // 临时上传授权key
		'left_word'      => '/api/up_order/left_word',      // 获取订单留言
		'gain_picture'   => '/api/up_order/gain_picture',   // 获取订单图片
		'send_message'   => '/api/up_order/send_message',   // 对订单订单留言
		'kf'             => '/api/up_order/kf',             // 申请客服
		'over'           => '/api/up_order/over',           // 订单完成
		'star'           => '/api/up_order/star',           // 评价订单
		'signed_url'     => '/api/up_order/signed_url',     // 获取临时凭证
		'all_list'       => '/api/up_order/all_list',       // 获取某一订单状态的所有列表

	];

	public function __construct($pt_account_id)
	{
		// 根据id获取代练账号密码
		$this->platformAccount = $this->getPtAccount($pt_account_id);
		$this->appPayword      = $this->platformAccount->baozi_payword;
		$this->appId           = $this->platformAccount->baozi_app_key;
		$this->appSecret       = $this->platformAccount->baozi_app_secret;
		$this->timestamp       = Carbon::now()->timestamp;
	}

	/**
	 * 从代练平台获取数据的封装函数
	 * @param string $action url 不存在
	 * @param array  $param
	 * @return bool
	 */
	public function make($action, $param = [])
	{
		$param = $this->createParam($param);
		$url   = site('pt_default_baozi_url') . $this->actions[$action];
		$curl = new Curl();
		$curl->setTimeout(10);
		$data = $curl->get($url, $param);

		$return = [];
		if (is_object($data)) {
			$return = json_decode(json_encode($data), true);
		}
		if (is_string($data)) {
			$return = json_decode($data, true);
		}

		// success log data
		$this->setReqUrl($url);
		$this->setParam($param);
		$this->setResp($return);

		$this->writeLog();

		if (!$data) {
			return $this->setError('请求服务器失败, 操作失败!');
		}


		return true;
	}

	/**
	 * 创建基础数据
	 * @param array $param
	 * @return array
	 */
	private function createParam($param = [])
	{
		$str = '';
		foreach ($param as $v) {
			$str .= $v;
		}
		//随机6位字符
		$rand_str = substr(md5(time()), -6);
		$data     = [
			'app_key'    => $this->appId,
			'app_secret' => $this->appSecret,
			'rand_str'   => $rand_str,
			'timestamp'  => $this->timestamp,
		];
		// sign
		$sign = sha1(md5(LmArr::toKvStr($data)) . $rand_str);

		// add to param
		$param['app_key']   = $this->appId;
		$param['timestamp'] = $this->timestamp;
		$param['rand_str']  = $rand_str;
		$param['sign']      = $sign;
		return $param;
	}

	/**
	 * 刷新易代练平台发单时间
	 * @return bool
	 */
	public function repeat()
	{
		$account_id = PlatformAccount::where('platform', 'baozi')->select('baozi_userid')->get();
		$param      = [
			'order_no' => $account_id,
		];
		if (!$this->make('repeat', $param)) {
			return false;
		}
		$resp = $this->getResp();
		if ($resp['status'] == 0) {
			return true;
		} else {
			return $this->setError($resp['message']);
		}
	}


	public function syncTotal()
	{
		$status = [
			Baozi::ORDER_STATUS_ING,
			Baozi::ORDER_STATUS_CREATE,
			Baozi::ORDER_STATUS_PUBLISH,
			Baozi::ORDER_STATUS_EXAMINE,
			Baozi::ORDER_STATUS_EXCEPTION,
			Baozi::ORDER_STATUS_CANCEL,
			Baozi::ORDER_STATUS_QUASH,
		];
		foreach ($status as $v) {
			$param = [
				'order_status' => $v,
				'pagesize'     => 10000,
			];

			if (!$this->make('all_list', $param)) {
				return false;
			}
			$result = $this->getResp();

			if (isset($result['data']) && isset($result['data']['lists']) && is_array($result['data']['lists'])) {
				$orderNo = [];
				foreach ($result['data']['lists'] as $res) {
					$orderNo[] = $res['order_no'];
				}
				$orders = PlatformStatus::whereIn('baozi_order_no', $orderNo)->select(['baozi_order_no', 'order_id'])->get();
				foreach ($orders as $order) {
					if ($order->baozi_order_status != $v) {
						$this->dispatch(new SyncDiffDetail($order->order_id));
					}
				}
			}
		}
		return true;
	}

}