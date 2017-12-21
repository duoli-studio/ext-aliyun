<?php namespace App\Models;

/**
 * App\Models\PlatformBind
 * php artisan ide-helper:models "App\Models\PlatformBind"
 * @property integer                     $id                  id
 * @property integer                     $account_id          管理员账号ID
 * @property string                      $account_type        账号类型
 * @property integer                     $platform_account_id 平台账号ID
 * @property string                      $platform
 * @property \Carbon\Carbon              $created_at
 * @property string                      $deleted_at
 * @property \Carbon\Carbon              $updated_at
 * @property-read \App\Models\PamAccount $pam
 * @mixin \Eloquent
 */
class PlatformBind extends \Eloquent {

	protected $table = 'platform_bind';

	public $timestamps = true;

	protected $fillable = [
		'account_id',
		'account_type',
		'platform',
		'platform_account_id',
	];

	public function pam() {
		return $this->belongsTo('App\Models\PamAccount', 'account_id', 'account_id');
	}
}
