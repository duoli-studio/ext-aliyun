<?php namespace App\Models;


/**
 * App\Models\PlatformMoney
 * php artisan ide-helper:models 'App\Models\PlatformMoney'
 * @mixin \Eloquent
 * @property integer        $id       id
 * @property integer        $order_id 订单ID
 * @property float          $amount   数量
 * @property boolean        $type     资金变动的类型
 * @property string         $note     备注
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 */
class PlatformMoney extends \Eloquent {

	const TYPE_GET_IN   = 'get_in';  // 接单价格
	const TYPE_PUBLISH  = 'publish';  // 接单价格
	const TYPE_TRANS    = 'trans';
	const TYPE_PUB_ADD  = 'pub_add';
	const TYPE_SD_ADD   = 'sd_add';
	const TYPE_SD_LOST  = 'sd_lost';
	const TYPE_PUB_LOST = 'pub_lost';
	const TYPE_OTHER    = 'other';


	protected $table = 'platform_money';


	protected $fillable = [
		'order_id',
		'amount',
		'type',
		'note',
	];


	/**
	 * @param null $key
	 * @return array|mixed|string
	 */
	public static function kvType($key = null) {
		$desc = [
			self::TYPE_GET_IN   => '接单收入',
			self::TYPE_PUBLISH  => '发单支出',
			self::TYPE_TRANS    => '转单支出',
			self::TYPE_PUB_ADD  => '号主补分',
			self::TYPE_SD_ADD   => '代练补分加钱',
			self::TYPE_SD_LOST  => '代练坏单赔偿',
			self::TYPE_PUB_LOST => '补偿号主费用',
			self::TYPE_OTHER    => '其他支出',
		];
		return kv($desc, $key);
	}
}
