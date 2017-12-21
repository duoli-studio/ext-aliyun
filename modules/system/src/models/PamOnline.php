<?php namespace System\Models;

use Poppy\Framework\Helper\TimeHelper;

/**
 * App\Models\PamOnline
 *
 * @property integer $account_id
 * @property string  $login_ip IP
 * @property string  $logined_at
 */
class PamOnline extends \Eloquent
{

	protected $table = 'pam_online';

	protected $primaryKey = 'account_id';

	protected $fillable   = [
		'account_id',
		'platform',
		'login_ip',
		'logined_at',
	];
	public    $timestamps = false;

	public static function userNum()
	{
		$num = self::where('logined_at', '>=', TimeHelper::todayStart())->count();
		return $num ? $num : 0;
	}

}