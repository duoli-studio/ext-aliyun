<?php namespace System\Request\Backend\Controllers;

use App\Http\Requests\Desktop\SourceRequest;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Repositories\Sour\LmUtil;
use App\Models\DailianOrder;
use App\Models\GameSource;
use App\Models\PlatformOrder;
use Illuminate\Http\Request;


/**
 * 游戏来源
 * Class GameSourceController
 * @package App\Http\Controllers\Desktop
 */
class GameSourceController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	public function getIndex() {
		$pageInfo = GameSource::orderBy('list_order', 'asc')->paginate($this->pagesize);
		return view('desktop.game_source.index', [
			'pageInfo' => $pageInfo,
		]);
	}

	public function getCreate() {
		return view('desktop.game_source.item');
	}

	public function postCreate(SourceRequest $request) {
		$input   = $request->except('_token');
		$node_id = GameSource::create($input);
		if ($node_id) {
			return AppWeb::resp(AppWeb::SUCCESS, '创建成功', 'location|' . route('dsk_game_source.index'));
		} else {
			return AppWeb::resp(AppWeb::ERROR, '创建失败');
		}
	}


	public function postDestroy($id) {
		$count = PlatformOrder::where('source_id', $id)->count();
		if ($count) {
			return AppWeb::resp(AppWeb::ERROR, '有子条目, 无法删除', 'forget|1');
		} else {
			GameSource::destroy($id);
			return AppWeb::resp(AppWeb::SUCCESS, '删除成功', 'location|' . route('dsk_game_source.index'));
		}
	}


	public function getEdit($id) {
		return view('desktop.game_source.item', [
			'item' => GameSource::findOrFail($id),
		]);
	}

	public function postEdit(Request $request, $id) {
		GameSource::where('source_id', $id)->update($request->except(['_token', '_method']));
		return AppWeb::resp(AppWeb::SUCCESS, '更新成功', 'location|' . route('dsk_game_source.index'));
	}

	/**
	 * 排序
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postSort(Request $request) {
		$sorts = $request->input('sort');
		if (!is_array($sorts)) return AppWeb::resp(AppWeb::ERROR, '参数错误!');
		foreach ($sorts as $id => $sort) {
			GameSource::where('source_id', $id)->update(['list_order' => intval($sort)]);
		}
		return AppWeb::resp(AppWeb::SUCCESS, '更新完成!', 'location|' . route('dsk_game_source.index'));
	}
}