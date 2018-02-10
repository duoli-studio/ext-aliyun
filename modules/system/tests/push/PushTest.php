<?php namespace System\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use System\Models\PamAccount;
use System\Models\SysHelp;
use User\Classes\extension\AliPush;
use System\Notifications\HelpNotification;

class PushTest extends TestCase
{
	public function testPush()
	{
		/** @var SysHelp $help */
		$help = SysHelp::findOrFail('1');
		$type = 'help';
		/*if ($help->cat_id == $this->getSetting()->get('activity_category')) {
			$type = 'help';
		}*/
		\Notification::send(new PamAccount(), new HelpNotification($type, $help));
		// return AppWeb::resp(AppWeb::SUCCESS, '推送成功!');
	}

	/**
	 * @throws \Exception
	 */
	public function testSendPush()
	{

		$notify = [
			'title'            => '测试',
			'content'          => '内容',
			'extras'           => $extras = [
				'type'  => 'help',
				'title' => '帮助标题',
				// 'url'    => route_url('app.help', '', ['id' => $this->help->id]),
				// 'id'    => 1,
				// 'cat_id' => $this->help->cat_id,
			],
			'broadcast_type'   => 'ALL',
			'registration_ids' => '',
		];


		$result = AliPush::sendPush($notify);
		$this->assertTrue($result, true);

	}

}
