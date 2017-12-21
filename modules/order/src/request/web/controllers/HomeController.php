<?php namespace Order\Request\Web\Controllers;

use Curl\Curl;
use Illuminate\Http\Request;

class HomeController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_front.auth', [
			'except' => [
				'getHomepage',
				'getTest',
			],
		]);
	}

	public function getHomepage()
	{
		return redirect(route('dsk_lemon_home.cp'));
	}


	public function getTest()
	{

		;
		$curl             = new Curl();
		$param            = [
			'player' => '狐狸的盛宴',
			'server' => 25,
			'title'  => 'fsfs',

		];
		$param['bt_list'] = 1;
		$param['type']    = 'zj';
		for ($a = 1; $a < 1000; $a++) {
			echo $curl->get(env('URL_TGP') . '/tgp/player', $param);

		}
		print_r($a);
	}

}
