<?php namespace System\Help\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\SysCategory;


class BeCategoryQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_category';
		$this->attributes['description'] = trans('system::category.graphql.query_desc');
	}


	public function authorize($root, $args)
	{
		return $this->isJwtBackend();
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('BeCategory');
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'id' => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::category.db.id'),
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
		$Db = SysCategory::find($args['id']);
		return $Db;
	}
}
