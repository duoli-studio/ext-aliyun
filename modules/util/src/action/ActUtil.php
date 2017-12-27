<?php namespace Util\Action;

/**
 * 基本账户操作
 */

use Carbon\Carbon;
use Poppy\Framework\Traits\AppTrait;
use Util\Models\Captcha;

class ActUtil
{

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
		} else {
			$data = [
				$key => $str,
			];
		}
		\Cache::forever($cacheKey, $data);
		return openssl_encrypt($str, self::CRYPT_METHOD, substr(config('app.key'), 0, 32));
	}

	public function getCaptcha($expired, $passport, $captcha)
	{
		//
		$now  = Carbon::now();
		$unix = Carbon::now()->addMinute($expired);
		if (!$captcha) {
			for ($i = 0; $i < 6; $i++) {
				$captcha .= rand(0, 9);
			}
		}
		//发送验证码
		//如果发送成功就写入数据库
		if (preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/', $passport)) {
			$type = 'email';
		} else if (preg_match('/^1[3|4|5|8][0-9]\d{4,8}$/', $passport)) {
			$type = 'mobile';
		} else {
			return AppTrait::setError('发送失败');
		}
		$count = Captcha::where('passport', $passport)
			->whereDay('created_at','=',date('d'))
			->count();
		if ($count > 0) {
			$num = $count + 1;
		} else {
			$num = 1;
		}
		$data   = [
			'passport'    => $passport,
			'captcha'     => $captcha,
			'type'        => $type,
			'num'         => $num,
			'created_at'  => $now,
			'disabled_at' => $unix,
		];
		$result = Captcha::create($data);
		if ($result) {
			return '发送成功';
		} else {
			return AppTrait::setError('发送失败');
		}
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
			} else {
				unset($data[$key]);
				$this->hiddenStr = $split[2];
				\Cache::forever($cacheKey, $data);
				return true;
			}
		} else {
			return $this->setError('非法请求');
		}
	}

}