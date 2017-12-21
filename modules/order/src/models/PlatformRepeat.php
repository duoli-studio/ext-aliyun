<?php namespace App\Models;

use App\Lemon\Repositories\Sour\LmArr;
use App\Lemon\Repositories\Sour\LmStr;


/**
 * App\Models\PlatformRepeat
 * php artisan ide-helper:models "App\Models\PlatformRepeat" -W
 * @mixin \Eloquent
 * @property int    $id
 * @property int    $order_id    订单ID
 * @property int    $num         数量
 * @property int    $main_status 当前的ID
 * @property string $status      状态
 * @property bool   $is_over     是否完成
 * @property bool   $is_pay      是否支付完成
 * @property int    $need_user
 * @property string $content
 */
class PlatformRepeat extends \Eloquent
{

	protected $table = 'platform_repeat';


	protected $fillable = [
		'order_id',
		'num',
		'main_status',
		'status',
		'is_over',
		'is_pay',
		'need_user',
		'content',
	];

	public static function descContent($content)
	{
		if (!$content) {
			return '';
		}
		$arrContent = unserialize($content);
		if ($arrContent['status'] == 'no_order') {
			return $arrContent['content'];
		}
		else {
			$status = $arrContent['content'];
			$return = '';
			foreach ($status as $id => $s) {
				$return .= 'ID:' . $id . ' 内容:' . $s['msg'] . '<br>';
			}
			return rtrim($return, '<br>');
		}
	}

	public static function descStatus($status)
	{
		if (!$status) {
			return '';
		}
		$arrStatus = LmStr::parseKey($status);
		$linkStr   = '';
		foreach ($arrStatus as $platform => $id) {
			$route   = 'dsk_platform_status_' . $platform . '.show';
			$link    = route($route, $id);
			$linkStr .= <<<LINK
<a href="$link" class="J_iframe">$platform:$id</a>
LINK;
		}
		return $linkStr;
	}
}
