<?php namespace Slt\Policies;


use Slt\Models\ArticleBook;
use System\Models\PamAccount;

class ArticleBookPolicy
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
