<?php namespace Slt\Request\Web;


use Poppy\Framework\Application\Controller;
use Slt\Classes\Traits\SltTrait;
use System\Models\PamAccount;


class InitController extends Controller
{
	use SltTrait;

	/**
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null|PamAccount
	 */
	protected function getWebPam()
	{
		return $this->getWebGuard()->user();
	}
}