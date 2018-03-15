<?php namespace System\Request\Backend;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Repositories\Sour\LmUtil;
use App\Models\PluginAllowip;
use Illuminate\Http\Request;

/**
 * 管理员初始化文件
 * Class PluginIpController
 * @package App\Http\Controllers\Desktop
 */
class IpController extends InitController
{
	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	public function getIndex() {
		$ips = PluginAllowip::orderBy('updated_at', 'desc')->paginate($this->pagesize);

		return view('desktop.plugin_ip.index', [
			'ips' => $ips,
		]);
	}

	public function getCreate() {
		return view('desktop.plugin_ip.item');
	}

	public function postCreate(Request $request) {
		$input = $request->except('_token');
		$ipId  = PluginAllowip::create($input);
		if ($ipId) {
			return AppWeb::resp(AppWeb::SUCCESS, '存储成功', 'location|' . route('dsk_plugin_ip.index'));
		}

			return AppWeb::resp(AppWeb::ERROR, '存储失败');
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  int $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postDestroy($id) {
		PluginAllowip::destroy($id);

		return AppWeb::resp(AppWeb::SUCCESS, '删除成功', 'location|' . route('dsk_plugin_ip.index'));
	}

	public function postIp(Request $request) {
		$ipId  = $this->parse($request->input('id'));
		$value = $request->input('value');
		$item  = PluginAllowip::findOrFail($ipId);
		if (!LmUtil::isIp($value)) {
			return AppWeb::resp(AppWeb::ERROR, 'IP 格式不正确!', 'oldvalue|' . $item['ip_addr']);
		}
		PluginAllowip::where('ip_id', $ipId)->update([
			'ip_addr' => $value,
		]);

		return AppWeb::resp(AppWeb::SUCCESS, '修改IP成功!', 'value|' . $value);
	}

	public function postNote(Request $request) {
		$ipId  = $this->parse($request->input('id'));
		$value = $request->input('value');
		$item  = PluginAllowip::findOrFail($ipId);
		if (
		PluginAllowip::where('ip_id', $ipId)->update([
			'note' => $value,
		])
		) {
			return AppWeb::resp(AppWeb::SUCCESS, '修改IP成功!', 'value|' . $value);
		}

			return AppWeb::resp(AppWeb::ERROR, '修改IP成功!', 'value|' . $item['note']);
	}

	private function parse($str, $full = false) {
		if (preg_match('/J_edit(.*)_(\d+)/', $str, $match)) {
			if ($full) {
				return $match;
			}

			return $match[2];
		}

		return '';
	}
}