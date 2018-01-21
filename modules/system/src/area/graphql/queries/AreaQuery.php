<?php namespace System\Area\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\Resources\Area;
use System\Models\SysArea;
use System\Models\SysConfig;


/**
 * Class SettingQuery.
 */
class AreaQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'area';
		$this->attributes['description'] = trans('system::area.graphql.query_desc');
	}


	public function authorize($root, $args)
	{
		return $this->isJwtUser();
	}

	/**
	 * @return ListOfType
	 * @throws TypeNotFound
	 */
	public function type(): ListOfType
	{
		return Type::listOf($this->getGraphQL()->type('Area'));
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [
			'type'      => [
				'type'        => Type::string(),
				'description' => trans('system::area.db.type'),

			],
			'id'        => [
				'type'        => Type::int(),
				'description' => trans('system::area.db.id'),

			],
			'parent_id' => [
				'type'        => Type::int(),
				'description' => trans('system::area.db.parent_id'),

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
		// todo 页面最好采用级别,比如第一级别显示, 游戏进入之后显示大区,再进入显示服务器.然后再加上分页
		$type      = $args['type'] ?? '';
		$id        = $args['id'] ?? 0;
		$parent_id = $args['parent_id'] ?? 0;

		if ($type === 'top') {
			return SysArea::where('parent_id', SysConfig::NO)->get();
		}
		$tree = [];
		if ($id && $parent_id == '') {
			$Db = SysArea::where('parent_id', SysConfig::NO)->find($id);
			if ($Db) {
				$ids = $Db->children;
				$ids = explode(',', $ids);
				foreach ($ids as $value) {
					if ($id != $value) {
						$parent = SysArea::find($value);
						$tree[] = (new Area($parent))->toArray($this->getRequest());
					}
				}
			}
			return $tree;
		}

		if ($id && $parent_id) {
			$Db = SysArea::where('parent_id', SysConfig::NO)->find($id);
			if ($Db) {
				$ids = SysArea::where('parent_id', $parent_id)->pluck('id')->toArray();
				foreach ($ids as $value) {
					$parent = SysArea::find($value);
					$tree[] = (new Area($parent))->toArray($this->getRequest());
				}
			}
			return $tree;
		}
	}
}
