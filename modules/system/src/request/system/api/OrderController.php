<?php namespace System\Request\System\Api;


use Order\Models\GamePrice;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;

class OrderController extends Controller
{
	use SystemTrait;

	/**
	 * @api                  {get} /api/system/price 段位价格获取
	 * @apiVersion           1.0.0
	 * @apiName              SystemPrice
	 * @apiGroup             System
	 * @apiSuccess {String}  normal                                 类型:normal/good/gold/girl
	 * @apiSuccess {String}  title                                  类型描述(普通猎手/优质猎手/金牌猎手/女猎手):普通猎手
	 * @apiSuccess {String}  games                                  游戏
	 * @apiSuccess {Integer} games.id                               游戏ID
	 * @apiSuccess {String}  games.title                            游戏名称:英雄联盟
	 * @apiSuccess {String}  games.lol                              游戏标识(lol/wz/pubg):lol
	 * @apiSuccess {String}  games.prices                           单位价格
	 * @apiSuccess {Integer} games.prices.dan_n                     段位ID
	 * @apiSuccess {String}  games.prices.dan_n.dan_title           段位名称
	 * @apiSuccess {String}  games.prices.dan_n.field_price         定义实际价格传参key
	 * @apiSuccess {String}  games.prices.dan_n.field_mp            定义市场价格传参key
	 * @apiSuccess {String}  games.prices.dan_n.field_unit          定义结算方式传参key
	 * @apiSuccess {Float}   games.prices.dan_n.price               实际价格
	 * @apiSuccess {Float}   games.prices.dan_n.market_price        市场价格
	 * @apiSuccess {String}  games.prices.dan_n.standard            结算标准(局/小时):元/局
	 * @apiSuccessExample    data:
	 * {
	 *      "normal":{
	 *          "title":"普通猎手",
	 *          "games":[{
	 *              "id":"1",
	 *              "title":"英雄联盟",
	 *              "name":"lol",
	 *              "prices":{
	 *                    "dan_1":{
	 *                              "dan_title":"英勇黄铜",
	 *                              "field_price":"normal_1_1_price",
	 *                              "field_mp":"normal_1_1_mp",
	 *                              "field_unit":"normal_1_1_unit",
	 *                              "price":"8.00",
	 *                              "market_price":"0.00",
	 *                              "standard":"元/局"
	 *                             }
	 *                        }
	 *                     }]
	 *               }
	 *
	 * }
	 */
	public function price()
	{
		$prices = GamePrice::with(['dan', 'games'])->get();
		$array  = [];
		foreach ($prices as $key => $value) {
			switch ($value->type) {
				case GamePrice::TYPE_NORMAL:
					$array[$value->type]['title'] = '普通猎手';
					break;
				case GamePrice::TYPE_GOOD:
					$array[$value->type]['title'] = '优质猎手';
					break;
				case GamePrice::TYPE_GOLD:
					$array[$value->type]['title'] = '金牌猎手';
					break;
				case GamePrice::TYPE_GIRL:
					$array[$value->type]['title'] = '女猎手';
					break;
			}
			$array[$value->type]['games'][] = [
				'id'     => $value->games->id,
				'title'  => $value->games->title,
				'name'   => $value->games->name,
				'prices' => [
					'dan_' . $value->dan_id => [
						'dan_title'    => $value->dan->title,
						'field_price'  => $value->type . '_' . $value->games->id . '_' . $value->dan_id . '_price',
						'field_mp'     => $value->type . '_' . $value->games->id . '_' . $value->dan_id . '_mp',
						'field_unit'   => $value->type . '_' . $value->games->id . '_' . $value->dan_id . '_unit',
						'price'        => $value->price,
						'market_price' => $value->market_price,
						'standard'     => $value->standard == 'game' ? '元/局' : '元/小时',
					],
				],
			];
		}

		return $this->getResponse()->json([
			'data'    => $array,
			'message' => '获取数据成功！',
			'status'  => Resp::SUCCESS,
		], 200, [], JSON_UNESCAPED_UNICODE);
	}

}