<?php namespace Util\Models;

use Carbon\Carbon;

/**
 * @property int    $id                 id
 * @property string $type               验证类型
 * @property string $num                发送次数
 * @property string $passport           手机号或邮箱
 * @property string $captcha            验证码
 * @property Carbon $disabled_at        失效时间
 */
class PamCaptcha extends \Eloquent
{
	const TYPE_MOBILE = 'mobile';
	const TYPE_MAIL   = 'mail';


	const CON_REGISTER      = 'register';
	const CON_FIND_PASSWORD = 'find_password';
	const CON_ORDER         = 'order';


	protected $table = 'pam_captcha';

	protected $dates = [
		'disabled_at',
	];


	protected $fillable = [
		'type',
		'num',
		'passport',
		'captcha',
		'disabled_at',
	];
}
