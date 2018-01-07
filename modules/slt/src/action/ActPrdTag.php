<?php namespace Slt\Action;

use Poppy\Framework\Helper\StrHelper;
use Illuminate\Support\Collection;
use Slt\Models\PrdContent;
use Slt\Models\PrdTag;
class ActPrdTag
{

	use BaseTrait;

	/**
	 * 更新博客的标签关联
	 * @param $blog PrdContent
	 * @return bool
	 * @throws \Exception
	 */
	public function handle($blog)
	{

		// 删除所有 tag
		PrdTag::where('id', $blog->id)->delete();

		$tags = PrdTag::unformat($blog->prd_tag);

		// 处理 tag 并且加入
		$tags       = array_map('trim', $tags);
		$formatTags = [];
		if (is_array($tags)) {
			foreach ($tags as $tag) {
				if (!$tag) {
					continue;
				}
				$formatTags[] = [
					'pinyin'     => StrHelper::text2py($tag),
					'first_py'   => StrHelper::text2py($tag, 0, true),
					'title'      => $tag,
					'content_id' => $blog->id,
				];
			}
			PrdTag::insert($formatTags);
		}

		// 更新引用数量
		$this->calcRefCount($tags);
		return true;
	}

	/**
	 * 更新引用数量
	 * @param $tags array 需要更新引用数量的标签, 一维数组 ['标签A','标签B','标签C',]
	 */
	public function calcRefCount($tags)
	{
		$table = (new PrdTag())->getTable();
		$refs  = \DB::table($table)->select(\DB::raw('count(*) as ref_num, title'))
			->whereIn('title', $tags)
			->groupBy('title')
			->get();
		if ($refs) {
			(new Collection($refs))->each(function ($item) {
				PrdTag::where('title', $item->title)->update([
					'tag_ref_count' => $item->ref_num,
				]);
			});
		}
	}


}