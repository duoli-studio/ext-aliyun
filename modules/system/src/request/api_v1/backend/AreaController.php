<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Http\Pagination\PageInfo;
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
		$input    = \Input::all();
		$pageInfo = new PageInfo($input);

		/** @var SysArea $Db */
		$Db = SysArea::filter($input, SysAreaFilter::class);
		return SysArea::paginationInfo($Db, $pageInfo, AreaResource::class);
	}


	/**
	 * @api                 {get} api_v1/backend/system/area/establish [O]地区-C2E
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
		if (!$Area->setPam($this->getJwtBeGuard()->user())->establish($input, $id)) {
			return Resp::web(Resp::ERROR, $Area->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}

	/**
	 * @api                 {get} api_v1/backend/system/area/do [O]地区-Do
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
				else {
					return Resp::web(Resp::ERROR, $Area->getError());
				}
			}
		}
		return Resp::web(Resp::ERROR, '不存在的方法');
	}

}
