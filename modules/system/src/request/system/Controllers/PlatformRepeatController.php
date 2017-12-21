<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Repositories\System\SysSearch;
use App\Models\PlatformRepeat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
 * 平台订单
 * Class PlatformOrderController
 * @package App\Http\Controllers\Desktop
 */
class PlatformRepeatController extends InitController
{

	use AuthorizesRequests;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * Display a listing of the resource.
	 * @return \Response
	 */
	public function getIndex()
	{
		$is_over  = \Input::get('is_over');
		$is_pay   = \Input::get('is_pay');
		$orderKey = SysSearch::key('id', [
			'order_id', 'id', 'is_over', 'is_pay',
		]);
		$Db       = PlatformRepeat::orderBy($orderKey, SysSearch::order());

		if (trim($is_over) != '') {
			$Db->where('is_over', $is_over);
		}
		if (trim($is_pay) != '') {
			$Db->where('is_pay', $is_pay);
		}

		$items = $Db->paginate($this->pagesize);
		return view('desktop.platform_repeat.index', [
			'items' => $items,
		]);
	}

}

