<?php namespace App\Models;

use App\Lemon\Repositories\Sour\LmUtil;


/**
 * App\Models\PluginAllowip
 * @property integer        $ip_id
 * @property string         $ip_addr
 * @property string         $note
 * @property \Carbon\Carbon $created_at
 * @property string         $deleted_at
 * @property \Carbon\Carbon $updated_at
 */
class PluginAllowip extends \Eloquent {


	protected $table = 'plugin_allowip';

	protected $primaryKey = 'ip_id';
	protected $fillable   = [
		'ip_addr',
		'note',
	];

	/**
	 * 验证ip是否存在
	 * @param $ip
	 * @return mixed
	 */
	public static function ipExists($ip) {
		if (!LmUtil::isIp($ip)) {
			return false;
		}
		return self::where('ip_addr', $ip)->value('ip_id');
	}
}
