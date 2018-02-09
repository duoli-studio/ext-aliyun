<?php namespace System\Models\Resources;

use Illuminate\Http\Resources\Json\Resource;

class HelpResource extends Resource
{

	/**-
	 * 将资源转换成数组。
	 * @param $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'id'        => $this->id,
			'cat_id'    => $this->cat_id,
			'cat_title' => $this->category ? $this->category->title : '',
			'title'     => $this->title,
			'content'   => $this->content,
		];
	}
}