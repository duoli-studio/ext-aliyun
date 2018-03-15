<?php namespace System\Action;

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
		if (!$this->checkPam()) {
			return false;
		}

		$cat_id    = intval(array_get($data, 'cat_id'));
		if (!SysCategory::find($cat_id)){
			return $this->setError(trans('system::action.help.parent_id_not_exists'));
		}
		$parent_id = intval(SysCategory::find($cat_id)->parent_id);
		if ($cat_id === SysConfig::NO || $parent_id === SysConfig::NO) {
			return $this->setError(trans('system::action.help.parent_error'));
		}

		$initDb    = [
			'cat_id'  => $cat_id,
			'title'   => strval(array_get($data, 'title', '')),
			'content' => strval(array_get($data, 'content', '')),
		];
		$validator = \Validator::make($initDb, [
			'cat_id'  => [
				Rule::required(),
				Rule::integer(),
			],
			'title'   => [
				Rule::required(),
				Rule::string(),
				Rule::unique($this->helpTable, 'title')->where('cat_id', $cat_id),
			],
			'content' => [
				Rule::required(),
				Rule::string(),
			],
		], [], [
			'cat_id'  => trans('system::db.help.cat_id'),
			'title'   => trans('system::db.help.title'),
			'content' => trans('system::db.help.content'),
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
		if (!$this->checkPam()) {
			return false;
		}
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
			return $this->setError(trans('system::action.help.item_not_exist'));
		}
	}
}