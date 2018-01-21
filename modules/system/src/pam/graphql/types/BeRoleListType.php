<?php namespace System\Pam\Graphql\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;
use System\Classes\Traits\SystemTrait;


class BeRoleListType extends AbstractType
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeRoleList';
		$this->attributes['description'] = trans('system::role.graphql.type_desc');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function fields()
	{
		return [
			'list'       => [
				'type'        => Type::listOf($this->getGraphQL()->type('BeRole')),
				'description' => trans('system::role.graphql.list'),
			],
			'pagination' => [
				'type'        => $this->getGraphQL()->type('Pagination'),
				'description' => trans('system::role.graphql.pagination'),
			],
		];
	}
}
