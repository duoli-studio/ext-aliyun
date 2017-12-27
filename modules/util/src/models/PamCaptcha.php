<?php namespace Util\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class PamCaptcha extends Model
{
	const TYPE_MOBILE = 'mobile';
	const TYPE_MAIL   = 'mail';

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
