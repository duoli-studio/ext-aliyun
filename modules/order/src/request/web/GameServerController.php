<?php namespace Order\Request\Web;

use Illuminate\Http\Request;

use Poppy\Framework\Application\Controller;

class GameServerController extends Controller
{
    //
	public function index()
	{
		echo 'game_server';
	}

	public function create(Request $request)
	{
	}
}
