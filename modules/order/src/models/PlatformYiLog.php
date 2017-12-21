<?php namespace App\Models;


/**
 * App\Models\PlatformTongLog
 * php artisan ide-helper:models "App\Models\PlatformTongLog"
 * @property-read \App\Models\PamAccount $pam
 * @property integer                     $id          ID
 * @property integer                     $msg_type    消息类型
 * @property string                      $tong_uid    代练通用户ID
 * @property integer                     $order_id    订单ID
 * @property string                      $nick_name   昵称
 * @property string                      $msg_content 留言内容
 * @property \Carbon\Carbon              $created_at  创建时间
 * @property \Carbon\Carbon              $updated_at  更新时间
 * @mixin \Eloquent
 */
class PlatformYiLog extends \Eloquent {


	protected $table = 'platform_yi_log';

	protected $primaryKey = 'id';

	public $timestamps = true;

	protected $fillable = [
		'msg_type',
		'md5_created_at',
		'account_id',
		'account_name',
		'order_id',
		'order_no',
		'nick_name',
		'msg_content',
		'created_at',
	];
	
}
