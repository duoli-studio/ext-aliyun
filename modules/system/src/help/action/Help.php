<?php namespace System\Help\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\SysCategory;
use System\Models\SysConfig;
use System\Models\SysHelp;

class Help
{
	use SystemTrait;

	/**
	 * @var SysHelp
	 */
	protected $help;

	/**
	 * @var int SysHelp id
	 */
	protected $helpId;

	/**
	 * @var string
	 */
	protected $helpTable;

	public function __construct()
	{
		$this->helpTable = (new SysHelp())->getTable();
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
		$cat_id    = intval(array_get($data, 'cat_id'));
		$parent_id = intval(SysCategory::find($cat_id)->parent_id);
		if ($cat_id === SysConfig::NO || $parent_id === SysConfig::NO) {
			return $this->setError(trans('system::help.action.parent_error'));
		}
		$initDb    = [
			'type'    => strval(array_get($data, 'type', '')),
			'cat_id'  => $cat_id,
			'title'   => strval(array_get($data, 'title', '')),
			'content' => strval(array_get($data, 'content', '')),
		];
		$validator = \Validator::make($initDb, [
			'type'    => [
				Rule::required(),
				Rule::string(),
			],
			'cat_id'  => [
				Rule::required(),
				Rule::integer(),
			],
			'title'   => [
				Rule::required(),
				Rule::string(),
				Rule::unique($this->helpTable, 'title'),
			],
			'content' => [
				Rule::required(),
				Rule::string(),
			],
		], [], [
			'type'    => trans('system::help.db.type'),
			'cat_id'  => trans('system::help.db.cat_id'),
			'title'   => trans('system::help.db.title'),
			'content' => trans('system::help.db.content'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->initHelp($id)) {
			return false;
		}

		if ($id) {
			$this->help->update($initDb);

		}
		else {
			/** @var SysHelp $help */
			$help = SysHelp::create($initDb);

			$this->help = $help;
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
		if ($id && !$this->initHelp($id)) {
			return false;
		}
		return $this->help->delete();
	}


	/**
	 * 初始化id
	 * @param int $id
	 * @return bool
	 */
	private function initHelp($id)
	{
		try {
			$this->help   = SysHelp::findOrFail($id);
			$this->helpId = $this->help->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError(trans('system::help.action.item_not_exist'));
		}
	}
}