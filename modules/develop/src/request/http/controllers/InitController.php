<?php namespace App\Http\Controllers\Develop;


use App\Lemon\Repositories\System\SysAcl;
use App\Models\PamAccount;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * 初始化文件
 * Class InitController
 * @package App\Http\Controllers\Lemon
 */
class InitController extends Controller {


	public function __construct(Request $request) {
		parent::__construct();
		
		$key = substr($this->route, 0, strpos($this->route, '.'));
		\View::share([
			'_menu'     => SysAcl::menu(PamAccount::ACCOUNT_TYPE_DEVELOP),
			'_sub_menu' => SysAcl::operation(PamAccount::ACCOUNT_TYPE_DEVELOP . '/' . $key),
		]);

		if ($this->route) {
			\View::share([
				'_title' => SysAcl::getTitleCache(PamAccount::ACCOUNT_TYPE_DEVELOP, $this->route),
			]);
		}
	}

}