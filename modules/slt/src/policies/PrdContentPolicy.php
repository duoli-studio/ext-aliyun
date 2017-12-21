<?php namespace Slt\Policies;


use User\Models\PamAccount;
use Slt\Models\PrdContent;

class PrdContentPolicy
{

	/**
	 * 是否自己
	 * @param PamAccount $pam
	 * @param PrdContent $prd
	 * @return bool
	 */
	public function self($pam, $prd)
	{
		if ($prd->account_id != $pam->id) {
			return false;
		}
		return true;
	}
}
