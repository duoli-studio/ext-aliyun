<?php namespace Order\Game\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class UserServerType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'user_server';
		$this->attributes['description'] = trans('order::server.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'        => [
				'type'        => Type::int(),
				'description' => trans('order::server.db.id'),
			],
			'title'     => [
				'type'        => Type::string(),
				'description' => trans('order::server.db.title'),

			],
		];
	}
}
