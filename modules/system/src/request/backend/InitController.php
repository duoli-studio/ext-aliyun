<?php namespace System\Request\Backend;

use Poppy\Framework\Application\Controller;
use System\Classes\Traits\SystemTrait;


class InitController extends Controller
{
	use SystemTrait;

	public function __construct()
	{
		parent::__construct();
		$this->getContainer()->setExecutionContext('backend');
	}
}