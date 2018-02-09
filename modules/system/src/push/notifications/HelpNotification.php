<?php namespace System\Push\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use System\Models\SysHelp;
use System\Push\Channels\AliPushChannel;
use System\Push\Contracts\AliPush;

class HelpNotification extends Notification implements ShouldQueue, AliPush
{
	use SerializesModels, Queueable;

	/**
	 * @var
	 */
	protected $type;

	/**
	 * @var SysHelp
	 */
	protected $help;

	/**
	 * Create a new notification instance.
	 * ArticleAndActivity constructor.
	 * @param            $type
	 * @param SysHelp $help
	 */
	public function __construct($type, $help)
	{
		$this->type = $type;
		$this->help = $help;
	}

	/**
	 * Get the notification's delivery channels.
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return [AliPushChannel::class];
	}

	public function toAliPush()
	{
		$extras = [
			'type'   => $this->type,
			'title'  => $this->help->title,
			'url'    => route_url('app.help', '', ['id' => $this->help->id]),
			'id'     => $this->help->id,
			'cat_id' => $this->help->cat_id,
		];

		return [
			'broadcast_type' => 'ALL',
			'title'          => $this->help->title,
			'content'        => mb_substr(strip_tags($this->help->content), 0, 30, 'utf-8'),
			'extras'         => $extras,
		];
	}
}
