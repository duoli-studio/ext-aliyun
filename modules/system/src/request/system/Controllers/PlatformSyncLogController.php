<?php namespace System\Request\Backend\Controllers;


use App\Lemon\Dailian\Application\App\AppWeb;
use App\Models\AccountDesktop;
use App\Models\AccountDevelop;
use App\Models\AccountFront;
use App\Models\PamAccount;
use App\Models\PamRoleAccount;
use App\Models\PlatformSyncLog;
use Illuminate\Http\Request;

/**
 * 账户管理
 * Class PlatformSyncLogController
 * @package App\Http\Controllers\Desktop
 */
class PlatformSyncLogController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * Display a listing of the resource.
	 * @param Request $request
	 * @return \Response
	 */
	public function getIndex(Request $request) {
		$Log             = PlatformSyncLog::orderBy('created_at', 'desc');
		$accept_platform = $request->input('accept_platform');
		if ($accept_platform) {
			$Log->where('accept_platform', $accept_platform);
		}
		$sync_status = $request->input('sync_status');
		if ($sync_status) {
			$Log->where('sync_status', $sync_status);
		}
		$order_id = $request->input('order_id');
		if ($order_id) {
			$Log->where('order_id', $order_id);
		}
		$items = $Log->paginate($this->pagesize);
		$items->appends(\Input::all());
		return view('desktop.platform_sync_log.index', [
			'items' => $items,
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postDestroy(Request $request) {
		$id      = $request->input('id');
		$account = PamAccount::find($id);

		// todo 检测用户订单(不可删)
		// todo 检测用户金钱记录(不可删)

		\DB::transaction(function() use ($account) {
			// 删除 pam

			$id           = (int) $account['account_id'];
			$account_type = $account['account_type'];
			PamAccount::destroy($id);

			// 删除 pam 附属资料
			if ($account_type == PamAccount::ACCOUNT_TYPE_DESKTOP) {
				AccountDesktop::destroy($id);
			}
			if ($account_type == PamAccount::ACCOUNT_TYPE_FRONT) {
				AccountFront::destroy($id);
			}
			if ($account_type == PamAccount::ACCOUNT_TYPE_DEVELOP) {
				AccountDevelop::destroy($id);
			}

			// 删除 role_account 关联
			PamRoleAccount::where('account_id', $id)->delete();
		});

		return AppWeb::resp(AppWeb::SUCCESS, '删除用户成功', 'location|' . route('dsk_account.index', ['type' => $account['account_type']]));
	}


}


