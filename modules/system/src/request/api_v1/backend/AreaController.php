<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\TreeHelper;
use System\Action\Area;
use System\Action\Area as ActArea;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\SysAreaFilter;
use System\Models\Resources\AreaResource;
use System\Models\SysArea;

class AreaController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                   {post} api_v1/backend/system/area/lists [O]地区-列表
	 * @apiVersion            1.0.0
	 * @apiName               AreaList
	 * @apiGroup              System
	 * @apiPermission         backend
	 * @apiParam   {String}   [parent_id]         上级ID
	 * @apiParam   {Integer}  [page]              分页
	 * @apiParam   {Integer}  [size]              页数
	 * @apiParam   {String}   [append]            附加, 支持 parent
	 * @apiSuccess {Object[]} list                列表
	 * @apiSuccess {Integer}  list.id             ID
	 * @apiSuccess {String}   list.title          标题
	 * @apiSuccess {String}   list.parent_id      父级ID
	 * @apiSuccessExample     list
	 * {
	 *     "list" : [
	 *         {
	 *             "id": 1,
	 *             "title": "北京市",
	 *             "parent_id": 0
	 *         }
	 *     ],
	 *     "pagination": {
	 *         "total": 2,
	 *         "page": 1,
	 *         "size": 20,
	 *         "pages": 1
	 *     }
	 * }
	 */
	public function lists()
	{
		$input              = input();
		$input['parent_id'] = intval($input['parent_id'] ?? 0);

		// append
		$strAppend = data_get($input, 'append');
		$arrAppend = StrHelper::separate(',', $strAppend);
		$append    = [];
		if (in_array('parent', $arrAppend)) {
			$parent = SysArea::select(['id', 'parent_id', 'title'])
				->whereIn('level', [1, 2])->get();

			$parent = $parent->keyBy('id')->toArray();
			if (count($parent)) {
				$Tree = new TreeHelper();
				$Tree->replaceSpace();
				$Tree->init($parent, 'id', 'parent_id', 'title');
				$append['parent'] = $Tree->getTreeArray(0, '', 'kv');
			}
			else {
				$append['parent'] = [];
			}
		}

		/** @var SysArea $Db */
		$Db = SysArea::filter($input, SysAreaFilter::class);

		return SysArea::paginationInfo($Db, AreaResource::class, $append);
	}

	/**
	 * @api                 {post} api_v1/backend/system/area/establish [O]地区-C2E
	 * @apiVersion          1.0.0
	 * @apiName             AreaEstablish
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam {String}   title        标题
	 * @apiParam {Integer}  [parent_id]  父级ID
	 * @apiParam {Integer}  [id]         ID
	 */
	public function establish()
	{
		$input = input();
		$id    = input('id', 0);
		$Area  = (new ActArea())->setPam($this->getJwtBeGuard()->user());
		if (!$Area->establish($input, $id)) {
			return Resp::web(Resp::ERROR, $Area->getError());
		}
		 
			return Resp::web(Resp::SUCCESS, '操作成功');
	}

	/**
	 * @api                 {post} api_v1/backend/system/area/do [O]地区-Do
	 * @apiVersion          1.0.0
	 * @apiName             AreaDo
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam {Integer}  id            角色ID
	 * @apiParam {String}   action        标识 [delete|删除;]
	 */
	public function do()
	{
		$id     = input('id', 0);
		$action = input('action', '');

		$Area = (new ActArea())->setPam($this->getJwtBeGuard()->user());
		if (in_array($action, ['delete'])) {
			$action = camel_case($action);
			if (is_callable([$Area, $action])) {
				if (call_user_func([$Area, $action], $id)) {
					return Resp::web(Resp::SUCCESS, '操作成功');
				}
				 
					return Resp::web(Resp::ERROR, $Area->getError());
			}
		}

		return Resp::web(Resp::ERROR, '不存在的方法');
	}

	/**
	 * @api                 {get} api_v1/backend/system/area/fix [O]地区-fix
	 * @apiVersion          1.0.0
	 * @apiName             AreaFix
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam {Integer}    [max]            最大值
	 * @apiParam {Integer}    [min]            最小值
	 * @apiParam {Integer}    [section]        每次处理多少个
	 * @apiParam {Integer}    [start]          当前开始值
	 * @apiParam {Integer}    [cache]          缓存
	 * @apiParam {Integer}    [total]          全部数量
	 * @apiSuccess {Integer}  [total]          全部数量
	 * @apiSuccess {Integer}  [section]        每次处理数量
	 * @apiSuccess {Integer}  [left]           剩余数量
	 * @apiSuccess {Integer}  [percentage]     百分比
	 * @apiSuccess {Integer}  [continue_time]  延迟时间
	 * @apiSuccess {Integer}  [continue_url]   下一次请求的地址
	 *
	 */
	public function fix()
	{
		// 最大id
		$max = input('max', 0);
		// 最小id
		$min = input('min', 0);
		// 每次更新数量
		$section = intval(input('section', 1)) ?: 1;
		// 需要更新的 start_id
		$start = intval(input('start', 0)) ?: 1;
		// 是否缓存过
		$cached = input('cache', 0);
		// 需要更新的总量
		$total = input('total', 0);

		// 重新清理掉缓存
		if (!$cached) {
			$cached = 1;
		}
		$Db = SysArea::whereRaw('1=1');
		if (!$total) {
			$total = $Db->count();
		}
		if (!$max) {
			$max = $Db->max('id');
		}
		if (!$min) {
			$min = $Db->min('id');
		}

		// ↑↑↑↑↑↑↑↑↑↑↑   获取参数

		// 剩余数
		$left = $Db->whereRaw('id > ?', [$start])
			->count('id');

		$last_id = $start;
		if ($left) {
			$left_items = SysArea::whereRaw('id > ?', [$start])
				->take($section)
				->orderBy('id', 'asc')
				->get(['id', 'title']);

			foreach ($left_items as $item) {
				(new Area())->fix($item->id);
			}
		}
		if ($total) {
			$percentage = round((($total - $left) / $total) * 100);
		}
		else {
			$percentage = '0';
		}

		$url = route_url('backend:api_v1.area.fix', null, [
			'max'     => $max,
			'min'     => $min,
			'section' => $section,
			'start'   => $last_id,
			'total'   => $total,
			'cache'   => $cached,
		]);

		return Resp::web(Resp::SUCCESS, '更新成功', [
			'total'         => $total,
			'section'       => $section,
			'left'          => $left,
			'percentage'    => $percentage,
			'continue_time' => 500, // ms 毫秒
			'continue_url'  => $url,
		]);
	}

	/**
	 * @api                 {get} api_v1/backend/system/area/child 地区-child
	 * @apiVersion          1.0.0
	 * @apiName             AreaChild
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam {Integer}    [max]            最大值
	 * @apiParam {Integer}    [min]            最小值
	 * @apiParam {Integer}    [section]        每次处理多少个
	 * @apiParam {Integer}    [start]          当前开始值
	 * @apiParam {Integer}    [cache]          缓存
	 * @apiParam {Integer}    [total]          全部数量
	 * @apiSuccess {Integer}  [total]          全部数量
	 * @apiSuccess {Integer}  [section]        每次处理数量
	 * @apiSuccess {Integer}  [left]           剩余数量
	 * @apiSuccess {Integer}  [percentage]     百分比
	 * @apiSuccess {Integer}  [continue_time]  延迟时间
	 * @apiSuccess {Integer}  [continue_url]   下一次请求的地址
	 *
	 */
	public function child()
	{
		// 最大id
		$max = input('max', 0);
		// 最小id
		$min = input('min', 0);
		// 每次更新数量
		$section = intval(input('section', 1)) ?: 1;
		// 需要更新的 start_id
		$start = intval(input('start', 0)) ?: 1;
		// 是否缓存过
		$cached = input('cache', 0);
		// 需要更新的总量
		$total = input('total', 0);

		// 重新清理掉缓存
		if (!$cached) {
			$cached = 1;
		}
		$Db = SysArea::whereRaw('1=1');
		if (!$total) {
			$total = $Db->count();
		}
		if (!$max) {
			$max = $Db->max('id');
		}
		if (!$min) {
			$min = $Db->min('id');
		}
		// ↑↑↑↑↑↑↑↑↑↑↑   获取参数

		// 剩余数
		$left = $Db->whereRaw('id > ?', [$start])
			->count('id');

		$last_id = $start;
		if ($left) {
			$left_items = SysArea::whereRaw('id > ?', [$start])
				->take($section)
				->orderBy('id', 'asc')
				->get(['id', 'title']);

			foreach ($left_items as $item) {
				// hasChild 子集 level 等级
				(new Area())->hasChild($item->id);
				(new Area())->level($item->id);
			}
		}
		if ($total) {
			$percentage = round((($total - $left) / $total) * 100);
		}
		else {
			$percentage = '0';
		}

		$url = route_url('backend:api_v1.area.child', null, [
			'max'     => $max,
			'min'     => $min,
			'section' => $section,
			'start'   => $last_id,
			'total'   => $total,
			'cache'   => $cached,
		]);

		return Resp::web(Resp::SUCCESS, '更新成功', [
			'total'         => $total,
			'section'       => $section,
			'left'          => $left,
			'percentage'    => $percentage,
			'continue_time' => 500, // ms 毫秒
			'continue_url'  => $url,
		]);
	}
}