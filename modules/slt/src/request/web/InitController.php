<?php namespace Slt\Request\Web\Controllers;


use Poppy\Framework\Application\Controller;
use User\Models\PamAccount;


class InitController extends Controller
{


	public function __construct()
	{
		$this->accountType = PamAccount::REG_PLATFORM_WEB;

		parent::__construct();

		if ($this->route) {
			\View::share([
				// '_title' => SysAcl::getTitleCache(PamAccount::ACCOUNT_TYPE_DESKTOP, $this->route),
			]);
		}

		// $this->assignMenus();

	}
}