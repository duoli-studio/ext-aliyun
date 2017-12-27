<?php namespace User\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\PamAccount $pam
 * @property integer                     $id                 id
 * @property string                      $mobile             手机号
 * @property string                      $username           用户名称
 * @property string                      $password           用户密码
 * @property datetime                    $logined_at         注册时间
 */
class PamAccount extends Model
{
	protected $table      = 'pam_account';

	protected $primaryKey = 'id';

	public    $timestamps = false;

	protected $fillable   = [
		'id',
		'mobile',
		'username',
		'password',
		'logined_at',
	];
}
