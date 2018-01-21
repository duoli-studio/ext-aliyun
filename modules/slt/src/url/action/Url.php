<?php namespace Slt\Url\Action;


use Poppy\Framework\Helper\StrHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Slt\Models\SiteTag;
use Slt\Models\SiteUrlRelTag;
use Slt\Models\SiteCollection;
use Slt\Models\SiteUrl;
use Slt\Models\SiteUserUrl;
use System\Classes\Traits\SystemTrait;


/**
 * 网址处理
 */
class Url
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
	 * 处理分组
	 * @param      $data
	 * @param null $id
	 * @return bool
	 * @throws \Exception
	 */
	public function establish($data, $id = null)
	{
		if (!$this->checkPermission()) {
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
		/** @var SiteUrl $siteUrl */
		$siteUrl = SiteUrl::where('url', '=', $url)->first();
		if (!$siteUrl) {
			$siteUrl = SiteUrl::create([
				'title'       => $title,
				'url'         => $url,
				'icon'        => $icon,
				'description' => $description,
				'account_id'  => $this->pam->id,
			]);
		}
		if (!$siteUrl->description && $description) {
			$siteUrl->description = $description;
			$siteUrl->save();
		}

		// auth check
		if ($id) {

		}
		else {
			$hasAdd = SiteUserUrl::where('url_id', $siteUrl->id)
				->where('account_id', $this->pam->id)
				->exists();
			if ($hasAdd) {
				return $this->setError('您已经添加此地址, 不得重复添加');
			}

		}

		$data = [
			'url_id'      => $siteUrl->id,
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
		if (!$this->checkPermission()) {
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
	 * @throws \Exception
	 */
	public function destroy($id)
	{
		if (!$this->checkPermission()) {
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
	 * @throws \Exception
	 */
	public function delete($id)
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

	/**
	 * @param $tags
	 * @return bool
	 * @throws \Exception
	 */
	private function handleRelation($tags)
	{
		if (!is_array($tags)) {
			return true;
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