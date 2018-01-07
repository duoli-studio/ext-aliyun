<?php namespace System\Request\Backend\Controllers;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Dailian\Application\Lol\Price;
use App\Models\BaseConfig;
use Illuminate\Http\Request;
/**
 * 管理员初始化文件
 * Class PluginIpController
 * @package App\Http\Controllers\Desktop
 */
class BillingPrice extends InitController {
	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}
	public function getIndex() {
		$items = [
			'dan'  => Price::$prices,
			'area' => Price::$area,
		];
		$game = BaseConfig::getCache('game');
		$data = unserialize($game['price']);
		return view('desktop.billing_price.index', [
			'items' => $items,
			'data'  => $data,
		]);
	}
	public function postIndex(Request $request) {
		$price = serialize($request->input('price'));
		BaseConfig::configUpdate(['price' => $price], 'game');
		BaseConfig::reCache();
		return AppWeb::resp(AppWeb::SUCCESS, '创建成功', 'location|' . route('dsk_billing_price.index'));
	}
}