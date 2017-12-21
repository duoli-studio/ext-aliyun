<?php namespace Order\Application\Platform\Traits;


use App\Models\PlatformAccount;

trait ReqTraits {

	protected $reqUrl;

	protected $resp;

	protected $param;

	protected static $reqLog;

	/**
	 * @return mixed
	 */
	public function getReqUrl() {
		return $this->reqUrl;
	}

	/**
	 * @param mixed $reqUrl
	 */
	public function setReqUrl($reqUrl) {
		$this->reqUrl = $reqUrl;
	}


	/**
	 * @return mixed
	 */
	public function getResp() {
		return $this->resp;
	}

	/**
	 * @param mixed $result
	 */
	public function setResp($result) {
		$this->resp = $result;
	}

	/**
	 * @return mixed
	 */
	public function getParam() {
		return $this->param;
	}

	/**
	 * @param mixed $param
	 */
	public function setParam($param) {
		$this->param = $param;
	}

	/**
	 * 获取所有请求数据返回数据
	 * @return array
	 */
	public function getReqResp() {
		return [
			'url'    => $this->getReqUrl(),
			'param'  => $this->getParam(),
			'result' => $this->getResp(),
		];
	}

	public function getReqLog() {
		return self::$reqLog;
	}

	/**
	 * @param $pt_account_id
	 * @return PlatformAccount
	 */
	protected function getPtAccount($pt_account_id) {
		return PlatformAccount::getCacheItem($pt_account_id);
	}

	protected function writeLog() {
		self::$reqLog[] = $this->getReqResp();
	}


}