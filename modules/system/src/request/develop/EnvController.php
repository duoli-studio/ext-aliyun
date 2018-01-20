<?php namespace System\Request\Develop;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use System\Classes\Traits\SystemTrait;


class EnvController extends Controller
{
	use SystemTrait, ViewTrait;

	public function __construct()
	{
		$this->middleware(['web', 'auth:develop']);
		parent::__construct();
	}

	public function phpinfo()
	{
		return view('system::develop.env.phpinfo');
	}

}
