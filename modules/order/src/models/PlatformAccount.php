<?php namespace App\Models;

use Illuminate\Support\Collection;


/**
 * App\Models\PlatformAccount
 * php artisan ide-helper:models "App\Models\PlatformAccount"
 *
 * @property-read \App\Models\PamAccount $pam
 * @property integer                     $id                 id
 * @property string                      $platform           平台  yi/tong/mao
 * @property string                      $tong_nickname      代练通昵称
 * @property string                      $tong_userid        代练通用户id
 * @property string                      $tong_account       代练通账号
 * @property string                      $tong_password      代练通密码
 * @property string                      $tong_payword       代练通支付密码
 * @property string                      $yi_userid          易代练userid
 * @property string                      $yi_nickname        易代练昵称
 * @property string                      $yi_app_key         易代练key
 * @property string                      $yi_app_secret      易代练secret
 * @property string                      $yi_payword         易代练支付密码
 * @property string                      $mao_account        代练猫账号
 * @property string                      $mao_password       代练猫密码
 * @property string                      $mao_payword        代练猫支付密码
 * @property string                      $contact            联系人
 * @property string                      $qq                 联系人QQ
 * @property string                      $mobile             手机号
 * @property integer                     $order_num          进行中的订单数量
 * @property string                      $note               备注
 * @property \Carbon\Carbon              $created_at
 * @property \Carbon\Carbon              $updated_at
 * @mixin \Eloquent
 * @property string                      $deleted_at
 * @property string                      $mama_account       代练妈妈key
 * @property string                      $mama_password      代练妈妈secret
 * @property string                      $mama_payword       代练妈妈支付密码
 *
 * @property string                      $baozi_userid       电竞包子userid
 * @property string                      $baozi_nickname     电竞包子昵称
 * @property string                      $baozi_app_key      电竞包子key
 * @property string                      $baozi_app_secret   电竞包子secret
 * @property string                      $baozi_payword      电竞包子支付密码
 * @property int                         $yq_phone           17代练手机号
 * @property string                      $yq_account         17代练appid
 * @property string                      $yq_auth_key        17代练key
 * @property string                      $yq_payword         17代练支付密码
 * @property string                      $yq_userid          17代练用户id
 */
class PlatformAccount extends \Eloquent
{

	protected $table = 'platform_account';

	public $timestamps = true;

	const PLATFORM_YI    = 'yi';
	const PLATFORM_BAOZI = 'baozi';
	const PLATFORM_TONG  = 'tong';
	const PLATFORM_MAO   = 'mao';
	const Employee       = 'employee';
	const PLATFORM_MAMA  = 'mama';
	const PLATFORM_YQ    = 'yq';


	protected $fillable = [
		'platform',
		'tong_nickname',
		'tong_userid',
		'tong_account',
		'tong_password',
		'tong_payword',
		'mao_account',
		'mao_password',
		'mao_payword',
		'yi_userid',
		'yi_app_key',
		'yi_app_secret',
		'yi_payword',
		'note',
		'contact',
		'mobile',
		'qq',
		'mama_account',
		'mama_password',
		'mama_payword',
		'baozi_userid',
		'baozi_app_key',
		'baozi_app_secret',
		'baozi_payword',
		'baozi_nickname',
		'yq_phone',
		'yq_account',
		'yq_auth_key',
		'yq_payword',
		'yq_userid',
	];

	/**
	 * 获取缓存的账号信息， 用于单进程对服务器进行加速
	 * @param $id
	 * @return PlatformAccount|null
	 */
	public static function getCacheItem($id)
	{
		static $accounts = [];
		if (!$accounts) {
			/** @var Collection $all */
			$all = self::get();//从数据库获取数据
			if ($all->isEmpty()) {
				return null;
			} else {
				$all->each(function($item) use (& $accounts) {
					$accounts[$item->id] = $item;
				});
			}
		}
		if (isset($accounts[$id])) {
			return $accounts[$id];
		} else {
			return null;
		}
	}

	/**
	 * 获取note
	 * @param $id
	 * @return mixed|string
	 */
	public static function getNote($id)
	{
		static $note;
		if (!$note) {
			$note = self::kvLinear();
		}
		if (isset($note[$id])) {
			return $note[$id];
		} else {
			return '';
		}
	}


	/**
	 * 平台描述
	 * @param null $key
	 * @return array|mixed
	 */
	public static function kvPlatform($key = null)
	{
		$desc = [
			self::PLATFORM_YI    => '易代练',
			self::PLATFORM_YQ    => '17代练',
			self::PLATFORM_BAOZI => '电竞包子',
			self::PLATFORM_TONG  => '代练通',
			self::PLATFORM_MAO   => '代练猫',
			self::PLATFORM_MAMA  => '代练妈妈',
		];
		return kv($desc, $key);
	}

	/**
	 * 根据平台来获取linear
	 * @param $platform
	 * @return array
	 */
	public static function kvLinear($platform = null)
	{
		if ($platform) {
			return self::where('platform', $platform)->lists('note', 'id');
		} else {
			return self::lists('note', 'id');
		}
	}

}
