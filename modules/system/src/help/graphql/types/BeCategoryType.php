<?php namespace System\Help\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;


class BeCategoryType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeCategory';
		$this->attributes['description'] = trans('system::category.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'         => [
				'type'        => Type::int(),
				'description' => trans('system::category.db.id'),
			],
			'type'      => [
				'type'        => Type::string(),
				'description' => trans('system::category.db.type'),

			],
			'parent_id'       => [
				'type'        => Type::int(),
				'description' => trans('system::category.db.parent_id'),

			],
			'title'      => [
				'type'        => Type::string(),
				'description' => trans('system::category.db.title'),

			],
		];
	}
}
