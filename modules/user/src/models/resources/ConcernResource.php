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
		switch ($this->lol_validated_type)
		{
			case 1:
				$this->lol_validated_type = '普通猎手';
				break;
			case 2:
				$this->lol_validated_type = '优选猎手';
				break;
			case 3:
				$this->lol_validated_type = '金牌猎手';
				break;
			default:
				$this->lol_validated_type = '';

		}
		switch ($this->wz_validated_type)
		{
			case 1:
				$this->wz_validated_type = '普通猎手';
				break;
			case 2:
				$this->wz_validated_type = '优选猎手';
				break;
			case 3:
				$this->wz_validated_type = '金牌猎手';
				break;
			default:
				$this->wz_validated_type = '';

		}
		switch ($this->pubg_validated_type)
		{
			case 1:
				$this->pubg_validated_type = '普通猎手';
				break;
			case 2:
				$this->pubg_validated_type = '优选猎手';
				break;
			case 3:
				$this->pubg_validated_type = '金牌猎手';
				break;
			default:
				$this->pubg_validated_type = '';

		}

		return [
			'account_id'   => $this->id,
			'user_img'     => $this->head_pic,
			'nick_name'    => $this->nickname,
			'gender'       => $this->sex ? '女' : '男',
			'autograph'    => $this->signature,
			'lol_is_girl'  => $this->lol_is_girl_validated ? '女猎手' : '',
			'lol_type'     => $this->lol_validated_type,
			'wz_is_girl'   => $this->wz_is_girl_validated ? '女猎手' : '',
			'wz_type'      => $this->wz_validated_type,
			'pubg_is_girl' => $this->pubg_is_girl_validated ? '女猎手' : '',
			'pubg_type'    => $this->pubg_validated_type,
		];
	}
}