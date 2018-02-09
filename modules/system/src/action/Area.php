<?php namespace System\Action;


use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\SysArea;

class Area
{
	use SystemTrait;


	/**
	 * @var SysArea
	 */
	protected $area;

	/**
	 * @var int SysArea id
	 */
	protected $areaId;

	/**
	 * @var string
	 */
	protected $areaTable;

	public function __construct()
	{
		$this->areaTable = (new SysArea())->getTable();
	}

	/**
	 * 创建需求
	 * @param array    $data
	 * @param null|int $id
	 * @return bool
	 */
	public function establish($data, $id = null)
	{
		if (!$this->checkPam()) {
			return false;
		}

		$initDb    = [
			'title'     => trim(strval(array_get($data, 'title', ''))),
			'parent_id' => intval(array_get($data, 'parent_id', 0)),
		];
		$validator = \Validator::make($initDb, [
			'title'     => [
				Rule::required(),
				Rule::string(),
			],
			'parent_id' => [
				Rule::required(),
				Rule::integer(),
			],
		], [], [
			'title'     => trans('system::area.db.title'),
			'parent_id' => trans('system::area.db.parent_id'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		if ($id && !$this->initArea($id)) {
			return false;
		}

		$needUpdate = [];
		$this->matchKv(true);
		if ($id) {
			if ($id == $initDb['parent_id']) {
				return $this->setError(trans('system::area.message.same_error'));
			}
			$needUpdate = array_merge(
				$needUpdate,
				$this->parentIds($this->areaId, 'array'),
				[$this->areaId]
			);
			$this->area->update($initDb);
		}
		else {
			$area       = SysArea::create($initDb);
			$this->area = $area;
		}

		$this->matchKv(true);

		$needUpdate = array_merge(
			$needUpdate,
			$this->parentIds($this->area->id, 'array'),
			[$this->area->id]
		);

		$this->batchFix(array_unique($needUpdate));

		return true;
	}

	/**
	 * 删除数据
	 * @param int $id
	 * @return bool|null
	 * @throws \Exception
	 */
	public function delete($id)
	{
		if ($id && !$this->initArea($id)) {
			return false;
		}
		if (SysArea::where('parent_id', $id)->exists()) {
			return $this->setError(trans('system::area.message.exist_error'));
		}
		$parentIds = $this->parentIds($id, 'array');
		$this->area->delete();
		$this->batchFix($parentIds);
		return true;
	}


	/**
	 * 获取父元素IDs
	 * @param int    $id
	 * @param string $type
	 * @return string|array
	 */
	public function parentIds($id, $type = 'string')
	{
		$matchKv = $this->matchKv();
		$ids     = [];
		while (isset($matchKv[$id])) {
			$id    = $matchKv[$id];
			$ids[] = $id;
		}
		$ids = array_reverse($ids);
		return ($type == 'string') ? implode(',', $ids) : $ids;
	}

	/**
	 * 获取所有的子id
	 * @param           $id
	 * @return array
	 */
	public function getChildren($id)
	{
		$matchKv = $this->matchKv();
		if (!is_array($id)) {
			$ids = [$id];
		}
		else {
			$ids = $id;
		}

		$children = [];
		foreach ($ids as $id) {
			$children = array_merge($children, array_keys($matchKv, $id));
		}

		if (!$children) {
			return $ids;
		}
		else {
			return array_merge($ids, $this->getChildren($children));
		}
	}

	/**
	 * 初始化id
	 * @param int $id
	 * @return bool
	 */
	private function initArea($id)
	{
		try {
			$this->area   = SysArea::findOrFail($id);
			$this->areaId = $this->area->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError(trans('system::area.message.undefined_error'));
		}
	}

	/**
	 * @param bool $clear
	 * @return mixed
	 */
	private function matchKv($clear = false)
	{
		$cache_name = cache_name(__CLASS__, 'match_kv');
		if ($clear) {
			\Cache::forget($cache_name);
		}
		return \Cache::remember($cache_name, 10, function() {
			return SysArea::pluck('parent_id', 'id')->toArray();
		});
	}

	/**
	 * 修复分类代码
	 * @param $id
	 */
	private function fix($id)
	{
		$children    = $this->getChildren($id);
		$topParentId = $this->topParentId($id);
		SysArea::where('id', $id)->update([
			'children'      => implode(',', $children),
			'top_parent_id' => $topParentId,
		]);
	}

	/**
	 * @param $ids
	 */
	private function batchFix($ids)
	{
		foreach ($ids as $id) {
			if (!$id) {
				continue;
			}
			$this->fix($id);
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function topParentId($id)
	{
		$parentIds = $this->parentIds($id, 'array');
		if (count($parentIds) == 1) {
			return $id;
		}
		else {
			return $parentIds[1];
		}
	}
}
