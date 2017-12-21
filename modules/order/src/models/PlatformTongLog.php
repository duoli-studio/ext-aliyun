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
class PlatformTongLog extends \Eloquent {

	const MSG_TYPE_SYSTEM = 10;
	const MSG_TYPE_USER   = 11;

	protected $table = 'platform_tong_log';

	protected $primaryKey = 'id';

	public $timestamps = true;

	protected $fillable = [
		'msg_type',
		'tong_uid',
		'md5_created_at',
		'order_id',
		'nick_name',
		'msg_content',
		'created_at',
	];

	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvMsgType($key = null) {
		$desc = [
			self::MSG_TYPE_SYSTEM => '系统消息',
			self::MSG_TYPE_USER   => '用户消息',
		];
		return kv($desc, $key);
	}
}
