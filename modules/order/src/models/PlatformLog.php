<?php namespace App\Models;


/**
 * App\Models\PlatformLog
 * @property integer                     $log_id      日志id
 * @property integer                     $order_id    订单id
 * @property string                      $log_content 日志
 * @property string                      $log_type    create:创建订单, assign:接手, progress: 更新进程, over:完成订单, examine: 提交订单到待审核状态, exception: 提交异常
 * @property string                      $log_by
 * @property \Carbon\Carbon              $created_at
 * @property string                      $deleted_at
 * @property \Carbon\Carbon              $updated_at
 * @property-read \App\Models\PamAccount $account
 */
class PlatformLog extends \Eloquent {

	const LOG_BY_USER     = 'user';
	const LOG_BY_PLATFORM = 'platform';

	const ORDER_STATUS_OVER = 'over';
	const ORDER_STATUS_ING  = 'ing';

	protected $table = 'platform_log';

	protected $primaryKey = 'log_id';

	protected $fillable = [
		'order_id',
		'log_content',
		'log_by',
		'account_id',
		'type',
	];


	public function pam() {
		return $this->belongsTo('App\Models\PamAccount', 'account_id', 'account_id');
	}


	/**
	 *  * 记录日志
	 * @param PlatformOrder $order
	 * @param               $editor_id
	 * @param               $message
	 * @return bool
	 */
	public static function record($order, $editor_id, $message) {
		$data = [
			'log_content' => $message,
			'order_id'    => $order->order_id,
			'log_by'      => $editor_id ? self::LOG_BY_USER : self::LOG_BY_PLATFORM,
			'account_id'  => $editor_id,
		];
		self::create($data);
		$order->last_log = $message;
		$order->save();
		return true;
	}


	/**
	 * 取消状态描述
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvSmsType($key = null) {
		$desc = [
			self::ORDER_STATUS_OVER => '完成订单',
			self::ORDER_STATUS_ING  => '接手订单',

		];
		return kv($desc, $key);
	}
}
