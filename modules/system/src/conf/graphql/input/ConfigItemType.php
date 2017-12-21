<?php namespace System\Conf\GraphQL\Input;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;

/**
 * Class SettingType.
 */
class ConfigItemType extends InputType
{
	protected $attributes = [
		'name'        => 'config_item',
		'description' => 'Config items',
	];

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'key'   => [
				'type'        => Type::string(),
				'description' => trans('system::conf.graphql.item_key'),
			],
			'value' => [
				'type'        => Type::string(),
				'description' => trans('system::conf.graphql.item_value'),
			],
		];
	}
}
