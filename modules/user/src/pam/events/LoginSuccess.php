<?php namespace User\Pam\Events;


use System\Models\PamAccount;

class LoginSuccess
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
