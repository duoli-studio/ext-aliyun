<?php namespace Order\Request\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\GameServer;
use App\Models\GameType;
use Illuminate\Http\Request;

/**
 * 游戏接口
 * Class GameController
 * @package App\Http\Controllers\Desktop
 */
class GameController extends Controller {

	protected $action;

	public function getServerHtml(Request $request) {
		$game_id    = $request->input('game_id');
		$server_key = $request->input('server_key');
		$options    = $request->input('options', []);
		$server_id  = isset($options['server_id']) ? $options['server_id'] : 0;
		if (isset($options['server_id'])) {
			unset($options['server_id']);
		}
		$servers                = GameServer::getAll($game_id, true);
		$options['placeholder'] = '请选择服务器';
		$options['class'] = 'form-control';
		echo \Form::tree($server_key, $servers, $server_id, $options);
		exit;
	}

	public function getTypeHtml(Request $request) {
		$gameId  = $request->input('game_id');
		$typeKey = $request->input('type_key');
		$options = $request->input('options');
		$typeId  = isset($options['type_id']) ? $options['type_id'] : 0;
		if (isset($options['type_id'])) {
			unset($options['type_id']);
		}
		$types                  = GameType::kvTypeTitle($gameId);
		$options['placeholder'] = '请选择代练类型';
		$options['class'] = 'form-control';
		echo \Form::select($typeKey, $types, $typeId, $options);
		exit;
	}
}
