<?php namespace System\Request\Backend\Controllers;

use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Repositories\Application\SettingUi;
use App\Lemon\Repositories\System\SysAcl;
use App\Models\BaseConfig;
use Illuminate\Http\Request;


/**
 * 网站设置
 * Class LemonSiteController
 * @package App\Http\Controllers\Desktop
 */
class LemonSiteController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	public function getSetting() {
		$Ui = new SettingUi('site');
		$Ui->setTitle('网站设置');
		$site = site();
		return $Ui->render($site);
	}

	public function postSetting(Request $request) {
		BaseConfig::configUpdate($request->except(['_token']), 'site');
		BaseConfig::reCache();
		return AppWeb::resp(AppWeb::SUCCESS, '更新配置成功', 'location|' . route('dsk_lemon_site.setting'));
	}

	/**
	 * 更新缓存
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postCache() {
		BaseConfig::reCache();
		SysAcl::reCache();
		return AppWeb::resp(AppWeb::SUCCESS, '更新缓存成功');
	}
}