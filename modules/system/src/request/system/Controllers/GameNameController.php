<?php namespace System\Request\Backend\Controllers;

use App\Http\Requests\Desktop\GameNameRequest;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Models\GameName;
use App\Models\PlatformOrder;
use Illuminate\Http\Request;

/**
 * 游戏名称
 * Class GameNameController
 * @package App\Http\Controllers\Desktop
 */
class GameNameController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * Display a listing of the resource.
	 * @return \Response
	 */
	public function getIndex() {
		$games = GameName::orderBy('created_at', 'desc')->paginate($this->pagesize);
		return view('desktop.game_name.index', [
			'games' => $games,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 * @return \Response
	 */
	public function getCreate() {
		return view('desktop.game_name.item');
	}

	/**
	 * Store a newly created resource in storage.
	 * @param GameNameRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postCreate(GameNameRequest $request) {
		$input   = $request->except('_token');
		$game_id = GameName::create($input);
		if ($game_id) {
			return AppWeb::resp(AppWeb::SUCCESS, '创建成功', 'location|' . route('dsk_game_name.index'));
		} else {
			return AppWeb::resp(AppWeb::ERROR, '创建失败');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  int $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postDestroy($id) {
		$count = PlatformOrder::whereRaw('game_id= ? ', [$id])->count();
		if ($count) {
			return AppWeb::resp(AppWeb::ERROR, '存在订单, 无法删除', 'forget|1');
		} else {
			GameName::destroy($id);
			return AppWeb::resp(AppWeb::SUCCESS, '删除成功', 'time|1;location|' . route('dsk_game_name.index'));
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param  int $id
	 * @return \Response
	 */
	public function getEdit($id) {
		return view('desktop.game_name.item', [
			'item' => GameName::findOrFail($id),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param  int    $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postEdit(Request $request, $id) {
		GameName::where('game_id', $id)->update($request->except(['_token', '_method']));
		return AppWeb::resp(AppWeb::SUCCESS, '编辑成功', 'location|' . route('dsk_game_name.index'));
	}


}
