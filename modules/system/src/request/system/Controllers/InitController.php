<?php namespace System\Request\Backend\Controllers;


use App\Models\PlatformOrder;
use System\Classes\AclHelper;
use System\Models\PamAccount;
use Poppy\Framework\Application\Controller;
use Illuminate\Http\Request;

/**
 * 管理员初始化文件
 */
class InitController extends Controller
{

	/**
	 * @type PamAccount 权限验证的用户对象
	 */
	protected $pam;


	public function __construct(Request $request)
	{
		parent::__construct();
		$questionCreateNum = 0;
		$this->pam         = $request->user(PamAccount::GUARD_BE);

		$menus = AclHelper::getMenuCache('backend', $this->pam, true);

		\View::share([
			'_pam'          => $this->pam,
			'_desktop'      => $this->desktop,
			'_roles'        => $this->roles,
			'_account_id'   => $this->pam ? $this->pam->account_id : 0,
			'_account_name' => $this->pam ? $this->pam->account_name : '',
			'_pam_types'    => PamAccount::accountTypeAll(),
			'_menus'        => $menus ?: [],
			'_qst_num'      => $questionCreateNum,
		]);


		if ($this->route) {
			\View::share([
				// '_title' => SysAcl::getTitleCache(PamAccount::ACCOUNT_TYPE_DESKTOP, $this->route),
			]);
		}

	}
}