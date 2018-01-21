<?php namespace System\Help\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Query\Builder;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\SystemTrait;
use System\Models\Resources\Help;
use System\Models\SysHelp;


class BeHelpListQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_help_list';
		$this->attributes['description'] = trans('system::help.graphql.queries_desc');
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
		return $this->getGraphQL()->type('BeHelpList');
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
				'description' => trans('system::help.graphql.pagination'),
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
		$Db = SysHelp::with('category')->first();
		return SysHelp::paginationInfo($Db, $pageInfo, Help::class);
	}
}
