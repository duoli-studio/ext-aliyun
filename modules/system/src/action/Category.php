<?php namespace System\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\SysCategory;

class Category
{
	use SystemTrait;

	/**
	 * @var SysCategory
	 */
	protected $category;

	/**
	 * @var int SysCategory id
	 */
	protected $categoryId;

	/**
	 * @var string
	 */
	protected $categoryTable;

	public function __construct()
	{
		$this->categoryTable = (new SysCategory())->getTable();
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
			'type'      => strval(array_get($data, 'type', '')),
			'parent_id' => intval(array_get($data, 'parent_id', 0)),
			'title'     => strval(array_get($data, 'title', '')),
		];
		$validator = \Validator::make($initDb, [
			'type'      => [
				Rule::required(),
				Rule::string(),
				Rule::in([
					SysCategory::TYPE_ACTIVITY,
					SysCategory::TYPE_HELP
				])
			],
			'parent_id' => [
				Rule::required(),
				Rule::integer(),
			],
			'title'     => [
				Rule::required(),
				Rule::string(),
				Rule::unique($this->categoryTable, 'title')->where('parent_id', $data['parent_id'])
			],
		], [], [
			'type'      => trans('system::db.category.type'),
			'parent_id' => trans('system::db.category.cat_id'),
			'title'     => trans('system::db.category.title'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->initCategory($id)) {
			return false;
		}

		if ($id) {
			$this->category->update($initDb);

		}
		else {
			/** @var SysCategory $category */
			$category = SysCategory::create($initDb);

			$this->category = $category;
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
		if ($id && !$this->initCategory($id)) {
			return false;
		}
		return $this->category->delete();
	}


	/**
	 * 初始化id
	 * @param int $id
	 * @return bool
	 */
	private function initCategory($id)
	{
		try {
			$this->category   = SysCategory::findOrFail($id);
			$this->categoryId = $this->category->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError(trans('system::action.category.item_not_exist'));
		}
	}
}