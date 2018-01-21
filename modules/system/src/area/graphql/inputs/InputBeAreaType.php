<?php namespace System\Area\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;


class InputBeAreaType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'InputBeArea';
		$this->attributes['description'] = trans('system::area.graphql.input_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'title'      => [
				'type'        => Type::string(),
				'description' => trans('system::area.db.title'),

			],
			'parent_id'  => [
				'type'        => Type::int(),
				'description' => trans('system::area.db.parent_id'),
			],
		];
	}
}
