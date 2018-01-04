<?php namespace Order\Game\Action;

/**
 * 游戏服务器操作
 */
use Order\Models\GameServer;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;

class Server
{
	use SystemTrait;

	/**
	 * @var
	 */
	protected $item;

	protected $id;

	/**
	 * @var string
	 */
	protected $gameServerTable;


	public function __construct()
	{
		$this->gameServerTable = (new GameServer())->getTable();
	}

	/**
	 *创建需求
	 * @param array    $data
	 * @param null|int $id
	 * @return bool
	 */
	public function establish($data, $id = null)
	{
		if (!$this->checkPermission()){
			return false;
		}
		$initDb    = [
			'title'      => strval(array_get($data, 'title', '')),
			'parent_id'  => strval(array_get($data, 'parent_id', '')),
			'is_enable'  => strval(array_get($data, 'is_enable', '0')),
			'is_default' => strval(array_get($data, 'is_default', '0')),
		];
		$validator = \Validator::make($initDb, [
			'title'      => [
				Rule::required(),
				Rule::unique($this->gameServerTable, 'title')->where(function($query) use ($id) {
					if ($id) {
						$query->where('id', '!=', $id);
					}
				}),
			],
			'parent_id'  => [
				Rule::required(),
				Rule::integer(),
			],
			'is_enable'  => [
				Rule::required(),
				Rule::integer(),
			],
			'is_default' => [
				Rule::required(),
				Rule::integer(),
			],
		], [], [
			'title'      => trans('order::server.db.title'),
			'parent_id'  => trans('order::server.db.parent_id'),
			'is_enable'  => trans('order::server.db.is_enable'),
			'is_default' => trans('order::server.db.is_default'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->init($id)) {
			return false;
		}

		if ($id) {
			$this->item->update($initDb);
			return true;
		}
		else {
			//创建数据
			$this->item = GameServer::create($initDb);
			//生成区服编码
			$this->item->code = $this->genCode($this->item->id);
			//生成当前id的子元素
			$this->item->children = $this->item->id;
			//生成顶层ID, 父元素
			$allPid = (array) $this->parentId($this->item->id, $ids);
			if (empty($allPid)) {
				$this->item->top_parent_id = 0;
			}
			elseif (count($allPid) == 1) {
				$this->item->top_parent_id = $allPid['0'];
			}
			else {
				$this->item->top_parent_id = $allPid['1'];
				$this->item->save();
				//生成所有子元素

				$SecondDb   = GameServer::find($allPid['0']);
				$parent_ids = GameServer::where('parent_id', $this->item->parent_id)->pluck('id')->toArray();
				array_unshift($parent_ids, $SecondDb->id);
				$SecondDb->children = implode(',', $parent_ids);

				$FirstDb        = GameServer::find($allPid['1']);
				$top_parent_ids = GameServer::where('top_parent_id', $this->item->top_parent_id)->pluck('id')->toArray();
				array_unshift($top_parent_ids, $FirstDb->id);
				$FirstDb->children = implode(',', $top_parent_ids);

				$SecondDb->save();
				$FirstDb->save();

			}
			$this->item->save();
		}
		return true;
	}

	/**
	 * 删除数据
	 * @param int $id
	 * @return bool
	 */
	public function delete($id)
	{
		if ($id && !$this->init($id)) {
			return false;
		}

		return $this->item->delete();
	}

	/**
	 * 生成游戏服务器编码
	 * @param int $id
	 * @return bool|string
	 */
	private function genCode($id)
	{
		$allPid = (array) $this->parentId($id, $ids);
		array_unshift($allPid, $id);
		if (is_null($ids)) {
			$formatId = [$id, 0, 0];
		}
		else {
			$ids = array_reverse($allPid);
			if (count($ids) > 3) {
				return $this->setError('级别错误');
			}
			$formatId = [];
			for ($i = 0; $i <= 2; $i++) {
				$formatId[] = $ids[$i] ?? 0;
			}
		}

		return sprintf("%'.04d%'.04d%'.04d", $formatId[0], $formatId[1], $formatId[2]);
	}

	private function init($id)
	{
		try {
			$this->item = GameServer::findOrFail($id);
			$this->id   = $this->item->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}
	}

	/**
	 * 递归查找所有父ID
	 * @param int   $id
	 * @param array $ids
	 * @return array
	 */
	public function parentId($id, &$ids)
	{
		$parentId = GameServer::where('id', $id)->value('parent_id');
		if ($parentId) {
			$ids[] = $parentId;
			return $this->parentId($parentId, $ids);
		}
		else {
			return $ids;
		}
	}

}