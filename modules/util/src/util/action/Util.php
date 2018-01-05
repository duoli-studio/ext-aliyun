<?php namespace Util\Util\Action;

use Carbon\Carbon;
use Poppy\Extension\Aliyun\Core\Config;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Extension\Aliyun\Core\Profile\DefaultProfile;
use Poppy\Extension\Aliyun\Dysms\Sms\Request\V20170525\SendSmsRequest;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use Slt\Models\UserProfile;
use System\Classes\Traits\SystemTrait;
use Util\Models\PamCaptcha;

class Util
{
	use SystemTrait;
	static $acsClient = null;

	public function __construct()
	{
		Config::load();
	}

	/**
	 * @param string $passport
	 * @param string $type
	 * @return bool
	 */
	public function sendCaptcha($passport, $type = PamCaptcha::CON_REGISTER)
	{
		// 验证数据格式
		if (UtilHelper::isEmail($passport)) {
			$sendType   = PamCaptcha::TYPE_MAIL;
			$expiredMin = $this->getSetting()->get('util::captcha.mail_expired_minute');
		} elseif (UtilHelper::isMobile($passport)) {
			$sendType   = PamCaptcha::TYPE_MOBILE;
			$expiredMin = $this->getSetting()->get('util::captcha.sms_expired_minute');
		} else {
			return $this->setError(trans('util::act.util.send_captcha_passport_format_error'));
		}


		// 发送验证码数据库操作
		$expired = Carbon::now()->addMinute($expiredMin);
		/** @var PamCaptcha $captcha */
		$captcha = PamCaptcha::updateOrCreate([
			'passport' => $passport,
		]);
		if ($captcha->num == 3) {
			$captcha->num = 0;
		}
		// Not Send
		if ($captcha->num == 0) {
			$captcha->num     = 1;
			$captcha->type    = $sendType;
			$captcha->captcha = StrHelper::randomCustom(6, '0123456789');
		} else {
			$captcha->num  += 1;
			$captcha->type = $sendType;
		}
		$captcha->disabled_at = $expired;
		$captcha->save();

		// todo:张年文  需要判定系统的操作类型
		switch ($type) {
			case PamCaptcha::CON_ORDER:
				$data =[
					'signName'     => $this->getSetting()->get('extension::sms.code_sign_name'),
					'templateCode' => $this->getSetting()->get('extension::sms.order_template'),
					'templateParam'=> [
						'code' => $captcha->captcha,
					]
				];
				break;
			case PamCaptcha::CON_FIND_PASSWORD:
				$data =[
					'signName'     => $this->getSetting()->get('extension::sms.code_sign_name'),
					'templateCode' => $this->getSetting()->get('extension::sms.find_password_template'),
					'templateParam'=> [
						'code' => $captcha->captcha,
					]
				];
				break;
			default :
				$data =[
					'signName'     => $this->getSetting()->get('extension::sms.code_sign_name'),
					'templateCode' => $this->getSetting()->get('extension::sms.register_template'),
					'templateParam'=> [
						'code' => $captcha->captcha,
					]
				];
		}


		if ($sendType === PamCaptcha::TYPE_MOBILE) {
			$this->sendSms($passport,  $data);
			return true;
		} else {
			$this->sendMail($passport, $data);
			return true;
		}
	}


	/**
	 * 取得AcsClient
	 * @return DefaultAcsClient
	 */
	public function getAcsClient()
	{
		//产品名称:云通信流量服务API产品,开发者无需替换
		$product = $this->getSetting()->get('extension::sms.product');

		//产品域名,开发者无需替换
		$domain = $this->getSetting()->get('extension::sms.domain');

		// TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
		$accessKeyId = $this->getSetting()->get('extension::sms.access_key_id');

		$accessKeySecret = $this->getSetting()->get('extension::sms.access_key_secret');

		// 暂时不支持多Region
		$region = $this->getSetting()->get('extension::sms.region');

		// 服务结点
		$endPointName = $this->getSetting()->get('extension::sms.end_point_name');

		if (static::$acsClient == null) {

			//初始化acsClient,暂不支持region化
			$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

			// 增加服务结点
			DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

			// 初始化AcsClient用于发起请求
			static::$acsClient = new DefaultAcsClient($profile);
		}
		return static::$acsClient;
	}

	/**
	 * @param $phoneNumbers
	 * @param $data
	 * @return mixed|\SimpleXMLElement
	 */
	private function sendSms($phoneNumbers,$data)
	{

		// 初始化SendSmsRequest实例用于设置发送短信的参数
		$request = new SendSmsRequest();

		// 必填，设置短信接收号码
		$request->setPhoneNumbers($phoneNumbers);

		// 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
		$request->setSignName($data['signName']);

		// 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
		$request->setTemplateCode($data['templateCode']);

		// 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
		$request->setTemplateParam(json_encode($data['templateParam'], JSON_UNESCAPED_UNICODE));

		// 可选，设置流水号
		// $request->setOutId("yourOutId");

		// 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
		// $request->setSmsUpExtendCode("1234567");

		// 发起访问请求
		$acsResponse = static::getAcsClient()->getAcsResponse($request);

		return $acsResponse;

	}


	/**
	 * @param $passport
	 * @param $rand_number
	 */
	private function sendMail($passport, $rand_number)
	{

	}


}