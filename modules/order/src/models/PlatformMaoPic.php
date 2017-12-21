<?php namespace App\Models;


/**
 * App\Models\PlatformTongLog
 * php artisan ide-helper:models "App\Models\PlatformTongLog"
 * @property-read \App\Models\PamAccount $pam
 * @property integer                     $id            ID
 * @property integer                     $order_id      订单ID
 * @property string                      $order_no      订单号
 * @property integer                     $account_id    账号ID
 * @property string                      $account_name  用户名
 * @property string                      $pic_type      图片格式
 * @property string                      $pic_screen    日志图
 * @property string                      $pic_desc      日志
 * @property \Carbon\Carbon              $created_at    创建时间
 * @property \Carbon\Carbon              $updated_at    更新时间
 * @mixin \Eloquent
 */
class PlatformMaoPic extends \Eloquent {

	protected $table = 'platform_mao_picture';

	protected $primaryKey = 'id';

	public $timestamps = true;

	protected $fillable = [
		'order_id',
		'order_no',
		'user_id',
		'description',
		'nick_name',
		'address',
		'created_at',
		'updated_at',
		'status',
	];
}
