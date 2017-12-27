<?php namespace Util\Request\Web;

use Illuminate\Http\Request;

use Poppy\Framework\Application\Controller;

class UtilController extends Controller
{
    //
	public function index(){
		echo 'hhhhhhhhhhhhhhhhhhhhhhhhhhhhhh ';
	}
	public function captcha(Request $request){
		$passport = $request->input('passport');
		$account_id = $request->input('account_id');
	}

}
