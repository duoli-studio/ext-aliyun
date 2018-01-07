<?php namespace Slt\Action;


use Poppy\Framework\Traits\BaseTrait;
use Slt\Models\PrdContent;
use Slt\Models\PrdVersion;

class ActPrdVersion
{

	use BaseTrait;


	/**
	 * @param $blog PrdContent 文档内容
	 * @return bool
	 */
	public function create($blog)
	{
		$blogVersionId = $this->nextVersionId($blog->id);
		PrdVersion::create([
			'content_id'     => $blog->id,
			'account_id'     => $blog->account_id,
			'version_id'     => $blogVersionId,
			'content'        => $blog->prd_content,
			'content_origin' => $blog->prd_content_origin,
		]);
		return true;
	}

	private function nextVersionId($blog_id)
	{
		return PrdVersion::where('blog_id', $blog_id)->max('blog_version_id') + 1;
	}

}