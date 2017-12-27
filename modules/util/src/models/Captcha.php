<?php namespace Util\models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class Captcha extends Model
{
    //
	protected $table = 'pam_captcha';

	protected $primaryKey = 'id';

	protected $fillable = [
		'id',
		'type',
		'num',
		'passport',
		'captcha',
		'careated_at',
		'disabled_at',
		'updated_at',
	];

}
