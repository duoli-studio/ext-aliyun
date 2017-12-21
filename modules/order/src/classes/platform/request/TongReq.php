<?php namespace Order\Application\Platform\Request;

use App\Lemon\Dailian\Application\Platform\Traits\ReqTraits;
use App\Lemon\Repositories\Application\Traits\AppTrait;
use App\Lemon\Repositories\Sour\LmEnv;
use App\Lemon\Repositories\Sour\LmUtil;
use Curl\Curl;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TongReq
{

	use AppTrait, DispatchesJobs, ReqTraits;

	private $platformAccount;
	private $appId;
	private $appSecret;
	private $timestamp;
	private $appPayword;
	private $userid;
	/** @type string 版本号 */
	private $version = '1.0';


	/** @type string 易代练接口调用地址 */
	private $url = 'http://open.dailiantong.com/API/OpenService.ashx ';


	public function __construct($pt_account_id)
	{
		// 根据id获取代练账号密码
		$this->platformAccount = $this->getPtAccount($pt_account_id);
		$this->appId           = $this->platformAccount->tong_account;
		$this->appSecret       = $this->platformAccount->tong_password;
		$this->userid          = $this->platformAccount->tong_userid;
		$md5Payword            = LmUtil::isMd5($this->platformAccount->tong_payword)
			? $this->platformAccount->tong_payword
			: md5($this->platformAccount->tong_payword);
		$this->appPayword      = md5($md5Payword . $this->userid);
		$this->timestamp       = LmEnv::time();
	}

	/**
	 * 从代练通获取数据的封装函数
	 * @param string $action 游戏获取
	 * @param array  $param
	 * @return bool|mixed
	 */
	public function make($action, $param = [])
	{
		$param = $this->createParam($action, $param);
		$curl  = new Curl();
		$curl->setTimeout(10);
		$data = $curl->post($this->url, $param);

		if (!$data) {
			return $this->setError('请求服务器失败, 操作失败!');
		}
		$return = json_decode($data, true);

		// success log data
		$this->setReqUrl($this->url);
		$this->setParam($param);
		$this->setResp($return);
		$this->writeLog();

		return true;
	}

	/**
	 * 创建基础数据
	 * @param string $action 参数值串
	 * @param array  $param
	 * @return array
	 */
	private function createParam($action, $param = [])
	{
		// 组合参数
		$param['Action'] = $action;
		$str             = '';
		foreach ($param as $v) {
			$str .= $v;
		}

		// sign
		$sign = md5($str . $this->appId . $this->timestamp . $this->version . $this->appSecret);

		// add to param
		$param['AppId']     = $this->appId;
		$param['TimeStamp'] = $this->timestamp;
		$param['Ver']       = $this->version;
		$param['Sign']      = $sign;
		return $param;
	}


}