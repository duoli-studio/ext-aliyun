<?php namespace Order\Game\Action;

/**
 * 游戏服务器操作
 */
use Poppy\Framework\Validation\Rule;
use Order\Models\GameServer;
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
		/*if (!$this->checkBe('backend:global.role.manage')) {
			return false;
		}*/
		$initDb    = [
			'title'     => strval(array_get($data, 'title', '')),
			'parent_id' => strval(array_get($data, 'parent_id', '')),
		];
		$validator = \Validator::make($initDb, [
			'title'     => [
				Rule::required(),
				Rule::unique($this->gameServerTable, 'title')->where(function($query) use ($id) {
					if ($id) {
						$query->where('id', '!=', $id);
					}
				}),
			],
			'parent_id' => [
				Rule::required(),
				Rule::integer(),
			],
		], [], [
			'title'     => trans('system::db.game_server.title'),
			'parent_id' => trans('system::db.game_server.parent_id'),
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
			$this->item = GameServer::create($initDb);
			$this->item->code = $this->genCode($this->item->id);
			$this->item->save();
		}
		return true;
	}

	/**
	 * 生成游戏服务器编码
	 * @param int $id
	 * @return bool|string
	 */
	public function genCode($id)
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

	public function init($id)
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