<?php namespace Order\Request\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Models\GameServer;
use App\Models\PlatformOrder;
use Curl\Curl;

/**
 * 游戏接口
 * Class GameController
 * @package App\Http\Controllers\Desktop
 */
class TgpController extends Controller
{

	public function getPlayer($id, $type = 'zj', $bt_list = null)
	{
		if (!in_array($type, ['zj', 'fw'])) {
			return AppWeb::resp(AppWeb::ERROR, '查询类型不存在');
		}
		$order = PlatformOrder::where('order_id', $id)->select(['server_id', 'game_actor', 'order_title'])->first();
		if (!$order) {
			return AppWeb::resp(AppWeb::ERROR, '订单不存在');
		}

		$player = $order->game_actor;


		// 查找腾讯的区服
		$tgpCode = GameServer::where('server_id', $order->server_id)->value('plat_tencent');
		if (!$tgpCode) {
			return AppWeb::resp(AppWeb::ERROR, '发布的订单服务器没有设置TGP区服');
		}

		$param         = [
			'player' => $player,
			'server' => $tgpCode,
			'title'  => $order->order_title,
		];
		$param['type'] = 'search';
		$curl          = new Curl();
		if ($bt_list) {
			$curl->setTimeout(3);
			$param['bt_list'] = $bt_list;
			$data             = $curl->get(env('URL_TGP') . '/tgp/tgp_info', $param);
			return $data;
		}
		// 根据 type 跳转到 战绩 / 符文
		return $curl->get(env('URL_I_TGP') . '/tgp/player', $param);
	}
}
