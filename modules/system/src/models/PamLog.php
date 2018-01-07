<?php namespace System\Models;


/**
 * App\Models\PamLog
 *
 * @property integer        $log_id
 * @property integer        $account_id
 * @property integer        $parent_id    父账号ID
 * @property string         $account_name 账户名
 * @property string         $account_type 账户类型
 * @property string         $log_content
 * @property string         $log_type     登录日志类型, success, error, warning
 * @property string         $log_ip       IP
 * @property \Carbon\Carbon $created_at
 * @property string         $deleted_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $log_area_text
 * @property string         $log_area_name
 * @property integer        $log_area_id  地区ID, 以此判定跨区登陆
 */
class PamLog extends \Eloquent
{
	protected $table = 'pam_log';

	protected $primaryKey = 'log_id';

	protected $fillable = [
		'account_id',
		'parent_id',
		'ip',
		'area_text',   // 山东济南联通
		'area_name',   // 济南
	];


}

