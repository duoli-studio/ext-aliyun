<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\SystemTrait;
use System\Action\Category as ActCategory;
use System\Models\Filters\SysCategoryFilter;
use System\Models\Resources\CategoryResource;
use System\Models\SysCategory;

class CategoryController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                   {post} api_v1/backend/system/category/lists [O]分类-列表
	 * @apiVersion            1.0.0
	 * @apiName               CategoryList
	 * @apiGroup              System
	 * @apiPermission         backend
	 * @apiParam   {Integer}  [id]                分类ID
	 * @apiParam   {String}   [type]              类型
	 *     [help|帮助;activity|活动]
	 * @apiParam   {Integer}  [page]              分页
	 * @apiParam   {Integer}  [size]              页数
	 * @apiSuccess {Object[]} list                列表
	 * @apiSuccess {Integer}  list.id             ID
	 * @apiSuccess {Integer}  list.parent_id      父级ID
	 * @apiSuccess {String}   list.type           类型
	 * @apiSuccess {String}   list.title          标题
	 * @apiSuccessExample     list
	 * {
	 *     "list" : [
	 *         {
	 *             "id": 1,
	 *             "parent_id": 0,
	 *             "type": "help",
	 *             "title": "关于猎手"
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
		$input = \Input::all();
		if (!empty($input['id']) && !empty($input['type'])) {
			return Resp::web(Resp::ERROR, '分类ID 和 类型 不能同时查询');
		}
		if (!empty($input['type'])) {
			if (!in_array($input['type'], [
				SysCategory::TYPE_HELP,
				SysCategory::TYPE_ACTIVITY,
			])) {
				return Resp::web(Resp::ERROR, '类型不合法');
			}
		}

		$pageInfo = new PageInfo($input);

		/** @var SysCategory $Db */
		$Db = SysCategory::filter($input, SysCategoryFilter::class);
		return SysCategory::paginationInfo($Db, $pageInfo, CategoryResource::class);
	}

	/**
	 * @api                  {post} api_v1/backend/system/category/establish [O]分类-C2E
	 * @apiVersion           1.0.0
	 * @apiName              CategoryEstablish
	 * @apiGroup             System
	 * @apiParam  {Integer}  [id]             分类ID
	 * @apiParam  {Integer}  parent_id        父级ID
	 * @apiParam  {String}   type             类型
	 *     [help|帮助;activity|活动]
	 * @apiParam  {String}   title            标题
	 */
	public function establish()
	{
		$id    = input('id', 0);
		$input = input();
		/** @var ActCategory $category */
		$Category = new ActCategory();
		if (!$Category->setPam($this->getJwtBeGuard()->user())->establish($input, $id)) {
			return Resp::web(Resp::ERROR, $Category->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, $Category->getSuccess());
		}
	}

	/**
	 * @api                  {post} api_v1/backend/system/category/do [O]分类-操作
	 * @apiVersion           1.0.0
	 * @apiName              CategoryDo
	 * @apiGroup             System
	 * @apiParam {Integer}   id             分类ID
	 * @apiParam {String}    action         标识
	 *     [delete|删除]
	 */
	public function do()
	{
		$id       = input('id', 0);
		$action   = input('action', '');
		$Category = (new ActCategory())->setPam($this->getJwtBeGuard()->user());
		if (in_array($action, ['delete'])) {
			$action = camel_case($action);
			if (is_callable([$Category, $action])) {
				if (call_user_func([$Category, $action], $id)) {
					return Resp::web(Resp::SUCCESS, '操作成功');
				}
				else {
					return Resp::web(Resp::ERROR, $Category->getError());
				}
			}
		}
		return Resp::web(Resp::ERROR, '不存在的方法');
	}
}
