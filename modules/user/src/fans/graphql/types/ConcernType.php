<?php namespace User\Fans\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class ConcernType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'concern';
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
				'description' => trans('user::fans.db.account_id'),
			],
			'user_img'   => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'nick_name'   => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'gender'        => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'autograph'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'lol_is_girl'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'lol_type'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'wz_is_girl'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'wz_type'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'pubg_is_girl'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'pubg_type'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
		];
	}
}
