<?php namespace Slt\Policies;


use Slt\Models\ArticleContent;
use System\Models\PamAccount;

class ArticleContentPolicy
{

	/**
	 * 是否自己
	 * @param PamAccount     $pam
	 * @param ArticleContent $prd
	 * @return bool
	 */
	public function edit($pam, $prd)
	{
		if ($prd->account_id != $pam->id) {
			return false;
		}
		return true;
	}
}
