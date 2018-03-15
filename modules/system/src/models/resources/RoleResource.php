<?php namespace System\Models\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
{
	/**-
	 * 将资源转换成数组。
	 * @param $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'id'             => $this->id,
			'name'           => $this->name,
			'title'          => $this->title,
			'type'           => $this->type,
			'description'    => $this->description,
		];
	}
}