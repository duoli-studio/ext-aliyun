<?php namespace System\Pam\Graphql\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;
use System\Classes\Traits\SystemTrait;


class BePamListType extends AbstractType
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BePamList';
		$this->attributes['description'] = trans('system::account.graphql.list_type');
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
				'description' => trans('system::account.graphql.pagination'),
			],
			'list'       => [
				'type'        => Type::listOf($this->getGraphQL()->type('BePam')),
				'description' => trans('system::account.graphql.list_desc'),
			],
		];
	}
}
