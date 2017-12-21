<?php namespace Order\Request\Web\Controllers;

use App\Lemon\Repositories\System\SysAcl;
use Poppy\Framework\Application\Controller;
use Illuminate\Http\Request;
use User\Models\PamAccount;

/**
 * 初始化文件
 * Class InitController
 * @package App\Http\Controllers\Site
 */
class InitController extends Controller
{


	/**
	 * 权限验证的用户对象
	 * @var PamAccount
	 */
	protected $pam;

	/**
	 * 用户
	 * @var AccountFront
	 */
	protected $front;

	/**
	 * 是否子账号
	 * @var bool
	 */
	protected $isSub = false;

	/**
	 * 主账号
	 * @var AccountFront
	 */
	protected $owner = null;

	/**
	 * 父账号ID
	 * @var int
	 */
	protected $ownerId = 0;

	/**
	 * 当前登陆账号的ID
	 * @type int
	 */
	protected $accountId = 0;


	/** @type string 当前登录的用户名 */
	protected $accountName = '';


	public function __construct(Request $request)
	{
		parent::__construct();


		$auth = \Auth::guard(PamAccount::GUARD_WEB);
		if ($auth->check() && $auth->user()->account_type == PamAccount::ACCOUNT_TYPE_FRONT) {
			$this->pam         = $auth->user();
			$this->accountId   = $this->pam->id;
			$this->accountName = $this->pam->account_name;
			// 账号所有者
		}


		\View::share([
			'_pam'          => $this->pam,                // 当前账号的基础资料
			'_front'        => $this->front,              // 当前用户的前端资料
			'_owner'        => $this->owner,              // 所有者的资料, 不包含用户名
			'_owner_id'     => $this->ownerId,            // 所有者的ID
			'_is_sub'       => $this->isSub,              // 是否子账号
			'_account_id'   => $this->accountId,          // 当前账户ID
			'_account_name' => $this->accountName,        // 当前账户名
			'_avatar'       => $this->accountId ? avatar($this->front->head_pic) : '',  // 当前用户头像
		]);
	}

	public function deny($message = '')
	{
		if (!$message) {
			$message = '您无权访问本页面';
		}
		return view('front.inc.deny', [
			'message' => $message,
		]);
	}
}