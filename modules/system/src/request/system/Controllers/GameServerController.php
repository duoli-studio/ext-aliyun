<?php namespace System\Request\Backend\Controllers;

use App\Http\Requests\Desktop\ServerRequest;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Repositories\Sour\LmTree;
use App\Models\GameName;
use App\Models\GameServer;
use Illuminate\Http\Request;


/**
 * 服务器
 * Class GameServerController
 * @package App\Http\Controllers\Desktop
 */
class GameServerController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}


	public function getIndex() {
		$nodes = GameServer::with('game')->orderBy('list_order')->get()->toArray();
		$array = [];
		// 构建生成树中所需的数据
		foreach ($nodes as $k => $r) {
			$r['id']        = $r['server_id'];
			$r['title']     = $r['server_title'];
			$r['sort']      = $r['list_order'];
			$r['pid']       = $r['parent_id'];
			$r['game_name'] = $r['game']['game_name'];
			$r['yq_game_name']  = $r['game']['yq_game_name'];
			$r['enable']    = $r['is_enable'] == 'Y'
				?
				'<a class="J_request" data-tip="当前启用, 点击禁用" title="禁用" href="' . route('dsk_game_server.status', [$r['server_id'], 'is_enable', 'N']) . '">
					<i class="fa fa-unlock fa-lg green"></i>
				</a>'
				: '<a class="J_request" data-tip="当前禁用, 点击启用" href="' . route('dsk_game_server.status', [$r['server_id'], 'is_enable', 'Y']) . '">
					<i class="fa fa-lock fa-lg red"></i>
				</a>';

			$r['edit']       = "<a href=\"" . route('dsk_game_server.edit', ['server_id' => $r['server_id']]) . "\"><i class='fa fa-edit fa-lg'></i></a>";
			$r['del']        = "<a class=\"J_request\" href='" . route('dsk_game_server.destroy', [$r['server_id']]) . "' data-confirm=\"确定删除该服务器吗?\"  href=\"javascript:void(0)\"><i class='fa fa-close fa-lg red'></i></a>";
			$array[$r['id']] = $r;
		}
		// gen html
		$str = "<tr class='tr'>
				    <td class='txt-center'><input type='text' value='\$sort' class='w36' name='sort[\$id]'></td>
				    <td class='txt-center'>\$id</td>
				    <td class='enable_\$is_enable'>\$spacer \$title </td>
				    <td class='txt-center'>\$game_name </td>
				    <td class='txt-center'>\$plat_tencent </td>
				    <td class='txt-center'>\$plat_dailianmao </td>
				    <td class='txt-center'>\$plat_dailiantong </td>
				    <td class='txt-center'>\$plat_yidailian </td>   
				    <td class='txt-center'>\$plat_yqdailian </td>   
				    <td class='txt-center'>\$plat_dianjingbaozi </td> 
					<td class='txt-center'>
						  \$edit \$enable  \$del
					</td>
				  </tr>";

		$Tree = new LmTree();
		$Tree->init($array);
		$html_tree = $Tree->getTree(0, $str);

		return view('desktop.game_server.index', [
			'htmlTree' => $html_tree,
		]);
	}

	public function getCreate() {
		return view('desktop.game_server.item', [
			'games'        => GameName::kvLinear(),
			'categoryTree' => $this->getCategoryTree(),
		]);
	}

	public function postCreate(ServerRequest $request) {
		$input   = $request->except('_token');
		$node_id = GameServer::create($input);
		if ($node_id) {
			return AppWeb::resp(AppWeb::SUCCESS, '创建成功', 'location|' . route('dsk_game_server.index'));
		} else {
			return AppWeb::resp(AppWeb::ERROR, '创建失败');
		}
	}


	public function postDestroy($id) {
		$count = GameServer::whereRaw('parent_id= ? ', [$id])->count();
		if ($count) {
			return AppWeb::resp(AppWeb::ERROR, '当下有子服务器, 不可删除上级服务器');
		} else {
			GameServer::destroy($id);
			return AppWeb::resp(AppWeb::SUCCESS, '删除成功', 'location|' . route('dsk_game_server.index'));
		}
	}


	public function getEdit($id) {
		return view('desktop.game_server.item', [
			'item'         => GameServer::findOrFail($id),
			'games'        => GameName::kvLinear(),
			'categoryTree' => $this->getCategoryTree(),
		]);
	}

	public function postEdit(Request $request, $id) {
		GameServer::where('server_id', $id)->update($request->except(['_token', '_method']));
		return AppWeb::resp(AppWeb::SUCCESS, '区服编辑成功', 'location|' . route('dsk_game_server.index'));
	}


	private function getCategoryTree() {
		$allServer = GameServer::orderBy('list_order')->enable()->get()->toArray();
		// gen tree
		$array = [];
		foreach ($allServer as $k => $r) {
			$array[$r['server_id']] = [
				'id'    => intval($r['server_id']),
				'title' => strval($r['server_title']),
				'pid'   => intval($r['parent_id']),
			];
		}
		return $array;
	}

	/**
	 * 服务器排序
	 * @param Request $request
	 * @return mixed
	 */
	public function postSort(Request $request) {
		$sorts = $request->input('sort');
		if (!is_array($sorts)) return AppWeb::resp(AppWeb::ERROR, '参数错误!');
		foreach ($sorts as $id => $sort) {
			GameServer::where('server_id', $id)->update(['list_order' => intval($sort)]);
		}
		return AppWeb::resp(AppWeb::SUCCESS, '更新完成!', 'location|' . route('dsk_game_server.index'));
	}

	/**
	 * 改变账户状态
	 * @param $id
	 * @param $field
	 * @param $status
	 * @return mixed
	 */
	public function postStatus($id, $field, $status) {
		GameServer::where('server_id', $id)->update([
			$field => $status,
		]);
		return AppWeb::resp(AppWeb::SUCCESS, '状态已经改变', 'location|' . route('dsk_game_server.index'));
	}
}