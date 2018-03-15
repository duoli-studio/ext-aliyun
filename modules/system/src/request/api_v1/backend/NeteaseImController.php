<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use System\Action\NeteaseIm;
use System\Classes\Traits\SystemTrait;

class NeteaseImController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                  {post} api_v1/backend/system/im/send 发送系统通知
	 * @apiVersion           1.0.0
	 * @apiName              SendNotice
	 * @apiGroup             Notice
	 * @apiParam  {String}   type           推送类型
	 *     [order|订单;system|系统通知;notice|官方公告]
	 * @apiParam  {Integer}  passport       通行证
	 * @apiParam  {string}   [msg_type]     消息类型
	 *     [single|单人;multi|群; 默认 single]
	 * @apiParam  {String}   content        推送的消息
	 */
	public function systemNotice()
	{
		$type     = input('type');
		$passport = input('passport');
		$msg_type = input('msg_type') == 'single' ? 0 : 0;
		$content  = input('content');

		$im = new NeteaseIm();
		if ($im->systemNotice($type, $passport, $msg_type, $content)) {
			return Resp::web(Resp::SUCCESS, '发送成功!');
		}
		 
			return Resp::web(Resp::ERROR, $im->getError());
	}

	/**
	 * @api                  {post} api_v1/backend/system/im/send_msg 发送普通消息
	 * @apiVersion           1.0.0
	 * @apiName              SendMsg
	 * @apiGroup             Notice
	 * @apiParam  {String}   [from]       发起者passport [二选一,优先此选项]
	 * @apiParam  {String}   [accid]      发起者accid    [二选一]
	 * @apiParam  {String}   passport     接受者
	 * @apiParam  {Integer}  [ope]        推送to类型
	 *          [0|点对点个人消息;1|群消息;]
	 * @apiParam  {String}   content      消息内容
	 */
	public function sendMsg()
	{
		$input = input();
		$im    = new NeteaseIm();
		if (!$im->sendMsg($input)) {
			return Resp::web(Resp::ERROR, $im->getError());
		}
		 
			return Resp::web(Resp::SUCCESS, '发送成功!');
	}

	/**
	 * @api                  {post} api_v1/backend/system/im/set 设置系统用户信息
	 * @apiVersion           1.0.0
	 * @apiName              SetSystemInfo
	 * @apiGroup             Notice
	 * @apiParam  {String}   system       系统
	 * @apiParam  {String}   system_icon  系统头像
	 * @apiParam  {String}   notice       消息
	 * @apiParam  {String}   notice_icon  消息头像
	 * @apiParam  {String}   order        订单
	 * @apiParam  {String}   order_icon   订单头像
	 */
	public function set()
	{
		$input = input();

		$im = new NeteaseIm();
		$im->setPam($this->getJwtBeGuard()->user());
		if ($im->setSystemInfo($input)) {
			return Resp::web(Resp::SUCCESS, '设置成功!');
		}
		 
			return Resp::web(Resp::ERROR, $im->getError());
	}
}
