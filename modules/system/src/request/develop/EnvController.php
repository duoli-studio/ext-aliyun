<?php namespace System\Request\Develop;


class EnvController extends InitController
{


	public function phpinfo()
	{
		return view('system::develop.env.phpinfo');
	}

}
