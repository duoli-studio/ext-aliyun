<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;
use System\Mail\TestSend;

class MailController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                 {get} api_v1/backend/system/mail/fetch [O]邮件-配置
	 * @apiVersion          1.0.0
	 * @apiName             MailFetch
	 * @apiGroup            System
	 * @apiPermission       backend
	 */
	public function fetch()
	{
		$Setting = $this->getSetting();

		return $this->getResponse()->json([
			'data'    => [
				'driver'     => $Setting->get('system::mail.driver'),
				'encryption' => $Setting->get('system::mail.encryption'),
				'port'       => $Setting->get('system::mail.port'),
				'host'       => $Setting->get('system::mail.host'),
				'from'       => $Setting->get('system::mail.from'),
				'username'   => $Setting->get('system::mail.username'),
				'password'   => $Setting->get('system::mail.password'),
			],
			'message' => '清空缓存！',
			'status'  => Resp::SUCCESS,
		], 200, [], JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @api                 {get} api_v1/backend/system/mail/store [O]邮件-存储
	 * @apiVersion          1.0.0
	 * @apiName             MailStore
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam {Integer}  id            角色ID
	 * @apiParam {String}   action        标识
	 */
	public function store()
	{
		$Setting = $this->getSetting();
		$all     = \Input::all();
		foreach ($all as $key => $value) {
			$Setting->set('system::mail.' . $key, $value);
		}

		return Resp::web(Resp::SUCCESS, '更新邮件配置成功');
	}

	/**
	 * @api                 {get} api_v1/backend/system/mail/test [O]邮件-测试
	 * @apiVersion          1.0.0
	 * @apiName             MailTest
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiParam {Integer}  id            角色ID
	 * @apiParam {String}   action        标识
	 */
	public function test()
	{
		$mail    = \Input::get('to');
		$content = \Input::get('content');
		\Mail::to($mail)->send(new TestSend($content));
	}
}
