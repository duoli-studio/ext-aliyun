<?php namespace User\Events\Listeners;

use Illuminate\Auth\Events\Login;
use User\Models\PamAccount;

class LoginNum
{

	/**
	 * Handle the event.
	 * @param Login $event
	 */
	public function handle($event)
	{
		/** @var PamAccount $user */
		$user = $event->user;
		$user->increment('login_times', 1);
	}

}


