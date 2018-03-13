<?php namespace Slt\Policies;


use User\Models\PamAccount;
use Slt\Models\ArticleBook;

class PrdBookPolicy
{

	/**
	 * 是否自己
	 * @param PamAccount  $pam
	 * @param ArticleBook $prd
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
