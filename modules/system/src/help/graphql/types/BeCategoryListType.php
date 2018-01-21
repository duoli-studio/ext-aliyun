<?php namespace System\Help\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;
use System\Classes\Traits\SystemTrait;


class BeCategoryListType extends AbstractType
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeCategoryList';
		$this->attributes['description'] = trans('system::category.graphql.type_desc');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function fields()
	{
		return [
			'pagination' => [
				'type'        => $this->getGraphQL()->type('Pagination'),
				'description' => trans('system::category.graphql.pagination'),
			],
			'list'       => [
				'type'        => Type::listOf($this->getGraphQL()->type('BeCategory')),
				'description' => trans('system::category.graphql.list'),
			],
		];
	}
}
