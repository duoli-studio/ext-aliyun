<?php namespace Poppy\Extension\Alipay\OpenApi\Fund;

use Poppy\Extension\Alipay\OpenApi\Request;

/**
 * 单笔转账到支付宝账户接口
 * ALIPAY API: alipay.fund.trans.toaccount.transfer request
 * @author auto create
 * @since  1.0, 2017-11-14 18:46:52
 */
class TransToAccountTransfer extends Request
{

	private $bizContent;

	protected $apiMethodName = 'alipay.fund.trans.toaccount.transfer';

	protected $apiVersion = "1.0";

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
