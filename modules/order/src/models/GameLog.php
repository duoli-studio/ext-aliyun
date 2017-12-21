<?php namespace App\Models;

use Carbon\Carbon;


/**
 * App\Models\DailianLog
 * @property integer         $id          日志id
 * @property integer         $order_id    订单id
 * @property integer         $account_id  账户id
 * @property string          $log_content 日志
 * @property string          $log_type    create:创建订单, assign:接手, progress: 更新进程, over:完成订单, examine: 提交订单到待审核状态, exception: 提交异常
 * @property string          $log_by
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 * @property integer         $parent_id   主账号ID
 * @property integer         $editor_id   编辑者ID
 * @property-read PamAccount $pam
 */
class GameLog extends \Eloquent {

	const LOG_TYPE_ADD_MONEY        = 'add_money';
	const LOG_TYPE_ADD_TIME         = 'add_time';
	const LOG_TYPE_ASSIGN           = 'assign';
	const LOG_TYPE_CANCEL           = 'cancel';
	const LOG_TYPE_PUB_CANCEL       = 'pub_cancel';
	const LOG_TYPE_CREATE           = 'create';
	const LOG_TYPE_PUBLISH          = 'publish';
	const LOG_TYPE_EXCEPTION        = 'exception';
	const LOG_TYPE_CANCEL_EXCEPTION = 'cancel_exception';
	const LOG_TYPE_MANAGER          = 'manager';
	const LOG_TYPE_OVER             = 'over';
	const LOG_TYPE_PROGRESS         = 'progress';
	const LOG_TYPE_SUBMIT_OVER      = 'submit_over';
	const LOG_TYPE_EXAMINE          = 'examine';
	const LOG_TYPE_QUASH            = 'quash';
	const LOG_TYPE_DELETE           = 'delete';
	const LOG_TYPE_LOCK             = 'lock';
	const LOG_TYPE_UNLOCK           = 'unlock';

	protected $table = 'game_log';


	protected $fillable = [
		'order_id',
		'account_id',
		'parent_id',
		'editor_id',
		'log_by',
		'log_type',
		'log_content',
	];


	public function pam() {
		return $this->belongsTo('App\Models\PamAccount', 'editor_id', 'account_id');
	}

	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvLogBy($key = null) {
		$desc = [
			PamAccount::ACCOUNT_TYPE_FRONT   => '用户',
			PamAccount::ACCOUNT_TYPE_DESKTOP => '管理员',
			PamAccount::DESKTOP_SYSTEM       => '系统',
			PamAccount::FRONT_SUBUSER        => '子账号',
		];
		return kv($desc, $key);
	}

	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvLogType($key = null) {
		$desc = [
			self::LOG_TYPE_CREATE           => '创建订单',
			self::LOG_TYPE_ASSIGN           => '分配订单',
			self::LOG_TYPE_PUBLISH          => '发布订单',
			self::LOG_TYPE_PROGRESS         => '进度日志',
			self::LOG_TYPE_OVER             => '完成',
			self::LOG_TYPE_MANAGER          => '管理员',
			self::LOG_TYPE_CANCEL_EXCEPTION => '取消异常',
			self::LOG_TYPE_EXCEPTION        => '提交异常',
			self::LOG_TYPE_LOCK             => '锁定订单',
			self::LOG_TYPE_UNLOCK           => '解锁订单',
			self::LOG_TYPE_PUB_CANCEL       => '发单者撤销',
			self::LOG_TYPE_SUBMIT_OVER      => '提交完成到待审核状态',
		];
		return kv($desc, $key);
	}
}
