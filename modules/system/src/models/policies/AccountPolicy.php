<?php namespace System\Models\Policies;

use System\Models\PamAccount;
use System\Models\SysConfig;

class AccountPolicy
{
	private $manage = 'backend:global.pam.manage';

	public function before(PamAccount $pam, $ability)
	{
		if (!$pam->hasRole('root')) {
			return $pam->capable($this->manage);
		}

		return null;
	}

	/**
	 * 编辑
	 * @param PamAccount $pam
	 * @return bool
	 */
	public function create(PamAccount $pam)
	{
		return true;
	}

	/**
	 * 编辑
	 * @param PamAccount $pam
	 * @param PamAccount $item
	 * @return bool
	 */
	public function edit(PamAccount $pam, PamAccount $item)
	{
		return true;
	}

	/**
	 * 保存权限
	 * @param PamAccount $pam
	 * @param PamAccount $item
	 * @return bool
	 */
	public function enable(PamAccount $pam, PamAccount $item)
	{
		if ($item->is_enable == SysConfig::NO) {
			return true;
		}

		return false;
	}

	/**
	 * 删除
	 * @param PamAccount $pam
	 * @param PamAccount $item
	 * @return bool
	 */
	public function disable(PamAccount $pam, PamAccount $item)
	{
		// 不得禁用自身
		if ($pam->id == $item->id) {
			return false;
		}

		return !$this->enable($pam, $item);
	}
}