<?php namespace Util\Util\Action;

/**
 * 基本账户操作
 */

use Carbon\Carbon;
use Poppy\Extension\Aliyun\Core\Config;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Extension\Aliyun\Core\Profile\DefaultProfile;
use Poppy\Extension\Aliyun\Dysms\Sms\Request\V20170525\SendSmsRequest;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use System\Classes\Traits\SystemTrait;
use Util\Models\PamCaptcha;
use Poppy\Extension\Aliyun\Dysms\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

class Util
{
	use SystemTrait;
	static $acsClient = null;

	/**
	 * @param $passport
	 * @return bool
	 */
	public function sendCaptcha($passport)
	{
		// 验证数据格式
		if (UtilHelper::isEmail($passport)) {
			$type       = PamCaptcha::TYPE_MAIL;
			$expiredMin = $this->getSetting()->get('util::captcha.mail_expired_minute');
		} elseif (UtilHelper::isMobile($passport)) {
			$type       = PamCaptcha::TYPE_MOBILE;
			$expiredMin = $this->getSetting()->get('util::captcha.sms_expired_minute');
		} else {
			return $this->setError(trans('util::act.util.send_captcha_passport_format_error'));
		}
		// 发送验证码
		// todo
		$this->sendSms();


		// 发送验证码数据库操作
		$expired = Carbon::now()->addMinute($expiredMin);
		$captcha = PamCaptcha::updateOrCreate([
			'passport' => $passport,
		]);

		if ($captcha->num == 3) {
			$captcha->num = 0;
		}
		// Not Send
		if ($captcha->num == 0) {
			$captcha->num     = 1;
			$captcha->type    = $type;
			$captcha->captcha = StrHelper::randomCustom(6, '0123456789');
		} else {
			$captcha->num  += 1;
			$captcha->type = $type;
		}

		// Captcha In Db or Re Generate.
		// todo : send captcha
		$randNo = $captcha->captcha;


		$captcha->disabled_at = $expired;
		$captcha->save();

		// 超出指定时间的验证码需要删除
		// todo 事件
		return true;
	}

	/**
	 * 取得AcsClient
	 *
	 * @return DefaultAcsClient
	 */
	public static function getAcsClient()
	{
		//产品名称:云通信流量服务API产品,开发者无需替换
		$product = "Dysmsapi";

		//产品域名,开发者无需替换
		$domain = "dysmsapi.aliyuncs.com";

		// TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
		$accessKeyId = "LTAILZQeoDjp9Use"; // AccessKeyId

		$accessKeySecret = "lzcEfdhKKfRKdVrh6zIreLXrXEcbXm"; // AccessKeySecret

		// 暂时不支持多Region
		$region = "cn-hangzhou";

		// 服务结点
		$endPointName = "cn-hangzhou";


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
	 * 发送短信
	 * @return stdClass
	 */
	public static function sendSms()
	{

		// 初始化SendSmsRequest实例用于设置发送短信的参数
		$request = new SendSmsRequest();

		// 必填，设置短信接收号码
		$request->setPhoneNumbers("18864838035");

		// 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
		$request->setSignName("简约CITY");

		// 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
		$request->setTemplateCode("SMS_119900021");

		// 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
		$request->setTemplateParam(json_encode([  // 短信模板中字段的值
			"name" => "哈哈哈",
			"time" => "半夜11点",
		], JSON_UNESCAPED_UNICODE));

		// 可选，设置流水号
		// $request->setOutId("yourOutId");

		// 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
		// $request->setSmsUpExtendCode("1234567");

		// 发起访问请求
		$acsResponse = static::getAcsClient()->getAcsResponse($request);

		return $acsResponse;

	}

	/**
	 * 短信发送记录查询
	 * @return stdClass
	 */
	public static function querySendDetails()
	{

		// 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
		$request = new QuerySendDetailsRequest();

		// 必填，短信接收号码
		$request->setPhoneNumber("12345678901");

		// 必填，短信发送日期，格式Ymd，支持近30天记录查询
		$request->setSendDate("20170718");

		// 必填，分页大小
		$request->setPageSize(10);

		// 必填，当前页码
		$request->setCurrentPage(1);

		// 选填，短信发送流水号
		$request->setBizId("yourBizId");

		// 发起访问请求
		$acsResponse = static::getAcsClient()->getAcsResponse($request);

		return $acsResponse;
	}


}