<?php namespace Addons\Push\Ali\Sample;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Poppy\Extension\Aliyun\Poppy\Channels\AliPushChannel;
use Poppy\Extension\Aliyun\Poppy\Contracts\AliPushChannel as AliPushChannelContract;


class SampleNotification extends Notification implements ShouldQueue, AliPushChannelContract
{
	use SerializesModels, Queueable;

	/**
	 * @var
	 */
	protected $type;

	/**
	 * Create a new notification instance.
	 * ArticleAndActivity constructor.
	 * @param            $type
	 */
	public function __construct($type)
	{
		$this->type = $type;
	}

	/**
	 * Get the notification's delivery channels.
	 * @param mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return [AliPushChannel::class];
	}

	public function toAliPush()
	{
		return [
			'broadcast_type' => 'android',
			'device_type'    => 'android|message;ios|notice;',
			'title'          => 'Test Title',
			'content'        => 'Test Content',
			'extras'         => [
				'key1' => '',
				'key2' => '',
			],
		];
	}
}
