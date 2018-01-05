<?php namespace User\Models\Resources;


use Illuminate\Http\Resources\Json\Resource;


class ConcernResource extends Resource
{

	/**
	 * 将资源转换成数组。
	 * @param $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'account_id'             => $this->id,
			'head_pic'               => $this->head_pic,
			'nickname'               => $this->nickname,
			'sex'                    => $this->sex ? '女' : '男',
			'signature'              => $this->signature,
			'lol_is_girl_validated'  => $this->lol_is_girl_validated ? '女猎手' : '',
			'lol_validated_type'     => $this->lol_validated_type,
			'wz_is_girl_validated'   => $this->wz_is_girl_validated ? '女猎手' : '',
			'wz_validated_type'      => $this->wz_validated_type,
			'pubg_is_girl_validated' => $this->pubg_is_girl_validated ? '女猎手' : '',
			'pubg_validated_type'    => $this->pubg_validated_type,
		];
	}
}