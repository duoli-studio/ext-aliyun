<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\TreeHelper;
use System\Action\Help;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\SysHelpFilter;
use System\Models\Resources\HelpResource;
use System\Models\SysCategory;
use System\Models\SysHelp;

class HelpController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                   {post} api_v1/backend/system/help/lists [O]帮助-列表
	 * @apiVersion            1.0.0
	 * @apiName               HelpList
	 * @apiGroup              System
	 * @apiPermission         backend
	 * @apiParam   {Integer}  [id]                帮助ID
	 * @apiParam   {Integer}  [cat_id]            分类ID
	 * @apiParam   {Integer}  [page]              分页
	 * @apiParam   {Integer}  [size]              页数
	 * @apiParam   {String}   [append]            附加, 支持 category
	 * @apiSuccess {Object[]} list                列表
	 * @apiSuccess {Integer}  list.id             ID
	 * @apiSuccess {String}   list.cat_id         分类ID
	 * @apiSuccess {String}   list.cat_title      分类标题
	 * @apiSuccess {String}   list.title          标题
	 * @apiSuccess {String}   list.content        帮助内容
	 * @apiSuccessExample     list
	 * {
	 *     "list" : [
	 *         {
	 *             "id": 6,
	 *             "cat_id": 6,
	 *             "cat_title": "打单问题",
	 *             "title": "猎手接单中是否可以邀请好友?",
	 *             "content": "请前往查看"
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
		if (!empty($input['id']) && !empty($input['cat_id'])) {
			return Resp::web(Resp::ERROR, '帮助ID 和 分类ID 不能同时查询');
		}

		// append
		$strAppend = data_get($input, 'append');
		$arrAppend = StrHelper::separate(',', $strAppend);
		$append    = [];
		if (in_array('category', $arrAppend)) {
			$parent = SysCategory::select(['id', 'parent_id', 'title'])
				->where('type', SysCategory::TYPE_HELP)->get();

			$parent = $parent->keyBy('id')->toArray();
			if (count($parent)) {
				$Tree = new TreeHelper();
				$Tree->replaceSpace();
				$Tree->init($parent, 'id', 'parent_id', 'title');
				$append['category'] = $Tree->getTreeArray(0, '', 'kv');
			}
			else {
				$append['category'] = [];
			}
		}

		/** @var SysHelp $Db */
		$Db = SysHelp::filter($input, SysHelpFilter::class);

		return SysHelp::paginationInfo($Db, HelpResource::class, $append);
	}

	/**
	 * @api                  {post} api_v1/backend/system/help/establish [O]帮助-C2E
	 * @apiVersion           1.0.0
	 * @apiName              HelpEstablish
	 * @apiGroup             System
	 * @apiParam  {Integer}  [id]             帮助ID
	 * @apiParam  {Integer}  cat_id           分类ID
	 * @apiParam  {String}   title            标题
	 * @apiParam  {String}   content          内容
	 */
	public function establish()
	{
		$id    = input('id', 0);
		$input = input();
		$Help  = new Help();
		if (!$Help->setPam($this->getJwtBeGuard()->user())->establish($input, $id)) {
			return Resp::web(Resp::ERROR, $Help->getError());
		}
		 
			return Resp::web(Resp::SUCCESS, $Help->getSuccess());
	}

	/**
	 * @api                  {post} api_v1/backend/system/help/do [O]帮助-操作
	 * @apiVersion           1.0.0
	 * @apiName              HelpDo
	 * @apiGroup             System
	 * @apiParam {Integer}   id             帮助ID
	 * @apiParam {String}    action         标识
	 *     [delete|删除]
	 */
	public function do()
	{
		$id     = input('id', 0);
		$action = input('action', '');
		$Help   = (new Help())->setPam($this->getJwtBeGuard()->user());
		if (in_array($action, ['delete'])) {
			$action = camel_case($action);
			if (is_callable([$Help, $action])) {
				if (call_user_func([$Help, $action], $id)) {
					return Resp::web(Resp::SUCCESS, '操作成功');
				}
				 
					return Resp::web(Resp::ERROR, $Help->getError());
			}
		}

		return Resp::web(Resp::ERROR, '不存在的方法');
	}
}