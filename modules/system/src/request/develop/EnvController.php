<?php namespace System\Request\Develop;


class EnvController extends InitController
{

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
