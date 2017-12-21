<?php namespace App\Models;


/**
 * App\Models\PlatformTongLog
 * php artisan ide-helper:models "App\Models\PlatformTongLog"
 *
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
 * @property int                         $account_id  用户ID
 * @property string                      $order_no    订单号
 * @property int                         $chatid
 * @property string                      $mao_user_id
 */
class PlatformMaoLog extends \Eloquent {

	const MSG_USER             = 0;
	const MSG_USER_OPERATION   = 1;
	const MSG_KF               = 2;
	const MSG_KF_OPERATION     = 3;
	const MSG_SYSTEM_OPERATION = 4;

	protected $table = 'platform_mao_log';

	protected $primaryKey = 'id';

	public $timestamps = true;

	protected $fillable = [
		'msg_type',
		//'md5_created_at',
		'account_id',
		//'account_name',
		'order_id',
		'order_no',
		'nick_name',
		'msg_content',
		'created_at',
		'chatid',
		'mao_user_id',
	];

	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvMsgType($key = null) {
		$desc = [
			self::MSG_USER             => '客户留言',
			self::MSG_USER_OPERATION   => '客户操作',
			self::MSG_KF               => '客服留言',
			self::MSG_KF_OPERATION     => '客服操作',
			self::MSG_SYSTEM_OPERATION => '系统操作',
		];
		return kv($desc, $key);
	}

}
