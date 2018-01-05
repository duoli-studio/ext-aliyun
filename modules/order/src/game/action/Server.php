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
	 * @var GameServer
	 */
	protected $server;

	/**
	 * @var int GameServer id
	 */
	protected $serverId;

	/**
	 * @var string
	 */
	protected $gameServerTable;

	public function __construct()
	{
		$this->gameServerTable = (new GameServer())->getTable();
	}

	/**
	 * 创建需求
	 * @param array    $data
	 * @param null|int $id
	 * @return bool
	 */
	public function establish($data, $id = null)
	{
		if (!$this->checkPermission()) {
			return false;
		}
		//判断parent_id
		if (!$this->judgeParent((int)$id, (int)$data['parent_id'])) {
			\Log::debug($this->getError());
			return false;
		}
		$initDb    = [
			'title'      => strval(array_get($data, 'title', '')),
			'parent_id'  => intval(array_get($data, 'parent_id', 0)),
			'is_enable'  => intval(array_get($data, 'is_enable', 0)),
			'is_default' => intval(array_get($data, 'is_default', 0)),
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
		if ($id && !$this->initServer($id)) {
			return false;
		}

		if ($id) {
			$this->server->update($initDb);
			//更新区服编码 与 顶级id
			$Db            = GameServer::find($id);
			$code          = $this->genCode($id);
			$top_parent_id = $this->genTopParentId($id);
			if ($code && $top_parent_id) {
				$Db->code          = $code;
				$Db->top_parent_id = $top_parent_id;
			}
			else {
				return false;
			}
			$Db->save();
			return true;
		}
		else {
			//创建数据
			$this->server = GameServer::create($initDb);
			//生成区服编码
			$this->server->code = $this->genCode($this->server->id);
			//生成顶级ID
			$this->server->top_parent_id = $this->genTopParentId($this->server->id);
			//生成当前id的子元素
			$this->server->children = $this->server->id;
			$this->server->save();
		}
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
		if ($id && !$this->initServer($id)) {
			return false;
		}

		return $this->server->delete();
	}

	/**
	 * 初始化id
	 * @param int $id
	 * @return bool
	 */
	private function initServer($id)
	{
		try {
			$this->server   = GameServer::findOrFail($id);
			$this->serverId = $this->server->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}
	}

	/**
	 * 获取传入的 parent_id 的级别
	 * @param int   $parentId
	 * @param array $ids
	 * @return array
	 */
	private function parent($parentId, &$ids)
	{
		if ($parentId) {
			$ids[] = $parentId;
			return $this->parentId($parentId, $ids);
		}
		else {
			return $ids;
		}
	}

	/**
	 * 判断传入的 parent_id 级别
	 * @param int $id
	 * @param int $parentId
	 * @return bool
	 */
	private function judgeParent($id, $parentId)
	{
		$ids = $this->parent($parentId, $ids);
		if ($ids){
			if (!empty($id)) {

				array_unshift($ids, $id);
				if (count($ids) > 3) {
					return $this->setError('级别错误');
				}
			}
			else{
				if (count($ids) > 2) {
					return $this->setError('级别错误');
				}
			}
		}
		return true;
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

	/**
	 * 递归查找所有父ID
	 * @param int   $id
	 * @param array $ids
	 * @return array
	 */
	private function parentId($id, &$ids)
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

	/**
	 * 生成顶级id
	 * @param int $id
	 * @return bool
	 */
	private function genTopParentId($id)
	{
		$allPid = (array) $this->parentId($id, $ids);
		array_unshift($allPid, $id);
		$ids = array_reverse($allPid);
		$num = count($ids);
		if ($num > 3) {
			return $this->setError('级别错误');
		}
		switch ($num) {
			case 1:
			case 2:
			case 3:
				return $ids['0'];
				break;
		}
	}

	/*	public function genChildren($id)
		{
			$allPid = (array) $this->parentId($id, $ids);
			switch ($allPid) {
				case !empty($allPid['0']):
					$SecondDb   = GameServer::find($allPid['0']);
					$parent_ids = GameServer::where('parent_id', $this->server->parent_id)->pluck('id')->toArray();
					array_unshift($parent_ids, $SecondDb->id);
					$SecondDb->children = implode(',', $parent_ids);
					$SecondDb->save();
				case $allPid['1']:
					$SecondDb   = GameServer::find($allPid['0']);
					$parent_ids = GameServer::where('parent_id', $this->server->parent_id)->pluck('id')->toArray();
					array_unshift($parent_ids, $SecondDb->id);
					$SecondDb->children = implode(',', $parent_ids);

					$FirstDb        = GameServer::find($allPid['1']);
					$top_parent_ids = GameServer::where('top_parent_id', $this->server->top_parent_id)->pluck('id')->toArray();
					// array_unshift($top_parent_ids, $FirstDb->id);
					$FirstDb->children = implode(',', $top_parent_ids);

					$SecondDb->save();
					$FirstDb->save();
				default:
					$this->server->children = $this->server->id;
					$this->server->save();
					break;
			}
		}*/
}
