<?php namespace Order\Application\Platform\Request;

use App\Jobs\Platform\SyncDiffDetail;
use App\Lemon\Dailian\Application\Platform\Traits\ReqTraits;
use App\Lemon\Dailian\Application\Platform\Yq;
use App\Lemon\Repositories\Application\Traits\AppTrait;
use App\Models\PlatformAccount;
use App\Models\PlatformStatus;
use Curl\Curl;
use Illuminate\Foundation\Bus\DispatchesJobs;

class YqReq
{
	use AppTrait, DispatchesJobs, ReqTraits;

	private $platformAccount;

	private $appId;

	private $phone;

	private $authKey;

	/** @type string 17代练接口调用地址 */
	private $actions = [
		//发布订单接口
		'send'   => '/open/out/send',
		//管理订单接口
		'manage' => '/open/outapi/interface',
	];

	public function __construct($pt_account_id)
	{
		// 根据id获取代练账号密码
		$this->platformAccount = $this->getPtAccount($pt_account_id);
		$this->appId           = $this->platformAccount->yq_account;
		$this->authKey         = $this->platformAccount->yq_auth_key;
		$this->phone           = $this->platformAccount->yq_phone;
	}

	/**
	 * 从17代练获取数据的封装函数
	 * @param string $action url 不存在
	 * @param array  $param
	 * @return bool
	 */
	public function make($action, $param = [])
	{
		$param = $this->createParam($action, $param);
		$url   = site('pt_default_yq_url').$this->actions[$action];
		$curl  = new Curl();
		$curl->setTimeout(10);
		$data = $curl->post($url, $param);

		if (!$data) {
			return $this->setError('请求服务器失败, 操作失败!');
		}
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

		return true;
	}

	/**
	 * 创建基础数据
	 * @param array $param
	 * @return array
	 */
	private function createParam($action, $param = [])
	{
		$param['Action'] = $action;
		$param['phone']  = $this->phone;

		ksort($param);
		$signArray = array_values($param);
		$signStr   = implode("", $signArray) . $this->authKey;
		$signStr   = md5($signStr);

		// add to param
		$param['authKey'] = $this->authKey;
		$param['appid']   = $this->appId;
		$param['Sign']    = $signStr;
		return $param;
	}

	/**
	 * 刷新17代练平台发单时间
	 * @return bool
	 */
	public function repeat()
	{
		$account_id = PlatformAccount::where('platform', 'yq')->select('yq_userid')->get();
		$param      = [
			'order_no' => $account_id,
		];
		if (!$this->make('repeat', $param)) {
			return false;
		}
		$resp = $this->getResp();
		if ($resp['status'] == 0) {
			return true;
		}
		else {
			return $this->setError($resp['message']);
		}
	}


	public function syncTotal()
	{
		$status = [
			Yq::ORDER_STATUS_ING,
			Yq::ORDER_STATUS_CREATE,
			Yq::ORDER_STATUS_PUBLISH,
			Yq::ORDER_STATUS_EXAMINE,
			Yq::ORDER_STATUS_EXCEPTION,
			Yq::ORDER_STATUS_CANCEL,
			Yq::ORDER_STATUS_QUASH,
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
				$orders = PlatformStatus::whereIn('yq_order_no', $orderNo)->select(['yq_order_no', 'order_id'])->get();
				foreach ($orders as $order) {
					if ($order->yq_order_status != $v) {
						$this->dispatch(new SyncDiffDetail ($order->order_id));
					}
				}
			}
		}
		return true;
	}

}
