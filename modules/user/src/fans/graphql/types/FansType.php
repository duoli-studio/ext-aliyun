<?php namespace User\Fans\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class FansType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'fans';
		$this->attributes['description'] = trans('user::fans.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'account_id'        => [
				'type'        => Type::int(),
				'description' => trans('user::fans.db.account_id'),
			],
			'fans_id' => [
				'type'        => Type::int(),
				'description' => trans('user::fans.db.fans_id'),
			],

		];
	}
}
