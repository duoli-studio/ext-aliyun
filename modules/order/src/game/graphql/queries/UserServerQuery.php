<?php namespace Order\Game\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use Order\Models\GameServer;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;


/**
 * Class SettingQuery.
 */
class UserServerQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'user_server';
		$this->attributes['description'] = trans('order::server.graphql.query_desc');
	}


	public function authorize($root, $args)
	{
		return $this->isJwtUser();
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('user_server');
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		$allServer = GameServer::where('parent_id',0)->get();
		// gen tree
		// $array = [];
		$ke = [];
		foreach ($allServer as $k => $r) {
			$all = GameServer::where('top_parent_id',$r->id)->get();
			foreach ($all as $v) {
				$ke[$r->id] = [
					'id'    => intval($v->id),
					'title' => strval($v->title),
					'pid'   => intval($v->parent_id),
				];
			}
		}

		// \Log::debug($array);
		// \Log::debug($all);
		\Log::debug($ke);
		// return $array;
	}
}
