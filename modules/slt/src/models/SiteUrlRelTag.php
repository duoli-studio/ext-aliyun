<?php namespace Slt\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;


/**
 * php artisan ide-helper:model 'App\Models\SiteUrl'
 *
 * @property int               $id
 * @property string            $title        导航名称
 * @property string            $image        导航图标
 * @property string            $description  导航图标
 * @property string            $url          导航链接
 * @property string            $cat_ids      所属分类id
 * @property int               $list_order   显示排序
 * @property int               $hits         点击次数
 * @property bool              $is_suggest   是否推荐
 * @property Carbon            $created_at
 * @property Carbon            $updated_at
 * @property-read BaseCategory $category
 * @mixin \Eloquent
 */
class SiteUrlRelTag extends \Eloquent
{

	protected $table = 'site_url_rel_tag';

	public $timestamps = false;

	protected $fillable = [
		'user_url_id',
		'url_id',
		'tag_id',
		'account_id',
	];

	public function siteTag()
	{
		return $this->hasOne(SiteTag::class, 'id', 'tag_id');
	}

	public function siteUrl()
	{
		return $this->hasOne(SiteUrl::class, 'id', 'url_id');
	}

	public function siteUserUrl()
	{
		return $this->hasOne(SiteUserUrl::class, 'id', 'user_url_id');
	}

	public static function userTag($id, $search = '')
	{
		$kw          = $search;
		$tbSiteTag   = (new SiteTag())->getTable();
		$tbUrlRelTag = (new SiteUrlRelTag())->getTable();

		$tags = \DB::table($tbSiteTag)
			->select([$tbSiteTag . '.title', $tbSiteTag . '.id'])
			->join($tbUrlRelTag, function ($join) use ($tbSiteTag, $tbUrlRelTag) {
				$join->on($tbSiteTag . '.id', '=', $tbUrlRelTag . '.tag_id');
			})
			->distinct()
			->where(function ($query) use ($kw, $tbSiteTag) {
				if ($kw) {
					$query->where(function ($q) use ($kw, $tbSiteTag) {
						$q->orWhere($tbSiteTag . '.title', 'like', '%' . $kw . '%');
						$q->orWhere($tbSiteTag . '.spell', 'like', '%' . $kw . '%');
						$q->orWhere($tbSiteTag . '.first_letter', 'like', '%' . $kw . '%');
					});
				}
			})
			->where($tbUrlRelTag . '.account_id', $id)
			->get();

		// dd($tags);
		$data = [];
		if ($tags->count()) {
			foreach ($tags as $tag) {
				$data[] = [
					'title'  => $tag->title,
					'tag_id' => $tag->id,
				];
			}
		}
		return $data;
	}

	/**
	 * @param $userRelTag [] SiteUrlRelTag
	 * @return string
	 */
	public static function translate($userRelTag)
	{
		static $tags;
		if (empty($userRelTag)) {
			return '';
		}
		if ($userRelTag instanceof Collection && $userRelTag->isEmpty()) {
			return '';
		}
		if (!$tags) {
			$rel  = $userRelTag[0];
			$tags = self::userTag($rel->account_id);
		}
		if (!$tags) {
			return '';
		}
		$kvTags = [];
		foreach ($tags as $tag) {
			$kvTags[$tag['tag_id']] = $tag['title'];
		}

		$data = '';
		foreach ($userRelTag as $rel) {
			$tag = isset($kvTags[$rel->tag_id]) ? $kvTags[$rel->tag_id] : '';
			if ($tag) {
				$data .= '<a href="' . route_url('web:nav.index', null, ['tag' => $tag]) . '">' . $tag . '</a>';
			}
		}
		return $data;
	}
}
