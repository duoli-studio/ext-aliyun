<?php namespace System\Area\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Query\Builder;
use Order\Models\GameServer;
use Order\Models\Resources\Server;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\SystemTrait;
use System\Models\Resources\Area;
use System\Models\SysArea;
use System\Models\SysConfig;


class BeAreaListQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_area_list';
		$this->attributes['description'] = trans('system::area.graphql.queries_desc');
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
		return $this->getGraphQL()->type('BeAreaList');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args()
	{
		return [
			'id'         => [
				'type'        => Type::int(),
				'description' => trans('system::area.db.id'),

			],
			'pagination' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('InputPagination')),
				'description' => trans('system::area.graphql.pagination'),
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
		$id       = $args['id'] ?? 0;
		$pageInfo = new PageInfo($args['pagination']);


		if (!empty($id)) {
			$Db = SysArea::where('top_parent_id', $id)->first();
			/** @var SysArea $Db */
			return SysArea::paginationInfo($Db, $pageInfo, Area::class);

		}
	}
}
