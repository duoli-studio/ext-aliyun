<?php namespace Slt\Url\Action;

/**
 * 需求处理类
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2013-2016 Sour Lemon team
 */

use Poppy\Framework\Helper\StrHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Slt\Models\SiteTag;
use Slt\Models\SiteUrlRelTag;
use Slt\Models\SiteCollection;
use Slt\Models\SiteUrl;
use Slt\Models\SiteUserUrl;
use System\Classes\Traits\SystemTrait;

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
	 * 处理分组
	 * @param      $data
	 * @param null $id
	 * @return bool
	 */
	public function establishUrl($data, $id = null)
	{
		if (!$this->checkPam()) {
			return false;
		}
		// data
		$validator = \Validator::make($data, [
			'title'       => 'required',
			'url'         => 'required|url',
			'description' => 'max:250',
		], [], [
			'title'       => '网站标题',
			'url'         => '网站地址',
			'description' => '描述',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->initUrl($id)) {
			return false;
		}

		$title       = strval(array_get($data, 'title', ''));
		$description = strval(array_get($data, 'description', ''));
		$tag         = array_get($data, 'tag', []);
		$url         = rtrim(strval(array_get($data, 'url', '')), '/');
		$icon        = strval(array_get($data, 'icon'));


		// 主表中存在数据
		/** @var SiteUrl $objUrl */
		$objUrl = SiteUrl::where('url', '=', $url)->first();
		if (!$objUrl) {
			$objUrl = SiteUrl::create([
				'title'       => $title,
				'url'         => $url,
				'icon'        => $icon,
				'description' => $description,
				'account_id'  => $this->pam->id,
			]);
		}
		if (!$objUrl->description && $description) {
			$objUrl->description = $description;
			$objUrl->save();
		}

		// auth check
		if ($id) {

		}
		else {
			$hasAdd = SiteUserUrl::where('url_id', $objUrl->id)
				->where('account_id', $this->pam->id)
				->exists();
			if ($hasAdd) {
				return $this->setError('您已经添加此地址, 不得重复添加');
			}

		}

		$data = [
			'url_id'      => $objUrl->id,
			'title'       => $title,
			'description' => $description,
		];
		if ($id) {
			$this->url->update($data);
		}
		else {
			$data['account_id'] = $this->pam->id;
			$data['is_star']    = 0;
			$this->url          = SiteUserUrl::create($data);
		}

		$this->handleRelation($tag);

		// handle tag
		return true;
	}

	public function hasCreate($url)
	{
		if (!$this->checkPam()) {
			return false;
		}
		$objUrl = SiteUrl::where('url', '=', $url)->first();
		if (!$objUrl) {
			return false;
		}
		$hasAdd = SiteUserUrl::where('url_id', $objUrl->id)
			->where('account_id', $this->pam->id)
			->exists();
		if ($hasAdd) {
			return true;
		}
		return false;
	}

	/**
	 * 删除Url Relation
	 * @param $id
	 * @return bool
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

	/**
	 * 删除 url relation
	 * @param $id
	 * @return bool
	 */
	public function destroyUrl($id)
	{
		if (!$id) {
			return $this->setError('请选择要删除的链接');
		}

		/** @var SiteUserUrl $item */
		$item = SiteUserUrl::find($id);
		if (!$item) {
			return false;
		}

		if ($item->account_id != $this->pam->id) {
			return $this->setError('您无权删除!');
		}

		$item->delete();
		return true;
	}

	private function handleRelation($tags)
	{
		if (!is_array($tags)) {
			return;
		}
		$id = [];
		foreach ($tags as $tag) {
			/** @var SiteTag $item */
			$item = SiteTag::firstOrCreate([
				'title' => $tag,
			], [
				'title'        => $tag,
				'spell'        => StrHelper::text2py($tag),
				'first_letter' => StrHelper::text2py($tag, 0, true),
			]);
			$id[] = [
				'url_id'      => $this->url->url_id,
				'user_url_id' => $this->url->id,
				'tag_id'      => $item->id,
				'account_id'  => $this->pam->id,
			];
		}
		SiteUrlRelTag::where('url_id', $this->url->url_id)
			->where('account_id', $this->pam->id)
			->delete();

		SiteUrlRelTag::insert($id);
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

	public function initUrl($id)
	{
		try {
			$this->url = SiteUrl::findOrFail($id);
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}

	}
}