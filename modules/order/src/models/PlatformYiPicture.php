<?php namespace App\Models;

use Carbon\Carbon;


/**
 * App\Models\DailianPicture
 * @property integer         $pic_id       日志id
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
class PlatformYiPicture extends \Eloquent {

	const PICTURE_TYPE_PUBLISHER = 'publish';
	const PICTURE_TYPE_SOLDIER   = 'solider';

	protected $table = 'platform_yi_picture';

	protected $primaryKey = 'id';

	protected $fillable = [
		'order_id',
		'order_no',
		'md5_created_at',
		'account_id',
		'account_name',
		'pic_type',
		'pic_screen',
		'pic_desc',
		'account_type',
		'created_at',
	];


	/**
	 * 图片来源描述
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvPictureType($key = null) {
		$desc = [
			self::PICTURE_TYPE_PUBLISHER => '发布者',
			self::PICTURE_TYPE_SOLDIER   => '接单者',
		];
		return kv($desc, $key);
	}
}
