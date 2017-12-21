<?php namespace App\Models;

use Carbon\Carbon;

/**
 * App\Models\PlatformBind
 * php artisan ide-helper:models "App\Models\PlatformExport"
 *
 * @property int             $id           id
 * @property string          $account_id
 * @property string          $storage_path 保存的路径
 * @property string          $storage_type 保存的类型
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 * @property-read PamAccount $pam
 * @mixin \Eloquent
 */
class PlatformExport extends \Eloquent {

	protected $table = 'platform_export';


	protected $fillable = [
		'account_id',
		'storage_path',
		'storage_type',
	];

	public function pam() {
		return $this->belongsTo('App\Models\PamAccount', 'account_id', 'account_id');
	}
}
