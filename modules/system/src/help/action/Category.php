<?php namespace System\Help\Action;

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
		if (!$this->checkPermission()) {
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
			],
			'parent_id' => [
				Rule::required(),
				Rule::integer(),
			],
			'title'     => [
				Rule::required(),
				Rule::string(),
			],
		], [], [
			'type'      => trans('system::category.db.type'),
			'parent_id' => trans('system::category.db.cat_id'),
			'title'     => trans('system::category.db.title'),
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
			return $this->setError(trans('system::category.action.item_not_exist'));
		}
	}
}