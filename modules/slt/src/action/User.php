<?php namespace Slt\Action;

use Slt\Classes\Traits\SltTrait;
use System\Action\Pam;

class User
{

	use SltTrait;

	public function webLogout()
	{
		return (new Pam())->webLogout();
	}
}