<?php namespace System\Models;

use Carbon\Carbon;

/**
 *
 * @property int $id           ID
 * @property int $account_id   账户ID
 * @property string  $account_type 账户类型
 * @property string  $type         登录日志类型, success, error, warning
 * @property string  $ip           IP
 * @property string  $area_text    地区方式
 * @property string  $area_name    地区名字
 * @property Carbon  $created_at   创建时间
 * @property Carbon  $updated_at   修改时间
 */
class PamLog extends \Eloquent
{
	protected $table = 'pam_log';

	protected $fillable = [
		'account_id',
		'account_type',
		'type',
		'ip',
		'area_text',   // 山东济南联通
		'area_name',   // 济南
	];
}

