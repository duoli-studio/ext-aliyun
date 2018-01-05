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
			'head_pic'   => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'nickname'   => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'sex'        => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			'signature'  => [
				'type'        => Type::string(),
				'description' => trans('user::fans.db.fans_id'),
			],
			//todo 认证等级 男女猎手 女猎手

		];
	}
}
