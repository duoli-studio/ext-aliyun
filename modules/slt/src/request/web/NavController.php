<?php namespace Slt\Request\Web;

use Curl\Curl;
use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\FileHelper;
use Slt\Models\SiteCollection;
use Slt\Models\SiteTag;
use Slt\Models\SiteUrl;
use Slt\Models\SiteUrlRelTag;
use Slt\Models\SiteUserUrl;
use Slt\Url\Action\Collection;
use System\Models\PamAccount;

class NavController extends InitController
{

	/**
	 * 导航地址
	 * @return mixed
	 */
	public function index()
	{
		$tag = \Input::get('tag');
		/** @var PamAccount $pam */
		$pam  = $this->getWebGuard()->user();
		$tags = SiteUrlRelTag::userTag($pam->id);
		$Db   = SiteUserUrl::where('account_id', $pam->id);
		if ($tag) {
			$arrTag = explode('|', $tag);
			$ids    = SiteTag::whereIn('title', $arrTag)->pluck('id');
			$urlIds = SiteUrlRelTag::whereIn('tag_id', $ids)
				->havingRaw('count(tag_id)=' . count($ids))
				->groupBy('user_url_id')
				->pluck('user_url_id');
			$Db->whereIn('id', $urlIds);
		}
		$urls =
			$Db->with(['siteUrlRelTag', 'siteUrl'])
				->paginate($this->pagesize);
		return view('slt::nav.index', [
			'items' => $urls,
			'tags'  => $tags,
		]);
	}

	public function jump($id)
	{
		$referer = \Input::server('HTTP_REFERER');
		if (parse_url($referer)['host'] != parse_url(config('app.url'))['host']) {
			return Resp::web(Resp::ERROR, '来源非法');
		}
		/** @var SiteUrl $url */
		$url       = SiteUrl::find($id);
		$url->hits += 1;
		$url->save();
		return Resp::web('OK~正在访问链接', 'location|' . $url->url);
	}

	public function jumpUser($id)
	{
		/** @var SiteUrl $url */
		$rel            = SiteUserUrl::with('url')->find($id);
		$rel->url->hits += 1;
		$rel->url->save();

		$rel->hits += 1;
		$rel->save();
		return Resp::web('正在访问链接', 'location|' . $rel->url->url);
	}


	/**
	 * 设定为已读
	 * @param null $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function collection($id = null)
	{
		if (is_post()) {
			$Collection = (new Collection())->setPam($this->getWebGuard()->user());
			if ($Collection->establish(\Input::all(), $id)) {
				return Resp::web(Resp::SUCCESS, '操作成功!', 'top_reload|1');
			}
			else {
				return Resp::web(Resp::ERROR, $Collection->getError());
			}
		}
		if ($id) {
			/** @var SiteCollection $item */
			$item = SiteCollection::find($id);
			\View::share('item', $item);
		}

		$icons = FileHelper::listFile(public_path('modules/slt/images/collection_icon'));

		$options = [];
		foreach ($icons as $icon) {
			$key       = basename($icon, '.png');
			$src       = str_replace(base_path('public'), '', $icon);
			$options[] = '<option ' . ((isset($item) && $item->icon == $key) ? ' selected="selected" ' : '') . ' value="' . $key . '" data-img-src="' . config('app.url') . '/' . $src . '">' . $key . '</option>';
		}
		return view('slt::nav.collection', [
			'options' => $options,
		]);
	}


	/**
	 * @param null $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 * @throws \Exception
	 */
	public function url($id = null)
	{
		$Collection = (new Collection())->setPam($this->getWebGuard()->user());
		if (is_post()) {
			if ($Collection->establishUrl(\Input::all(), $id)) {
				return Resp::web(Resp::SUCCESS, '操作成功!', 'top_reload|1');
			}
			else {
				return Resp::web(Resp::ERROR, $Collection->getError());
			}
		}

		$url         = '';
		$title       = '';
		$description = '';
		$icon        = '';
		if ($id) {
			if ($Collection->initUrl($id)) {
				// todo
			}
			else {
				return Resp::web(Resp::ERROR, $Collection->getError());
			}

		}
		else {
			$url         = rtrim(\Input::get('url'), '/');
			$title       = \Input::get('title');
			$description = \Input::get('description');
			$img_url     = \Input::get('img_url');
			$imgUrls     = explode(',', $img_url);
			if (count($imgUrls)) {
				$icon = $imgUrls[0];
			}
			if ($url && $Collection->hasCreate($url)) {
				return Resp::web(Resp::ERROR, '您已经添加了该网址, 请不要重复添加!');
			}
		}

		return view('slt::nav.url', [
			'url'         => $url,
			'title'       => $title,
			'description' => $description,
			'icon'        => $icon,
		]);
	}

	public function tag()
	{
		$kw = strval(trim(\Input::get('search')));
		/** @var PamAccount $user */
		$user = $this->getWebGuard()->user();
		$tags = SiteUrlRelTag::userTag($user->id, $kw);
		$data = [];
		if (count($tags)) {
			foreach ($tags as $tag) {
				$data[] = [
					'value' => $tag['title'],
					'text'  => $tag['title'],
				];
			}
		}
		echo json_encode($data);
	}

	/**
	 * 批量删除
	 * @param null $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function collectionDestroy($id = null)
	{
		if (!$id) {
			return Resp::web(Resp::ERROR, '请选中要删除的信息');
		}
		$Collection = (new Collection())->setPam($this->getWebGuard()->user());
		if ($Collection->destroy($id)) {
			return Resp::web(Resp::SUCCESS, 'OK~删除成功', 'top_reload | 1');
		}
		else {
			return Resp::web(Resp::ERROR, $Collection->getError());
		}
	}


	public function urlDestroy($id)
	{
		if (!$id) {
			return Resp::web(Resp::ERROR, '请选中要删除的链接');
		}
		$Collection = (new Collection())->setPam($this->getWebGuard()->user());
		if ($Collection->destroyUrl($id)) {
			return Resp::web(Resp::SUCCESS, '删除成功', 'top_reload | 1');
		}
		else {
			return Resp::web(Resp::ERROR, $Collection->getError());
		}
	}

	/**
	 * 获取标题
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function fetchTitle(Request $request)
	{
		$url = $request->input('url');
		if (!$url) {
			return Resp::web(Resp::ERROR, '请填写url地址!');
		}
		$curl = new Curl();
		$curl->setTimeout(2);
		if (!preg_match(' /^http(s)?:\/\/.*?/', $url)) {
			$url = 'http://' . $url;
		}
		$content = $curl->get($url);
		if (preg_match("/<title>(.*?)<\/title>/i", $content, $match)) {
			return Resp::web(Resp::SUCCESS, '获取到标题', [
				'title'  => $match[1],
				'url'    => $url,
				'forget' => true,
			]);
		}
		else {
			return Resp::web('没有找到相关网页标题', [
				'title'  => $url,
				'url'    => $url,
				'forget' => true,
			]);
		}
	}
}

