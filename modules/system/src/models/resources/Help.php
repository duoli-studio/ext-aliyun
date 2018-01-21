<?php namespace System\Models\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Help extends Resource
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
			'type'      => $this->type,
			'title'     => $this->title,
			'content'   => $this->content,
		];
	}
}