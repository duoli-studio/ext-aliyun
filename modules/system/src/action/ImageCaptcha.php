<?php namespace System\Action;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Poppy\Framework\Classes\Traits\AppTrait;

class ImageCaptcha
{
	use AppTrait;

	/**
	 * 图像验证码验证
	 * @param $mobile
	 * @param $captcha
	 * @return bool
	 */
	public function check($mobile, $captcha)
	{
		if (!$captcha) {
			return $this->setError(trans('system::action.image_captcha.check_not_input'));
		}

		$captchaCache = \Cache::get('captcha_' . $mobile);
		if (!$captchaCache) {
			return $this->setError(trans('system::action.image_captcha.check_not_exist'));
		}

		if (strtolower($captchaCache) !== strtolower($captcha)) {
			return $this->setError(trans('system::action.image_captcha.check_error'));
		}

		\Cache::forget('captcha_' . $mobile);

		return true;
	}

	/**
	 * @param     $mobile
	 * @param int $length
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function generate($mobile, $length = 4)
	{
		$phraseBuilder = new PhraseBuilder($length);
		//生成验证码图片的Builder对象，配置相应属性
		$builder = new CaptchaBuilder(null, $phraseBuilder);
		//可以设置图片宽高及字体
		$builder->build($width = 180, $height = 50, $font = null);
		//获取验证码的内容
		$phrase = $builder->getPhrase();

		//把内容存入 缓存
		\Cache::put('captcha_' . $mobile, $phrase, 5);
		ob_clean();
		ob_start();
		//生成图片
		$builder->output();
		$content = ob_get_clean();

		return response($content, 200, [
			'Content-Type'  => 'image/png',
			'Cache-Control' => 'no-cache, must-revalidate',
		]);
	}
}