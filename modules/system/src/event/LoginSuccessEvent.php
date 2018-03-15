<?php namespace System\Event;

use System\Models\PamAccount;

class LoginSuccessEvent
{
	/** @var PamAccount */
	private $pam;

	public function __construct(PamAccount $pam)
	{
		$this->pam = $pam;
	}

	public function pam()
	{
		return $this->pam;
	}
}