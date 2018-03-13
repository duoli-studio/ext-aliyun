<?php namespace Slt\Action;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Slt\Models\SiteCollection;
use Slt\Models\SiteUserUrl;
use System\Classes\Traits\SystemTrait;

/**
 * 收藏夹
 */
class Collection
{

	use SystemTrait, AuthorizesRequests;

	/** @var  SiteCollection */
	protected $item;

	private $id;

	private $group;

	/** @var  SiteUserUrl */
	private $url;


	/**
	 * @return SiteCollection
	 */
	public function getItem()
	{
		return $this->item;
	}

	public function getUrl()
	{
		return $this->url;
	}


	public function setGroup($group)
	{
		$this->group = $group;
		return $this;
	}


	/**
	 * 创建需求
	 * @param      $data
	 * @param null $id
	 * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function establish($data, $id = null)
	{
		if (!$this->checkPam()) {
			return false;
		}
		$validator = \Validator::make($data, [
			'title' => 'required',
		], [], [
			'收藏夹标题' => '标题',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->initCategory($id)) {
			return false;
		}

		$title = strval(array_get($data, 'title', ''));
		$icon  = strval(array_get($data, 'icon', ''));
		// check exist
		$Db = SiteCollection::where('account_id', $this->pam->id)
			->where('title', '=', $title);
		if ($this->id
			? (clone $Db)->where('id', '!=', $this->id)->exists() // edit
			: (clone $Db)->exists() // create
		) {
			return $this->setError('已经存在同名收藏夹!');
		}

		// authorize

		if ($this->id) {
			if ($this->item->account_id != $this->pam->id) {
				return $this->setError('您无权修改此分类');
			}
		}


		// data handler
		$item = [
			'title'      => $title,
			'icon'       => $icon,
			'list_order' => intval(array_get($data, 'list_order', 0)),
		];

		if ($this->id) {
			$this->item->update($item);
		}
		else {
			$itemInit = [
				'account_id' => $this->pam->id,
				'num'        => 0,
			];
			$item     = $item + $itemInit;

			$this->item = SiteCollection::create($item);
		}
		return true;
	}


	/**
	 * 删除Url Relation
	 * @param $id
	 * @return bool
	 * @throws \Exception
	 */
	public function destroy($id)
	{
		if (!$this->checkPam()) {
			return false;
		}
		// init
		if ($id && !$this->initCategory($id)) {
			return false;
		}

		if ($this->item->account_id != $this->pam->id) {
			return $this->setError('您无权删除!');
		}

		if (SiteUserUrl::where('collection_id', $this->item->id)->exists()) {
			return $this->setError('收藏夹下有链接, 请删除后再删除收藏夹!');
		}

		$this->item->delete();
		return true;
	}


	public function initCategory($id)
	{
		try {
			$this->item = SiteCollection::findOrFail($id);
			$this->id   = $this->item->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}

	}
}