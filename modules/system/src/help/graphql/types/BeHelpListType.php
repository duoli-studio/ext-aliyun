<?php namespace System\Help\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;
use System\Classes\Traits\SystemTrait;


class BeHelpListType extends AbstractType
{
	use SystemTrait;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeHelpList';
		$this->attributes['description'] = trans('system::help.graphql.type_desc');
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
				'description' => trans('system::help.graphql.pagination'),
			],
			'list'       => [
				'type'        => Type::listOf($this->getGraphQL()->type('BeHelp')),
				'description' => trans('system::help.graphql.list'),
			],
		];
	}
}
