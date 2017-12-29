<?php namespace System\Models\Resources;


use Illuminate\Http\Resources\Json\Resource;
use System\Models\PamAccount;


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
			'id'         => $this->id,
			'username'   => $this->username,
			'email'      => $this->email,
			'created_at' => $this->created_at->toDatetimeString(),
			'updated_at' => $this->updated_at->toDatetimeString(),
		];
	}
}