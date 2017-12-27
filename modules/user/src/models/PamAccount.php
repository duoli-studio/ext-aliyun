<?php namespace User\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id                 id
 * @property string $mobile             手机号
 * @property string $username           用户名称
 * @property string $password           用户密码
 * @property Carbon $logined_at         注册时间
 */
class PamAccount extends Model
{
	protected $table = 'pam_account';

	protected $dates = [
		'logined_at',
	];

	protected $fillable = [
		'mobile',
		'username',
		'password',
		'logined_at',
	];
}
