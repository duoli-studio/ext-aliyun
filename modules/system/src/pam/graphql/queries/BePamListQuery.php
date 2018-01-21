<?php namespace System\Pam\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\PamFilter;
use System\Models\PamAccount;
use System\Models\Resources\PamResource;


class BePamListQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_pam_list';
		$this->attributes['description'] = '[O]' . trans('system::account.graphql.queries_desc');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('BePamList');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args()
	{
		return [
			'filters'    => [
				'type'        => $this->getGraphQL()->type('BePamFilter'),
				'description' => trans('system::account.graphql.filter_desc'),
			],
			'pagination' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('InputPagination')),
				'description' => trans('system::account.graphql.pagination'),
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
		$filters  = $args['filters'] ?? [];
		$pageInfo = new PageInfo($args['pagination']);
		$Db       = PamAccount::filter($filters, PamFilter::class);
		return PamAccount::paginationInfo($Db, $pageInfo, PamResource::class);
	}
}
