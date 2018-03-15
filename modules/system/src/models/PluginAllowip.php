<?php namespace System\Models;

use Carbon\Carbon;
use Poppy\Framework\Helper\UtilHelper;

/**
 * App\Models\PluginAllowip
 * @property int $id
 * @property string  $ip_address
 * @property string  $note
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 */
class PluginAllowip extends \Eloquent
{
	protected $table = 'plugin_allowip';

	protected $fillable = [
		'ip_address',
		'note',
	];

	/**
	 * 验证ip是否存在
	 * @param $ip
	 * @return mixed
	 */
	public static function ipExists($ip)
	{
		if (!UtilHelper::isIp($ip)) {
			return false;
		}

		return self::where('ip_address', $ip)->value('id');
	}
}
