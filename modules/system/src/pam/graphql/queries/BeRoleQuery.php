<?php namespace System\Pam\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\PamRole;


class BeRoleQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_role';
		$this->attributes['description'] = '[O]' . trans('system::role.graphql.query_desc');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('BeRole');
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::role.db.id'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		return PamRole::find($args['id']);
	}
}