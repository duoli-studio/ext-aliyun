<?php namespace System\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestSend extends Mailable
{
	use Queueable, SerializesModels;

	public $content;

	/**
	 * Create a new message instance.
	 *
	 * @param $content
	 */
	public function __construct($content)
	{
		$this->content = $content;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->markdown('system::email.test_send');
	}
}
