<?php namespace Order\Action;

/**
 * 游戏服务器操作
 */
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Validation\Rule;
use Order\Models\GameServer;
use System\Classes\Traits\SystemAppTrait;

class ActGameServer
{
	use SystemAppTrait;

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
			GameServer::genCode();
		}
		return true;
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

	public function genId($pid)
	{
		$id = GameServer::where('id',$pid)->value('id');
		$parent_id = GameServer::where('id',$id)->value('parent_id');

		if ($parent_id === 0){
			return $id;
		}
		else{
			$id = GameServer::where('id',$pid)->value('id');
			return $id;
		}

	}
}