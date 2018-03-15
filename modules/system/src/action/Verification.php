<?php namespace System\Action;

use Carbon\Carbon;
use Poppy\Extension\Aliyun\Core\Config;
use Poppy\Extension\Aliyun\Core\DefaultAcsClient;
use Poppy\Extension\Aliyun\Core\Profile\DefaultProfile;
use Poppy\Extension\Aliyun\Dysms\Sms\Request\V20170525\SendSmsRequest;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use System\Classes\Traits\SystemTrait;
use System\Models\PamCaptcha;

class Verification
{
	use SystemTrait;

	const CRYPT_METHOD = 'AES-256-ECB';

	/** @var string 隐藏在加密中的字符串 */
	private $hiddenStr;

	/** @var PamCaptcha */
	private $captcha;

	public static $acsClient = null;

	public function __construct()
	{
		Config::load();
	}

	/**
	 * @param string $passport
	 * @param string $type
	 * @return bool
	 */
	public function send($passport, $type)
	{
		// 验证数据格式
		if (UtilHelper::isEmail($passport)) {
			$passportType = PamCaptcha::TYPE_MAIL;
			$expiredMin   = $this->getSetting()->get('system::captcha.mail_expired_minute');
		}
		elseif (UtilHelper::isMobile($passport)) {
			$passportType = PamCaptcha::TYPE_MOBILE;
			$expiredMin   = $this->getSetting()->get('extension::sms.expired_minute');
		}
		else {
			return $this->setError(trans('system::action.verification.send_passport_format_error'));
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
			$captcha->type    = $passportType;
			$captcha->captcha = StrHelper::randomCustom(6, '0123456789');
		}
		else {
			$captcha->num += 1;
			$captcha->type = $passportType;
		}
		$captcha->disabled_at = $expired;
		$captcha->save();

		$this->captcha = $captcha;

		if ($passportType === PamCaptcha::TYPE_MOBILE) {
			if (!$this->sendSms($passport, $captcha->captcha, $type)) {
				return false;
			}
		}
		else {
			$this->sendMail($passport, $type);
		}

		return true;
	}

	/**
	 * 验证验证码
	 * @param $passport
	 * @param $captcha
	 * @return bool
	 */
	public function check($passport, $captcha)
	{
		$type = PamCaptcha::TYPE_MOBILE;
		if (UtilHelper::isEmail($passport)) {
			$type = PamCaptcha::TYPE_MAIL;
		}
		$Validate = PamCaptcha::where('disabled_at', '>', Carbon::now());
		$exists   = $Validate->where('captcha', $captcha)
			->where('passport', $passport)
			->where('type', $type)
			->exists();
		if ($exists) {
			return true;
		}
		 
			return $this->setError(trans('system::action.verification.check_captcha_error'));
	}

	/**
	 * 删除验证码
	 * @param $passport
	 * @return bool|null
	 */
	public function delete($passport)
	{
		try {
			return PamCaptcha::where('passport', $passport)->delete();
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 清除过期验证码
	 * @return bool|null
	 */
	public function clean()
	{
		try {
			return PamCaptcha::where('disabled_at', '<', Carbon::now())->delete();
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 生成一次验证码
	 * @param int    $expired_min 过期时间
	 * @param string $hidden_str  隐藏的验证字串
	 * @return string
	 */
	public function genOnceVerifyCode($expired_min = 10, $hidden_str = '')
	{
		// 生成 10 分钟的有效 code
		$now     = Carbon::now();
		$unix    = Carbon::now()->addMinutes($expired_min)->timestamp;
		$randStr = str_random(16);
		$key     = $now->timestamp . '_' . str_random(6);
		if (!$hidden_str) {
			$hidden_str = str_random(6);
		}
		$str      = $unix . '|' . $key . '|' . $hidden_str . '|' . $randStr;
		$cacheKey = cache_name(__CLASS__, 'once_verify_code');
		if (\Cache::has($cacheKey)) {
			$data = \Cache::get($cacheKey);
			if (is_array($data)) {
				foreach ($data as $item) {
					list($_unix, $_key) = explode('|', $item);
					if ($_unix < $now->timestamp) {
						// key 已经过期, 移除
						unset($data[$_key]);
					}
				}
			}
			$data[$key] = $str;
		}
		else {
			$data = [
				$key => $str,
			];
		}
		\Cache::forever($cacheKey, $data);

		return openssl_encrypt($str, self::CRYPT_METHOD, substr(config('app.key'), 0, 32));
	}

	/**
	 * 需要验证的验证码
	 * @param $verify_code
	 * @return bool
	 */
	public function verifyOnceCode($verify_code)
	{
		$str = openssl_decrypt($verify_code, self::CRYPT_METHOD, substr(config('app.key'), 0, 32));
		if (strpos($str, '|') !== false) {
			$split    = explode('|', $str);
			$expire   = (int) $split[0];
			$key      = strval($split[1]);
			$cacheKey = cache_name(__CLASS__, 'once_verify_code');
			$data     = (array) \Cache::get($cacheKey);
			if ($expire < Carbon::now()->timestamp) {
				return $this->setError(trans('system::action.verification.verify_code_expired'));
			}

			if (!isset($data[$key])) {
				return $this->setError(trans('system::action.verification.verify_code_expired'));
			}
			 
				unset($data[$key]);
				$this->hiddenStr = $split[2];
				\Cache::forever($cacheKey, $data);

				return true;
		}
		 
			return $this->setError(trans('system::action.verification.verify_code_error'));
	}

	public function getHiddenStr()
	{
		return $this->hiddenStr;
	}

	/**
	 * @return PamCaptcha
	 */
	public function getCaptcha(): PamCaptcha
	{
		return $this->captcha;
	}

	/**
	 * @param $phoneNumbers
	 * @param $captcha
	 * @param $type
	 * @return mixed|\SimpleXMLElement
	 */
	private function sendSms($phoneNumbers, $captcha, $type)
	{
		$sign  = $this->getSetting()->get('extension::sms.sign');
		$param = [
			'code' => $captcha,
		];
		switch ($type) {
			case PamCaptcha::CON_LOGIN:
			case PamCaptcha::CON_PASSWORD:
			case PamCaptcha::CON_USER:
			case PamCaptcha::CON_REBIND:
			default:
				$code = $this->getSetting()->get('extension::sms_aliyun.captcha_template');
				break;
		}

		$sendType = $this->getSetting()->get('extension::sms.send_type');
		if ($sendType == 'local') {
			$content = ($sign ? "[{$sign}]" : '') . sys_setting('extension::sms_local.captcha_text');
			$trans   = sys_trans($content, $param);
			\Log::info($trans);

			return true;
		}

		// 初始化 SendSmsRequest 实例用于设置发送短信的参数
		$request = new SendSmsRequest();
		$request->setPhoneNumbers($phoneNumbers);
		$request->setSignName($sign);
		$request->setTemplateCode($code);
		$request->setTemplateParam(json_encode($param, JSON_UNESCAPED_UNICODE));

		try {
			$acsResponse = static::getAcsClient()->getAcsResponse($request);
			if ($acsResponse->Code && $acsResponse->Code == 'OK') {
				return true;
			}
			 
				return $this->setError($acsResponse->Message);
		} catch (\Exception $e) {
			return $this->setError(substr($e->getMessage(), 0, 255));
		}
	}

	/**
	 * @param $passport
	 * @param $rand_number
	 */
	private function sendMail($passport, $rand_number)
	{
	}

	/**
	 * 取得AcsClient
	 * @return DefaultAcsClient
	 */
	private function getAcsClient()
	{
		$accessKeyId     = $this->getSetting()->get('extension::sms_aliyun.access_key');
		$accessKeySecret = $this->getSetting()->get('extension::sms_aliyun.access_secret');

		// 暂时不支持多Region
		// cn-hangzhou
		$region = 'cn-hangzhou';

		// 服务结点
		// cn-hangzhou
		$endPointName = 'cn-hangzhou';

		if (static::$acsClient == null) {
			//初始化acsClient,暂不支持region化
			$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

			// 增加服务结点
			DefaultProfile::addEndpoint($endPointName, $region, 'Dysmsapi', 'dysmsapi.aliyuncs.com');

			// 初始化AcsClient用于发起请求
			static::$acsClient = new DefaultAcsClient($profile);
		}

		return static::$acsClient;
	}
}