<?php namespace Slt\Policies;


use Slt\Models\ArticleContent;
use System\Models\PamAccount;

class ArticleContentPolicy
{

	public function create($pam)
	{
		return true;
	}

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
