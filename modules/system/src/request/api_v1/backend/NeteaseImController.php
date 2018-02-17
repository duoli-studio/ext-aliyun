<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Action\NeteaseIm;
use System\Classes\Traits\SystemTrait;

class NeteaseImController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                  {post} api_v1/backend/system/im/send_notice 发送系统通知
	 * @apiVersion           1.0.0
	 * @apiName              HelpEstablish
	 * @apiGroup             System
	 * @apiParam  {String}   push_type      推送类型
	 *            [order|订单;system|系统通知;activity|官方公告]
	 * @apiParam  {Integer}  account        Account Id
	 * @apiParam  {string}   [msgtype]      Send type
	 *            [0|单人;1|群; 默认 0]
	 * @apiParam  {String}   msg            推送的消息
	 */
	public function systemNotice()
	{
		$pushType   = input('push_type');
		$account_id = input('account', 3);
		$msgtype    = input('msgtype') ?? 0;
		$msg        = input('msg');

		$im = new NeteaseIm();
		if ($im->systemNotice($pushType, $account_id, $msgtype, $msg)) {
			return Resp::web(Resp::SUCCESS, '发送成功!');
		}
		else {
			return Resp::web(Resp::ERROR, $im->getError());
		}
	}
}
