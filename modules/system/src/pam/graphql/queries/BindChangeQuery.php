<?php namespace System\Pam\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;


class BindChangeQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'bind_change';
		$this->attributes['description'] = trans('system::bind_change.graphql.query_desc');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('bind_change');
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'account_id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::bind_change.db.account_id'),
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
		return PamAccount::find($args['account_id']);
	}
}
