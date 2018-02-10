<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\PamFilter;
use System\Models\Filters\RoleFilter;
use System\Models\PamAccount;
use System\Models\PamRole;
use System\Models\Resources\PamResource;
use System\Action\Pam as ActPam;


class PamController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                   {post} api_v1/backend/system/pam/lists [O]账号-列表
	 * @apiVersion            1.0.0
	 * @apiName               PamList
	 * @apiGroup              Pam
	 * @apiPermission         backend
	 * @apiParam   {String}   [field]             字段 [id|ID;username|用户名;mobile|手机号;email|邮箱]
	 * @apiParam   {String}   [kw]                关键词
	 * @apiParam   {String}   type                角色类型 [user|用户;backend|后台;develop|开发者]
	 * @apiParam   {Integer}  [page]              分页
	 * @apiParam   {Integer}  [size]              页数
	 * @apiParam   {String}   [append]            附加模式[role|角色]
	 * @apiSuccess {Object[]} list                列表
	 * @apiSuccess {Integer}  list.id             ID
	 * @apiSuccess {String}   list.username       用户名
	 * @apiSuccess {String}   list.mobile         手机号
	 * @apiSuccess {String}   list.email          邮箱
	 * @apiSuccess {String}   list.type           用户类型
	 * @apiSuccess {Boolean}  list.is_enable      是否启用
	 * @apiSuccess {Boolean}  list.disable_reason 禁用原因
	 * @apiSuccess {Boolean}  list.created_at     创建时间
	 * @apiSuccess {Boolean}  list.can_enable     是否可启用
	 * @apiSuccess {Boolean}  list.can_disable    是否可禁用
	 * @apiSuccessExample     list
	 * {
	 *     "list" : [
	 *         {
	 *             "id": 3,
	 *             "username": "fadan001",
	 *             "mobile": "18766482988",
	 *             "email": "",
	 *             "type": "user",
	 *             "is_enable": 1,
	 *             "disable_reason": "",
	 *             "created_at": "2018-01-02 16:08:01",
	 *             "updated_at": "2018-01-31 10:28:13",
	 *             "can_enable": false,
	 *             "can_disable": true
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
		$field = data_get($input, 'field');
		$kw    = data_get($input, 'kw');
		$type  = data_get($input, 'type', PamAccount::TYPE_BACKEND);
		if ($field && $kw) {
			$input[$field] = $kw;
		}
		/** @var PamAccount $user */
		$user      = $this->getJwtBeGuard()->user();
		$pageInfo  = new PageInfo($input);
		$Db        = PamAccount::filter($input, PamFilter::class);
		$strAppend = data_get($input, 'append');
		$arrAppend = StrHelper::separate(',', $strAppend);
		$append    = [];
		if (in_array('role', $arrAppend)) {
			$filter         = [
				'type' => $type,
			];
			$roles          = PamRole::filter($filter, RoleFilter::class)->get();
			$append['role'] = $roles;
		}
		return PamAccount::paginationInfo($Db, $pageInfo, function($item) use ($user) {
			$pam                = (new PamResource($item))->toArray(app('request'));
			$pam['can_enable']  = $user->can('enable', $item);
			$pam['can_disable'] = $user->can('disable', $item);
			return $pam;
		}, $append);
	}

	/**
	 * @api                 {get} api_v1/backend/system/pam/establish [O]账号-C2E
	 * @apiVersion          1.0.0
	 * @apiName             AccountEstablish
	 * @apiGroup            Pam
	 * @apiPermission       backend
	 * @apiParam {String}   passport      标识
	 * @apiParam {String}   password      名称
	 * @apiParam {Integer}  role_id       角色ID
	 */
	public function establish()
	{
		$passport = input('passport', '');
		$password = input('password', '');
		$role_id  = input('role_id', 0);

		$Pam = (new ActPam())->setPam($this->getJwtBeGuard()->user());
		if (!$Pam->register($passport, $password, $role_id)) {
			return Resp::web(Resp::ERROR, $Pam->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}


	/**
	 * @api                 {get} api_v1/backend/system/pam/do [O]账号-Do
	 * @apiVersion          1.0.0
	 * @apiName             PamDo
	 * @apiGroup            Pam
	 * @apiPermission       backend
	 * @apiParam {Integer}  id            角色ID
	 * @apiParam {String}   action        标识 [enable|启用;]
	 */
	public function do()
	{
		$id     = input('id', 0);
		$action = input('action', '');
		$Pam    = (new ActPam())->setPam($this->getJwtBeGuard()->user());
		switch ($action) {
			case 'enable':
				if (!$Pam->enable($id)) {
					return Resp::web(Resp::ERROR, $Pam->getError());
				}
				else {
					return Resp::web(Resp::SUCCESS, '操作成功');
				}
				break;
			default:
				return Resp::web(Resp::ERROR, '无效操作');
				break;
		}
	}

	/**
	 * @api                 {get} api_v1/backend/system/pam/disable [O]账号-禁用
	 * @apiVersion          1.0.0
	 * @apiName             PamDisable
	 * @apiGroup            Pam
	 * @apiPermission       backend
	 * @apiParam {Integer}  id           用户ID
	 * @apiParam {String}   date         解封日期
	 * @apiParam {String}   reason       禁用原因
	 */
	public function disable()
	{
		$id     = input('id', 0);
		$date   = input('date', '');
		$reason = input('reason', '');

		$Pam = (new ActPam())->setPam($this->getJwtBeGuard()->user());
		if (!$Pam->disable($id, $date, $reason)) {
			return Resp::web(Resp::ERROR, $Pam->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}

	/**
	 * @api                 {get} api_v1/backend/system/pam/password [O]账号-改密码
	 * @apiVersion          1.0.0
	 * @apiName             PamDisable
	 * @apiGroup            Pam
	 * @apiPermission       backend
	 * @apiParam {Integer}  id           用户ID
	 * @apiParam {String}   password     密码
	 */
	public function password()
	{
		$id       = input('id', 0);
		$password = input('password', '');
		$Pam      = (new ActPam)->setPam($this->getJwtBeGuard()->user());
		if (!$Pam->setPasswordById($id, $password)) {
			return Resp::web(Resp::ERROR, $Pam->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}
}
