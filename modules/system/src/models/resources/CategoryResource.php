<?php namespace System\Models\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CategoryResource extends Resource
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
			'parent_id' => $this->parent_id,
			'type'      => $this->type,
			'title'     => $this->title,
		];
	}
}