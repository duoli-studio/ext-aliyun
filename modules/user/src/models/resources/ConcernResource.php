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
		switch ($this->lol_validated_type) {
			case 'normal':
				$this->lol_validated_type = '普通猎手';
				break;
			case 'good':
				$this->lol_validated_type = '优选猎手';
				break;
			case 'gold':
				$this->lol_validated_type = '金牌猎手';
				break;
			default:
				$this->lol_validated_type = '';

		}
		switch ($this->wz_validated_type) {
			case 'normal':
				$this->wz_validated_type = '普通猎手';
				break;
			case 'good':
				$this->wz_validated_type = '优选猎手';
				break;
			case 'gold':
				$this->wz_validated_type = '金牌猎手';
				break;
			default:
				$this->wz_validated_type = '';

		}
		switch ($this->pubg_validated_type) {
			case 'normal':
				$this->pubg_validated_type = '普通猎手';
				break;
			case 'good':
				$this->pubg_validated_type = '优选猎手';
				break;
			case 'gold':
				$this->pubg_validated_type = '金牌猎手';
				break;
			default:
				$this->pubg_validated_type = '';

		}

		return [
			'account_id'   => $this->account_id,
			'user_img'     => $this->head_pic,
			'nickname'     => $this->nickname,
			'sex'          => $this->sex == 'man' ? '男' : '女',
			'signature'    => $this->signature,
			'lol_is_girl'  => $this->lol_is_girl_validated ? '女猎手' : '',
			'lol_type'     => $this->lol_validated_type,
			'wz_is_girl'   => $this->wz_is_girl_validated ? '女猎手' : '',
			'wz_type'      => $this->wz_validated_type,
			'pubg_is_girl' => $this->pubg_is_girl_validated ? '女猎手' : '',
			'pubg_type'    => $this->pubg_validated_type,
		];
	}
}