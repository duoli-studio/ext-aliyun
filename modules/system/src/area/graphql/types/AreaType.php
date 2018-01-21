<?php namespace System\Area\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class AreaType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'Area';
		$this->attributes['description'] = trans('system::area.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'        => [
				'type'        => Type::int(),
				'description' => trans('system::area.db.id'),
			],
			'title'     => [
				'type'        => Type::string(),
				'description' => trans('system::area.db.title'),

			],
			'parent_id'     => [
				'type'        => Type::string(),
				'description' => trans('system::area.db.parent_id'),

			],
		];
	}
}
