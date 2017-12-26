<?php namespace System\Pam\Action;

/**
 * 基本账户操作
 */
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemAppTrait;
use System\Models\PamAccount;
use System\Models\PamRole;

class Role
{

	use SystemAppTrait;

	/** @var PamRole */
	protected $role;

	/** @var int Role id */
	protected $roleId;

	/**
	 * @var string
	 */
	protected $roleTable;

	public function __construct()
	{
		$this->roleTable = (new PamRole())->getTable();
	}

	/**
	 * 创建需求
	 * @param array    $data
	 * @param null|int $id
	 * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function establish($data, $id = null)
	{
		if (!$this->checkBe('backend:global.role.manage')) {
			return false;
		}
		$initDb    = [
			'title'       => strval(array_get($data, 'title', '')),
			'name'        => strval(array_get($data, 'name', '')),
			'type'        => strval(array_get($data, 'type', '')),
			'description' => strval(array_get($data, 'description', '')),
		];
		$validator = \Validator::make($initDb, [
			'name'  => [
				Rule::required(),
				Rule::string(),
				Rule::alpha(),
				Rule::between(3, 15),
				Rule::unique($this->roleTable, 'name')->where(function ($query) use ($id) {
					if ($id) {
						$query->where('id', '!=', $id);
					}
				}),
			],
			'title' => [
				Rule::required(),
				Rule::unique($this->roleTable, 'title')->where(function ($query) use ($id) {
					if ($id) {
						$query->where('id', '!=', $id);
					}
				}),
			],
			'type'  => [
				Rule::required(),
				Rule::in([
					PamAccount::GUARD_BACKEND,
					PamAccount::GUARD_WEB,
					PamAccount::GUARD_DEVELOP,
				]),
			],
		], [], [
			'name'  => trans('system.db.role.name'),
			'title' => trans('system.db.role.title'),
			'type'  => trans('system.db.role.type'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->initRole($id)) {
			return false;
		}

		if ($this->roleId) {
			$this->role->update($initDb);
		}
		else {
			$this->role = PamRole::create($initDb);
		}
		return true;
	}


	public function initRole($id)
	{
		try {
			$this->role   = PamRole::findOrFail($id);
			$this->roleId = $this->role->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}
	}
}