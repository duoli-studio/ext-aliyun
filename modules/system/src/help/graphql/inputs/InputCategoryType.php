<?php namespace System\Help\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;

/**
 * Class SettingType.
 */
class InputCategoryType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'InputCategory';
		$this->attributes['description'] = trans('system::category.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'type' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::category.db.type'),
			],
			'parent_id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::category.db.parent_id'),

			],
			'title'   => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::category.db.title'),
			],
		];
	}
}
