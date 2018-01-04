<?php namespace Poppy\Extension\Alipay\OpenApi\Trade;

use Poppy\Extension\Alipay\OpenApi\Request;

/**
 * ALIPAY API: alipay.trade.app.pay request
 *
 * @author auto create
 * @since  1.0, 2016-11-17 11:45:56
 */
class AppPay extends Request
{
	/**
	 * app支付接口2.0
	 **/
	private $bizContent;

	protected $apiVersion = "1.0";

	protected $apiMethodName = 'alipay.trade.app.pay';

	private $needEncrypt = false;


	public function setBizContent($bizContent)
	{
		$this->bizContent              = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function setNeedEncrypt($needEncrypt)
	{

		$this->needEncrypt = $needEncrypt;

	}

	public function getNeedEncrypt()
	{
		return $this->needEncrypt;
	}

}
