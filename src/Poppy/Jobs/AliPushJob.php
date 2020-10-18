<?php namespace Poppy\Extension\Aliyun\Poppy\Jobs;

use Illuminate\Notifications\Notification;
use Poppy\Extension\Aliyun\Poppy\Action\AliPush;
use Poppy\Extension\Aliyun\Poppy\Contracts\AliPushChannel as AliPushChannelContract;
use Poppy\Framework\Application\Job;
use Throwable;


/**
 * 阿里推送Job
 */
class AliPushJob extends Job
{
	/**
	 * @var array 需要推送的信息
	 */
	private $notify;


	public function __construct($notify)
	{
		$this->notify = $notify;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		try {
			if (!$this->notify) {
				sys_success('addon', self::class, 'No push id to send');
				return;
			}
			$Push = AliPush::getInstance();
			if (!$Push->send($this->notify)) {
				sys_error('addon', self::class, $Push->getError());
			}
			sys_success('addon', self::class, $Push->getMessages());
		} catch (Throwable $e) {
			sys_error('addon', self::class, $e->getMessage());
		}
	}

	/**
	 * Send the given notification.
	 * @param mixed                               $notifiable
	 * @param Notification|AliPushChannelContract $notification
	 */
	public function send($notifiable, Notification $notification)
	{

	}
}