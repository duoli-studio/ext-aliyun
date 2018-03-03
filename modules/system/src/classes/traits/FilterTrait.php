<?php namespace System\Classes\Traits;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Http\Pagination\PageInfo;


trait FilterTrait
{
	use Filterable;


	/**
	 * @param Builder  $query
	 * @param PageInfo $pageInfo
	 * @return mixed
	 */
	public function scopePageFilter($query, PageInfo $pageInfo)
	{
		$offset = ($pageInfo->page() - 1) * $pageInfo->size();
		return $query->offset($offset)->take($pageInfo->size());
	}

	/**
	 * @param Builder|FilterTrait|\Eloquent $Db
	 * @param PageInfo                      $pageInfo
	 * @param string                        $resource
	 * @param array                         $append
	 * @return array
	 */
	public static function paginationInfo($Db, PageInfo $pageInfo, $resource, $append = [])
	{
		/* 缓存查询结果数量, 暂不开启
		 --------------------------------------------
		if ($cache) {
			$binding = $this->getBindings();
			array_unshift($binding, str_replace('?', '%s', $this->toSql()));
			$sql      = call_user_func_array('sprintf', $binding);
			$cacheKey = md5($sql);
		}
		*/

		$total = (clone $Db)->count();
		$page  = $pageInfo->page();
		$size  = $pageInfo->size();
		$pages = intval(ceil($total / $size));

		/** @var Collection $list */
		$list = $Db->pageFilter($pageInfo)->get();
		$data = collect();
		if ($list->count()) {
			if (is_string($resource) && class_exists($resource)) {
				$list->each(function($item) use ($resource, $data) {
					$res = (new $resource($item))->toArray(app('request'));
					$data->push($res);
				});
			}
			if (is_callable($resource)) {
				$list->each(function($payload) use ($resource, $data) {
					$res = $resource(...array_values(func_get_args()));
					$data->push($res);
				});
			}
		}
		$return = [
			'status'  => Resp::SUCCESS,
			'message' => '获取列表成功',
			'data'    => [
				'list'       => $data->toArray(),
				'pagination' => [
					'total' => $total,
					'page'  => $page,
					'size'  => $size,
					'pages' => $pages,
				],
			],
		];

		// 附加数据
		if ($append) {
			$return['data'] += $append;
		}
		return $return;
	}

}