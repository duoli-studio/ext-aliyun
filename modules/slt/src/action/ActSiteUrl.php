<?php namespace Slt\Action;

/**
 * 需求处理类
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2013-2016 Sour Lemon team
 */


use Poppy\Framework\Traits\BaseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Slt\Models\SiteRelCat;
use Slt\Models\SiteUrl;

class ActSiteUrl
{

	use BaseTrait, AuthorizesRequests;

	/** @var  SiteUrl */
	protected $item;

	private $id;

	/**
	 * @return SiteUrl
	 */
	public function getItem()
	{
		return $this->item;
	}

	/**
	 * 创建需求
	 * @param      $data
	 * @param null $id
	 * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function establish($data, $id = null)
	{
		// data
		$validator = \Validator::make($data, [
			'title'      => 'required',
			'url'        => 'required|url',
			'cat_ids'    => 'required',
			'list_order' => 'required|integer',
		], [], [
			'title'      => '导航标题',
			'url'        => '导航链接',
			'list_order' => '排序',
			'cat_ids'    => '分类',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->init($id)) {
			return false;
		}

		$title       = strval(array_get($data, 'title', ''));
		$cat_id      = strval(trim(array_get($data, 'cat_ids', ''), ','));
		$url         = strval(rtrim(array_get($data, 'url', ''), '/'));
		$description = strval(array_get($data, 'description', ''));
		$image       = strval(array_get($data, 'image', ''));

		// trim end slash

		// check exist
		$Db = SiteUrl::where('url', '=', $url);
		if ($this->id
			? (clone $Db)->where('id', '!=', $this->id)->exists() // edit
			: (clone $Db)->exists() // create
		) {
			return $this->setError('已经存在此URL!');
		}

		// authorize

		// data handler
		$item = [
			'title'       => $title,
			'url'         => $url,
			'cat_ids'     => $cat_id,
			'description' => $description,
			'image'       => $image,
			'list_order'  => intval(array_get($data, 'list_order', 0)),
		];

		if ($this->id) {
			$this->item->update($item);
		}
		else {
			$this->item = SiteUrl::create($item);
		}

		$this->relCat($cat_id);

		return true;
	}


	/**
	 * 删除分类
	 * @param $id
	 * @return bool
	 */
	public function destroy($id)
	{
		// init
		if ($id && !$this->init($id)) {
			return false;
		}

		$this->item->delete();
		return true;
	}

	public function suggest($id)
	{
		// init
		if ($id && !$this->init($id)) {
			return false;
		}
		/** @var SiteUrl $nav */
		$nav             = SiteUrl::find($id);
		$nav->is_suggest = intval(boolval(!$nav->is_suggest));
		$nav->save();
		return true;
	}

	private function relCat($cat_ids)
	{
		SiteRelCat::where('site_id', $this->item->id)->delete();
		$arrCat = explode(',', $cat_ids);
		if ($arrCat) {
			$data = [];
			foreach ($arrCat as $cat_id) {
				$data[] = [
					'cat_id'  => $cat_id,
					'site_id' => $this->item->id,
				];
			}
			SiteRelCat::insert($data);
		}
	}

	private function init($id)
	{
		try {
			$this->item = SiteUrl::findOrFail($id);
			$this->id   = $this->item->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}

	}

}