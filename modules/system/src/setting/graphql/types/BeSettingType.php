<?php namespace System\Setting\Graphql\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

class BeSettingType extends AbstractType
{
	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeSetting';
		$this->attributes['description'] = trans('system::setting.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'key'         => [
				'type'        => Type::string(),
				'description' => trans('system::setting.graphql.key'),
			],
			'value'       => [
				'description' => trans('system::setting.graphql.value'),
				'type'        => Type::string(),
			],
			'description' => [
				'description' => trans('system::setting.graphql.description'),
				'type'        => Type::string(),
			],
		];
	}
}
