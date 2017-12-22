<?php namespace System\Setting\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class ConfItemType extends AbstractType
{
	protected $attributes = [
		'name'        => 'conf_item',
		'description' => 'Config items',
	];

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'item'        => [
				'type'        => Type::string(),
				'description' => trans('system::conf.graphql.children_item'),
			],
			'value'       => [
				'description' => trans('system::conf.graphql.children_value'),
				'type'        => Type::string(),
			],
			'description' => [
				'description' => trans('system::conf.graphql.children_description'),
				'type'        => Type::string(),
			],
		];
	}
}
