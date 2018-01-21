<?php namespace System\Area\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;
use System\Classes\Traits\SystemTrait;


class BeAreaListType extends AbstractType
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeAreaList';
		$this->attributes['description'] = trans('system::area.graphql.type_desc');
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
				'description' => trans('system::area.graphql.pagination'),
			],
			'list'       => [
				'type'        => Type::listOf($this->getGraphQL()->type('BeArea')),
				'description' => trans('system::area.graphql.list_desc'),
			],
		];
	}
}
