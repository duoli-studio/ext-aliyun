<?php namespace System\Request\Backend\Controllers;

use App\Http\Requests\Desktop\TypeRequest;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Models\GameName;
use App\Models\GameType;
use Illuminate\Http\Request;


/**
 * 服务器
 * Class TypeController
 * @package App\Http\Controllers\Desktop
 */
class GameTypeController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	public function getIndex() {
		$types = GameType::with('game')->orderBy('list_order')->get()->toArray();
		return view('desktop.game_type.index', [
			'items' => $types,
		]);
	}

	public function getCreate() {
		return view('desktop.game_type.item', [
			'games' => GameName::kvLinear(),
		]);
	}

	public function postCreate(TypeRequest $request) {
		$input   = $request->except('_token');
		$node_id = GameType::create($input);
		if ($node_id) {
			return AppWeb::resp(AppWeb::SUCCESS, '创建成功', 'location|' . route('dsk_game_type.index'));
		} else {
			return AppWeb::resp(AppWeb::ERROR, '创建失败');
		}
	}


	public function postDestroy($id) {
		GameType::destroy($id);
		return AppWeb::resp(AppWeb::SUCCESS, '删除成功', 'location|' . route('dsk_game_type.index'));
	}


	public function getEdit($id) {
		return view('desktop.game_type.item', [
			'item'  => GameType::findOrFail($id),
			'games' => GameName::kvLinear(),
		]);
	}

	public function postEdit(Request $request, $id) {
		GameType::where('type_id', $id)->update($request->except(['_token', '_method']));

		return AppWeb::resp(AppWeb::SUCCESS, '编辑成功', 'location|' . route('dsk_game_type.index'));

	}


	// 改变账户状态
	public function postSort(Request $request) {
		$sorts = $request->input('sort');
		if (!is_array($sorts)) return AppWeb::resp(AppWeb::ERROR, '参数错误!');
		foreach ($sorts as $id => $sort) {
			GameType::where('type_id', $id)->update(['list_order' => intval($sort)]);
		}
		return AppWeb::resp(AppWeb::SUCCESS, '更新完成!', 'location|' . route('dsk_game_type.index'));
	}

	// 改变服务器状态
	public function postStatus(Request $request) {
		$field  = $request->input('field');
		$status = $request->input('status');
		$id     = $request->input('id');
		GameType::where('type_id', $id)->update([
			$field => $status,
		]);
		return AppWeb::resp(AppWeb::SUCCESS, '状态改变成功', 'location|' . route('dsk_game_type.index'));
	}
}