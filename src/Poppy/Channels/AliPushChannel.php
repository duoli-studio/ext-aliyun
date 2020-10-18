<?php namespace Poppy\Extension\Aliyun\Poppy\Channels;

use Illuminate\Notifications\Notification;
use Poppy\Extension\Aliyun\Poppy\Action\AliPush;
use Poppy\Extension\Aliyun\Poppy\Contracts\AliPushChannel as AliPushChannelContract;
use Throwable;


/**
 * 阿里推送频道
 */
class AliPushChannel
{
	/**
	 * Send the given notification.
	 * @param mixed                               $notifiable
	 * @param Notification|AliPushChannelContract $notification
	 */
	public function send($notifiable, Notification $notification)
	{
		try {
			$notify = $notification->toAliPush();
			if (!$notify) {
				return;
			}
			$Push = AliPush::getInstance();
			if (!$Push->send($notify)) {
				sys_error('addon', self::class, [
					'error'  => $Push->getError(),
					'notify' => $notify,
				]);
			}
			sys_success('addon', self::class, $Push->getMessages());
		} catch (Throwable $e) {
			sys_error('addon', self::class, [
				'notify' => $notify ?? [],
			]);
			sys_error('addon', self::class, $e->getMessage());
		}
	}
}