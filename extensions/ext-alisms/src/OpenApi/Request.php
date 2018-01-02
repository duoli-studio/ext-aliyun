<?php namespace Poppy\Extension\Alipay\OpenApi;


abstract class Request
{
	/** @var array Api 参数 */
	protected $apiParas = [];

	/** @var string Api 版本号 */
	protected $apiVersion = "";

	/** @var string Api 方法名 */
	protected $apiMethodName = '';


	protected $terminalType;
	protected $terminalInfo;
	protected $prodCode;

	/** @var string 通知/callback url */
	protected $notifyUrl;

	/** @var string 跳转地址 */
	protected $returnUrl;

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion = $apiVersion;
	}


	public function getApiVersion()
	{
		return $this->apiVersion;
	}

	public function getApiMethodName()
	{
		return $this->apiMethodName;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl = $notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl = $returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

}