<?php namespace User\Fans\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class ListType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'list';
		$this->attributes['description'] = trans('user::fans.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'account_id' => [
				'type'        => Type::int(),
				'description' => trans('user::profile.db.account_id'),
			],
			'user_img'   => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.head_pic'),
			],
			'nickname'   => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.nickname'),
			],
			'sex'        => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.sex'),
			],
			'signature'  => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.signature'),
			],
			'lol_is_girl'  => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.lol_is_girl_validated'),
			],
			'lol_type'  => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.lol_validated_type'),
			],
			'wz_is_girl'  => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.wz_is_girl_validated'),
			],
			'wz_type'  => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.wz_validated_type'),
			],
			'pubg_is_girl'  => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.pubg_is_girl_validated'),
			],
			'pubg_type'  => [
				'type'        => Type::string(),
				'description' => trans('user::profile.db.pubg_validated_type'),
			],
		];
	}
}
