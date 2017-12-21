<?php namespace App\Models;

use Carbon\Carbon;


/**
 * App\Models\DailianPicture
 * @property integer         $id       日志id
 * @property integer         $order_id     订单id
 * @property integer         $account_id   账户id
 * @property string          $pic_screen   日志图
 * @property string          $pic_desc     日志
 * @property string          $pic_type
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 * @property-read PamAccount $account
 * @property string          $account_type 账户类型
 */
class GamePicture extends \Eloquent {

	const PICTURE_TYPE_PUBLISHER        = 'publisher';
	const PICTURE_TYPE_SOLDIER_OVER     = 'soldier_over';
	const PICTURE_TYPE_SOLDIER_PROGRESS = 'soldier_progress';

	protected $table = 'game_picture';


	protected $fillable = [
		'order_id',
		'account_id',
		'pic_screen',
		'pic_type',
		'pic_desc',
		'account_type',
	];


	public function pam() {
		return $this->belongsTo('App\Models\PamAccount', 'account_id', 'account_id');
	}


	/**
	 * 图片来源描述
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvPictureType($key = null) {
		$desc = [
			self::PICTURE_TYPE_PUBLISHER        => '发布者',
			self::PICTURE_TYPE_SOLDIER_OVER     => '接单者完成图',
			self::PICTURE_TYPE_SOLDIER_PROGRESS => '接单者进度图',
		];
		return kv($desc, $key);
	}
}
