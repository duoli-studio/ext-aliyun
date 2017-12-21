<?php namespace App\Models;

/**
 * \App\Models\PlatformSyncLog
 * @property integer        $id            日志id
 * @property string         $sync_platform 同步平台
 * @property string         $sync_status   状态
 * @property string         $sync_note     日志, 备注
 * @property \Carbon\Carbon $created_at
 * @property string         $deleted_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class PlatformSyncLog extends \Eloquent {


	protected $table = 'platform_sync_log';

	protected $fillable = [
		'accept_platform',
		'sync_status',
		'sync_note',
		'order_id',
		'params',
	];

	const SYNC_STATUS_SUCCESS = 'success';
	const SYNC_STATUS_ERROR   = 'error';


	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvSyncStatus($key = null) {
		$desc = [
			self::SYNC_STATUS_SUCCESS => '成功',
			self::SYNC_STATUS_ERROR   => '失败',
		];
		return kv($desc, $key);
	}

	/**
	 * 记录错误
	 * @param        $platform
	 * @param        $message
	 * @param int    $order_id
	 * @param string $request_code
	 */
	public static function error($platform, $message, $order_id = 0, $request_code = '') {
		self::create([
			'order_id'        => $order_id,
			'sync_status'     => self::SYNC_STATUS_ERROR,
			'accept_platform' => $platform,
			'sync_note'       => $message,
			'params'          => $request_code,
		]);
	}

	/**
	 * 记录正确
	 * @param        $platform
	 * @param        $message
	 * @param int    $order_id
	 * @param string $request_code
	 */
	public static function success($platform, $message, $order_id = 0, $request_code = '') {
		self::create([
			'order_id'        => $order_id,
			'sync_status'     => self::SYNC_STATUS_SUCCESS,
			'accept_platform' => $platform,
			'sync_note'       => $message,
			'params'          => $request_code,
		]);
	}
}
