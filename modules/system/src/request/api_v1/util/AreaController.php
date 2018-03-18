<?php namespace System\Request\ApiV1\Util;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\UtilHelper;
use System\Classes\Traits\SystemTrait;
use System\Models\SysArea;

class AreaController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                  {get} api_v1/util/area/code [O]地区代码
	 * @apiVersion           1.0.0
	 * @apiName              UtilAreaCode
	 * @apiGroup             Util
	 * @apiSuccess {String}    id                  ID
	 * @apiSuccess {String}    title               地区名称
	 * @apiSuccess {Object[]}  cities              所属城市子集
	 * @apiSuccess {Integer}   cities.id           城市ID
	 * @apiSuccess {String}    cities.title        城市名称
	 * @apiSuccess {Object[]}  cities.areas        地区信息
	 * @apiSuccess {Integer}   cities.areas.id     地区ID
	 * @apiSuccess {String}    cities.areas.title  地区名称
	 * @apiSuccessExample    城市数据
	 * [
	 *     {
	 *         "id": 1,
	 *         "title": "北京市",
	 *         "cities": [
	 *             {
	 *                 "id": 3,
	 *                 "title": "北京市",
	 *                 "areas": [
	 *                     {
	 *                         "id": 4,
	 *                         "title": "东城区"
	 *                     },
	 *                     ...
	 *                     {
	 *                         "id": 14,
	 *                         "title": "大兴区"
	 *                     }
	 *                 ]
	 *             }
	 *         ]
	 *     },
	 *     ...
	 * ]
	 */
	public function code()
	{
		$items = SysArea::get()->toArray();
		$array = UtilHelper::genTree($items, 'id', 'parent_id', 'areas');

		$return = [];
		foreach ($array as $province_key => $province_value) {
			$new_province_value = [
				'id'    => $province_value['id'],
				'title' => $province_value['title'],
			];
			if (!isset($province_value['areas'])) {
				continue;
			}
			foreach ($province_value['areas'] as $city_key => $city_value) {
				$new_city_value = [
					'id'    => $city_value['id'],
					'title' => $city_value['title'],
				];
				if (!isset($city_value['areas'])) {
					continue;
				}
				foreach ($city_value['areas'] as $area_key => $area_value) {
					$new_area_value            = [
						'id'    => $area_value['id'],
						'title' => $area_value['title'],
					];
					$new_city_value['areas'][] = $new_area_value;
				}

				$new_province_value['cities'][] = $new_city_value;
			}
			$return[$province_key] = $new_province_value;
		}

		return Resp::web(Resp::SUCCESS, '获取数据成功', $return);
	}
}