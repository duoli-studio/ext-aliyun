<?php namespace Order\Request\Web;

use Illuminate\Http\Request;

use Order\Models\GameServer;
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
		if (is_post()){
			$input = $request->input();
			$game = app('act.game');
			if($game->establish($input)){

			}
		}
	}
}
