<?php namespace App\Http\Controllers\Develop;

use App\Http\Requests;
use Illuminate\Http\Request;

class EnvController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_develop.auth');
	}

	// phpinfo
	public function getInfo() {
		return view('develop.home.info');
	}

	// env
	public function getRun() {
		return view('develop.home.run');
	}
}