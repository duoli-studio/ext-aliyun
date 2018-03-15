<?php namespace System\Event;

class LoginFailedEvent
{
	/** @var string */
	private $type;

	/** @var string */
	private $passport;

	/** @var string */
	private $password;

	public function __construct(array $credentials)
	{
		$this->type     = $credentials['type'] ?? '';
		$this->passport = $credentials['passport'] ?? '';
		$this->password = $credentials['password'] ?? '';
	}

	public function type()
	{
		return $this->type;
	}

	public function passport()
	{
		return $this->passport;
	}

	public function password()
	{
		return $this->password;
	}
}
