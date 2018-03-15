<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;

class CoreController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                 {get} api_v1/backend/system/core/cache_clear [O]清空缓存
	 * @apiVersion          1.0.0
	 * @apiName             CoreCacheClear
	 * @apiGroup            System
	 */
	public function cacheClear()
	{
		\Artisan::call('cache:clear');

		return $this->getResponse()->json([
			'message' => '清空缓存！',
			'status'  => Resp::SUCCESS,
		], 200, [], JSON_UNESCAPED_UNICODE);
	}
}
