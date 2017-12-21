<?php namespace Order\Application\Platform\Request;

use App\Jobs\Platform\SyncDiffDetail;
use App\Lemon\Dailian\Application\Platform\Mama;
use App\Lemon\Dailian\Application\Platform\Traits\ReqTraits;
use App\Lemon\Repositories\Application\Traits\AppTrait;
use App\Lemon\Repositories\Sour\LmEnv;
use App\Models\PlatformAccount;
use App\Models\PlatformStatus;
use Curl\Curl;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MamaReq
{

	use AppTrait, DispatchesJobs, ReqTraits;

	/** @type string 授权app id */
	private $appId;

	/** @type string 商户密钥 */
	private $appSecret;

	/** @type string 商户支付密码 */
	private $appPayword;

	/** @type int  时间戳 */
	private $timestamp;

	/** @var PlatformAccount */
	private $platformAccount;


	private $urls = [
		// 修改和发布
		'mod_pub'           => 'http://inf.dailianmama.com/releaseOrder.intf',

		// 详细订单
		'detail'            => 'http://inf.dailianmama.com/orderinfo.intf',

		// 上架订单
		'up'                => 'http://inf.dailianmama.com/upOrder.intf',

		// 下架订单
		'down'              => 'http://inf.dailianmama.com/closeOrder.intf',

		// 删除订单
		'delete'            => 'http://inf.dailianmama.com/deleteOrder.intf',

		//发布留言
		'add_chat'          => 'http://inf.dailianmama.com/addChat.intf',

		//上传截图
		'save_pic'          => 'http://inf.dailianmama.com/savePicture.intf',

		//获取留言
		'get_chat'          => 'http://inf.dailianmama.com/chatOldList.intf',

		//获取截图
		'get_pic'           => 'http://inf.dailianmama.com/getOrderPictureList.intf',

		//订单操作
		'order_operation'   => 'http://inf.dailianmama.com/operationOrder.intf',

		//支付密码验证
		'check_pwd'         => 'http://inf.dailianmama.com/checkTradePassword.intf',

		//获取阿里云临时凭证
		'get_TempUploadKey' => 'http://inf.dailianmama.com/getTempUploadKey.intf',

		//获取某一订单状态的所有列表
		'all_list'          => 'http://inf.dailianmama.com/getReleaseOrderStatusList.intf',

		// 刷新未接手订单
		'refresh_all'       => 'http://inf.dailianmama.com/refreshAllOrderTime.intf',
	];

	public function __construct($pt_account_id)
	{
		$this->platformAccount = $this->getPtAccount($pt_account_id);//TODO 从 PlatformAccount
		$this->appId           = $this->platformAccount->mama_account;
		$this->appSecret       = $this->platformAccount->mama_password;
		$this->appPayword      = $this->platformAccount->mama_payword;
		$this->timestamp       = LmEnv::time();
	}

	/**
	 * 创建签名
	 * @param $param
	 * @return string
	 */
	private function createSign($param)
	{
		ksort($param);
		$query = '';
		foreach ($param as $key => $value) {
			$query .= $key . '=' . $value . '&';
		}
		$query = rtrim($query, '&');
		return md5($query . $this->appSecret);
	}


	/**
	 * 从代练猫获取数据的封装函数
	 * @param string $action url 不存在
	 * @param array  $param
	 * @return bool
	 * @throws \Exception
	 */
	public function make($action, $param = [])
	{
		if (!isset($this->urls[$action])) {
			throw new \Exception('url ' . $action . ' 不存在!');
		}
		$url   = $this->urls[$action];
		$param = $this->createParam($param);
		$curl  = new Curl();
		$curl->setTimeout(10);
		$data = $curl->post($url, $param);

		if (!$data) {
			return $this->setError('请求服务器失败, 操作失败!');
		}

		// convert to array
		$return = json_decode(json_encode($data), true);

		// success log data
		$this->setReqUrl($url);
		$this->setParam($param);
		$this->setResp($return);
		$this->writeLog();
		return true;
	}

	public function syncTotal()
	{
		$status = [
			Mama::ORDER_STATUS_CREATE,
			Mama::ORDER_STATUS_ING,
			Mama::ORDER_STATUS_EXCEPTION,
			Mama::ORDER_STATUS_WAIT_VERIFY,
			Mama::ORDER_STATUS_CANCELING,
			Mama::ORDER_STATUS_LOCK,
		];
		foreach ($status as $v) {
			$param = [
				'orderstatus' => $v,
			];

			if (!$this->make('all_list', $param)) {
				continue;
			}
			$result = $this->getResp();
			if (isset($result['data']) && isset($result['data']['list']) && is_array($result['data']['list'])) {
				$orderNo = [];

				foreach ($result['data']['list'] as $res) {
					$orderNo[] = $res['orderid'];
				}
				$orders = PlatformStatus::whereIn('mama_order_no', $orderNo)->select(['mama_order_status', 'order_id'])->get();
				foreach ($orders as $order) {
					if ($order->mama_order_status != $v) {
						$this->dispatch(new SyncDiffDetail($order->order_id));
					}
				}
			}
		}
	}

	public function refresh()
	{
		$this->make('refresh_all');
	}

	/**
	 * 创建参数和签名
	 * @param $param
	 * @return mixed
	 */
	private function createParam($param)
	{
		$param['sourceid']  = $this->appId;      // 与代练猫约定的商户标识
		$param['timestamp'] = $this->timestamp;
		$param['sign']      = $this->createSign($param);
		return $param;
	}
}