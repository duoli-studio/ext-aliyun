<?php namespace System\Help\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Query\Builder;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\SystemTrait;
use System\Models\Resources\Category;
use System\Models\SysCategory;


class BeCategoryListQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_category_list';
		$this->attributes['description'] = trans('system::category.graphql.queries_desc');
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
		return $this->getGraphQL()->type('BeCategoryList');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args()
	{
		return [
			'pagination' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('InputPagination')),
				'description' => trans('system::category.graphql.pagination'),
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
		$pageInfo = new PageInfo($args['pagination']);

		/** @var Builder $Db */
		$Db = SysCategory::first();
		return SysCategory::paginationInfo($Db, $pageInfo, Category::class);
	}
}
