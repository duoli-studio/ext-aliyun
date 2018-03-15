<?php namespace System\Models\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PamResource extends Resource
{
	/**
	 * 将资源转换成数组。
	 * @param $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'id'             => $this->id,
			'username'       => $this->username,
			'mobile'         => $this->mobile,
			'email'          => $this->email,
			'type'           => $this->type,
			'is_enable'      => $this->is_enable,
			'disable_reason' => $this->disable_reason,
			'created_at'     => $this->created_at->toDatetimeString(),
			'updated_at'     => $this->updated_at->toDatetimeString(),
		];
	}
}