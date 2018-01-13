<?php

namespace Poppy\Extension\Aliyun\Dysms\Sms\Request\V20170525;

use  Poppy\Extension\Aliyun\Core\RpcAcsRequest;

class SendSmsRequest extends RpcAcsRequest
{
	public function  __construct()
	{
		parent::__construct("Dysmsapi", "2017-05-25", "SendSms");
		$this->setMethod("POST");
	}

	private  $templateCode;

	private  $phoneNumbers;

	private  $signName;

	private  $resourceOwnerAccount;

	private  $templateParam;

	private  $resourceOwnerId;

	private  $ownerId;

	private  $outId;

    private  $smsUpExtendCode;

	public function getTemplateCode() {
		return $this->templateCode;
	}

	/**
	 * 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考
	 * @url https://dysms.console.aliyun.com/dysms.htm#/develop/template
	 * @param $templateCode
	 */
	public function setTemplateCode($templateCode) {
		$this->templateCode = $templateCode;
		$this->queryParameters["TemplateCode"]=$templateCode;
	}

	public function getPhoneNumbers() {
		return $this->phoneNumbers;
	}

	/**
	 * 设置短信接收号码
	 * @param $phoneNumbers
	 */
	public function setPhoneNumbers($phoneNumbers) {
		$this->phoneNumbers = $phoneNumbers;
		$this->queryParameters["PhoneNumbers"]=$phoneNumbers;
	}

	public function getSignName() {
		return $this->signName;
	}

	/**
	 * 必填，设置签名名称，应严格按"签名名称"填写
	 * @param $signName
	 * @url https://dysms.console.aliyun.com/dysms.htm#/develop/sign
	 */
	public function setSignName($signName) {
		$this->signName = $signName;
		$this->queryParameters["SignName"]=$signName;
	}

	public function getResourceOwnerAccount() {
		return $this->resourceOwnerAccount;
	}

	public function setResourceOwnerAccount($resourceOwnerAccount) {
		$this->resourceOwnerAccount = $resourceOwnerAccount;
		$this->queryParameters["ResourceOwnerAccount"]=$resourceOwnerAccount;
	}

	public function getTemplateParam() {
		return $this->templateParam;
	}

	/**
	 * 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
	 * @param $templateParam
	 */
	public function setTemplateParam($templateParam) {
		$this->templateParam = $templateParam;
		$this->queryParameters["TemplateParam"]=$templateParam;
	}

	public function getResourceOwnerId() {
		return $this->resourceOwnerId;
	}

	public function setResourceOwnerId($resourceOwnerId) {
		$this->resourceOwnerId = $resourceOwnerId;
		$this->queryParameters["ResourceOwnerId"]=$resourceOwnerId;
	}

	public function getOwnerId() {
		return $this->ownerId;
	}

	public function setOwnerId($ownerId) {
		$this->ownerId = $ownerId;
		$this->queryParameters["OwnerId"]=$ownerId;
	}

	public function getOutId() {
		return $this->outId;
	}

	public function setOutId($outId) {
		$this->outId = $outId;
		$this->queryParameters["OutId"]=$outId;
	}

    public function getSmsUpExtendCode() {
        return $this->smsUpExtendCode;
    }

    public function setSmsUpExtendCode($smsUpExtendCode) {
        $this->smsUpExtendCode = $smsUpExtendCode;
        $this->queryParameters["SmsUpExtendCode"]=$smsUpExtendCode;
    }
}