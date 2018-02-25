<?php namespace System\Request\Develop;


class EnvController extends InitController
{


	public function phpinfo()
	{
		return view('system::develop.env.phpinfo');
	}

	public function check()
	{

		$env = [
			'weixin' => false
		];
		if (class_exists('\Poppy\Extension\Wxpay\Lib\WxPayAppApiPay')){
			$env['weixin'] = true;
		}
		return view('system::develop.env.check', [
			'env' => $env
		]);
	}

}
