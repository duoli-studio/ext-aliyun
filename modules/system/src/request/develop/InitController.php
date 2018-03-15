<?php namespace System\Request\Develop;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use System\Classes\Traits\SystemTrait;

class InitController extends Controller
{
	use SystemTrait, ViewTrait;

	public function __construct()
	{
		parent::__construct();
		$this->share('_menus', $this->getModule()->developMenus());
	}
}
