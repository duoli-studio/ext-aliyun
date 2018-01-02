<?php namespace Util\Util\Action;

/**
 * 基本账户操作
 */

use Carbon\Carbon;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use System\Classes\Traits\SystemTrait;
use Util\Models\PamCaptcha;

class Util
{
	use SystemTrait;

	/**
	 * 生成验证码
	 * @param int    $expired
	 * @param string $hidden_str
	 * @return string
	 */
	public function genOnceVerifyCode($expired = 10, $hidden_str = '')
	{
		// 生成 10 分钟的有效 code
		$now     = Carbon::now();
		$unix    = Carbon::now()->addMinutes($expired)->timestamp;
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

	public function sendCaptcha($passport)
	{
		// 验证数据格式
		if (UtilHelper::isEmail($passport)) {
			$type       = PamCaptcha::TYPE_MAIL;
			$expiredMin = $this->getSetting()->get('util::captcha.mail_expired_minute');
		}
		elseif (UtilHelper::isMobile($passport)) {
			$type       = PamCaptcha::TYPE_MOBILE;
			$expiredMin = $this->getSetting()->get('util::captcha.sms_expired_minute');
		}
		else {
			return $this->setError(trans('util::act.util.send_captcha_passport_format_error'));
		}
		// 发送验证码
		// todo


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
		}
		else {
			$captcha->num  += 1;
			$captcha->type = $type;
		}

		// Captcha In Db or Re Generate.
		// todo : send captcha
		$randNo               = $captcha->captcha;


		$captcha->disabled_at = $expired;
		$captcha->save();

		// 超出指定时间的验证码需要删除
		// todo 事件
		return true;
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
				return $this->setError('安全校验已经过期, 请重新请求');
			}
			// dd($data[$key]);
			if (!isset($data[$key])) {
				return $this->setError('安全校验已经过期, 请重新请求');
			}
			else {
				unset($data[$key]);
				$this->hiddenStr = $split[2];
				\Cache::forever($cacheKey, $data);
				return true;
			}
		}
		else {
			return $this->setError('非法请求');
		}
	}

}