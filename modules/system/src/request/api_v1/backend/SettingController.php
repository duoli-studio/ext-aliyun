<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;


class SettingController extends ApiController
{
	use SystemTrait;


	/**
	 * @api                 {get} api_v1/backend/system/setting/fetch [O]设置-获取
	 * @apiVersion          1.0.0
	 * @apiName             SettingFetch
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam   {String} namespace  命名空间
	 * @apiParam   {String} group      命名分组
	 * @apiSuccessExample   data
	 * [
	 *     {
	 *         "value": "罂粟网络",
	 *         "type": "string",
	 *         "key": "system::site.name",
	 *         "description": "网站名称"
	 *     },
	 *     ....
	 *     {
	 *         "value": true,
	 *         "type": "int",
	 *         "key": "system::site.is_open",
	 *         "description": "是否开启"
	 *     },
	 * ]
	 */
	public function fetch()
	{
		$namespace = \Input::get('namespace');
		$group     = \Input::get('group');
		$validator = \Validator::make($this->getRequest()->all(), [
			'namespace' => Rule::required(),
			'group'     => Rule::required(),
		], [], [
			'namespace' => trans('system::setting.db.config.namespace'),
			'group'     => trans('system::setting.db.config.group'),
		]);
		if ($validator->fails()) {
			return Resp::web(Resp::ERROR, $validator->messages());
		}

		$setting = $this->getSetting()->getNsGroup($namespace, $group);
		return Resp::web(Resp::SUCCESS, '操作成功',
			$setting
		);
	}

	/**
	 * @api                 {get} api_v1/backend/system/setting/establish [O]设置-存储
	 * @apiVersion          1.0.0
	 * @apiName             SettingEstablish
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam   {String} key        设置 KEY
	 * @apiParam   {String} value      设置 值
	 */
	public function establish()
	{
		$key     = $args['key'] ?? '';
		$value   = $args['value'] ?? '';
		$Setting = $this->getSetting();
		if (!$Setting->set($key, $value)) {
			return Resp::web(Resp::ERROR, $Setting->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}
}
