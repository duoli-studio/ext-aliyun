<?php namespace Slt\Models;

use Carbon\Carbon;
use System\Models\PamAccount;


/**
 * \Slt\Models\ArticleContent
 *
 * @property int             $id
 * @property int             $parent_id
 * @property int             $book_id  所属文档ID
 * @property string          $title
 * @property string          $description
 * @property string          $content_md
 * @property string          $content  内容
 * @property string          $author
 * @property string          $icon
 * @property int             $good_num 点赞
 * @property int             $bad_num  差评
 * @property string          $password 访问密码
 * @property int             $list_order
 * @property int             $hits
 * @property string          $status
 * @property int             $account_id
 * @property int             $is_star  是否星标
 * @property string          $tag_note tag 标记
 * @property Carbon|null     $created_at
 * @property Carbon|null     $updated_at
 * @property-read PamAccount $pam
 * @mixin \Eloquent
 */
class ArticleContent extends \Eloquent
{

	const STATUS_DELETE = 'delete';
	const STATUS_TRASH  = 'trash';
	const STATUS_POST   = 'post';
	const STATUS_DRAFT  = 'draft';

	const ROLE_STATUS_NONE = 0;
	const ROLE_STATUS_PWD  = 1;

	const TYPE_PRIVATE = 0;
	const TYPE_PUBLIC  = 1;
	const TYPE_TRANS   = 2;
	const TYPE_TEAM    = 3;

	protected $table = 'article_content';

	protected $fillable = [
		'title',
		'cat_id',
		'parent_id',
		'book_id',
		'content',
		'content_md',
		'account_id',
		'role_status',
		'password',
		'description',
		'tag',
		'is_star',
		'tag_note',
		'author',
		'icon',
		'hits',
		'good_num',
		'bad_num',
		'list_order',
		'is_star',
		'prd_tag',
	];

	public function pam()
	{
		return $this->belongsTo(PamAccount::class, 'account_id', 'id');
	}


	/**
	 * 文档状态
	 * @param null $key
	 * @param bool $check_key
	 * @return array|string
	 */
	public static function kvStatus($key = null, $check_key = false)
	{
		$desc = [
			self::STATUS_DELETE => '删除',
			self::STATUS_TRASH  => '回收',
			self::STATUS_POST   => '发布',
			self::STATUS_DRAFT  => '草稿',
		];
		return kv($desc, $key, $check_key);
	}

	public static function kvType($key = null)
	{
		$desc = [
			self::TYPE_PRIVATE => '私有',
			self::TYPE_PUBLIC  => '公有',
			self::TYPE_TRANS   => '转存',
		];
		return kv($desc, $key);
	}

	/**
	 * 顶级 parent id
	 * @param $id
	 * @return mixed
	 */
	public static function topParentId($id)
	{
		$parent_id = self::where('id', $id)->value('parent_id');
		if ($parent_id == 0) {
			return $id;
		}
		else {
			return self::topParentId($parent_id);
		}
	}

	/**
	 * 所有的父级别元素
	 * @param        $id
	 * @param string $ids
	 * @return string
	 */
	public static function parentIds($id, $ids = '')
	{
		$parent_id = self::where('id', $id)->value('parent_id');
		if ($parent_id == 0) {
			return ltrim($ids, ',');
		}
		else {
			$pids = self::parentIds($parent_id, $ids);
			if ($pids) {
				return $pids . ',' . $parent_id;
			}
			else {
				return $parent_id;
			}
		}
	}

	/**
	 * 多级标题
	 * @param $id
	 * @return \Illuminate\Support\Collection
	 */
	public static function parentTitles($id, $self = false, $crypt = true, $num = 3)
	{
		$parent_ids = explode(',', self::parentIds($id) . ($self ? ',' . $id : ''));
		$titles     = self::whereIn('id', $parent_ids)
			->select(['title', 'id'])->get();
		foreach ($titles as $k => &$v) {
			$v->crypt_url = route('slt:article.show', [$v->id]);
		}
		if ($titles->count() > $num) {
			// 多于数量, 去掉最顶部的标题
			$removeCount = $titles->count() - $num;
			if ($removeCount) {
				for ($i = 0; $i < $removeCount; $i++) {
					$titles->shift();
				}
			}
		}
		return $titles;
	}


	/**
	 * prd 文档的类型计算
	 * @param     $account_id
	 * @param int $type
	 * @return int
	 */
	public static function userNum($account_id, $type = self::TYPE_PUBLIC)
	{
		$DbCount = self::where('account_id', $account_id)
			->where('parent_id', 0)
			->where('status', self::STATUS_POST);
		switch ($type) {
			case self::TYPE_PRIVATE:
				$DbCount->where('type', self::TYPE_PRIVATE);
				break;
			case self::TYPE_TRANS:
				$DbCount->where('type', self::TYPE_TRANS);
				break;
			case self::TYPE_PUBLIC:
			default:
				$DbCount->where('type', self::TYPE_PUBLIC);
				break;
		}
		return $DbCount->count();
	}

	/**
	 * 是否原型被某用户转存过
	 * @param $id
	 * @param $account_id
	 * @return bool
	 */
	public static function hasTransfer($id, $account_id)
	{
		return self:: where('ori_id', $id)
			->where('account_id', $account_id)
			->exists();
	}

	public static function transferNum($id)
	{
		return self::where('ori_id', $id)
			->where('parent_id', 0)
			->count();
	}

	/**
	 * 解析 markdown 内联样式
	 * @param string $content
	 * @param int    $project_id
	 * @return mixed|string
	 */
	public static function mdInlineLink($content = '', $project_id = 0, $is_self = true)
	{
		if (!$content) {
			return '';
		}
		preg_match_all('/\[\[(.*?)\]\]/', $content, $matches, PREG_SET_ORDER);
		if (is_array($matches) && isset($matches[0])) {
			foreach ($matches as $match) {
				$replace = $match[0];
				$title   = $match[1];
				if ($is_self) {
					$content = str_replace($replace, '<a href="' . route('front_prd.name', [$project_id, $title]) . '" title="' . $title . '">' . $title . '</a>', $content);
				}
				else {
					$content = str_replace($replace, '<a href="' . route('front_prd.view_name', [$project_id, $title]) . '" title="' . $title . '">' . $title . '</a>', $content);
				}

			}
		}
		return $content;
	}
}
