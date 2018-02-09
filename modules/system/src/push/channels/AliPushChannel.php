<?php namespace System\Push\Channels;

use Illuminate\Notifications\Notification;
use System\Push\Action\AliPush;
use System\Push\Notifications\HelpNotification;

/**
 * 阿里推送频道
 * @package App\Channels
 */
class AliPushChannel
{

	/**
	 * Send the given notification.
	 * @param  mixed                          $notifiable
	 * @param  Notification| HelpNotification $notification
	 */
	public function send($notifiable, Notification $notification)
	{
		try {
			$notify = $notification->toAliPush();
			if (!$notify) {
				return;
			}

			AliPush::sendPush($notify);
		} catch (\Exception $e) {
			\Log::error('[Queue-Notification]: Send Push Fail, msg : ' . $e->getMessage());
		}
	}

}