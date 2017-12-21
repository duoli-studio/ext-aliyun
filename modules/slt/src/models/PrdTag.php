<?php namespace Slt\Models;

/**
 * @property integer        $id
 * @property string         $tag_py        标签拼音
 * @property string         $tag_sp        标签首拼
 * @property string         $tag_title     标签标题
 * @property integer        $tag_ref_count 标签引用数量
 * @property integer        $prd_id
 * @property integer        $cat_id
 * @property string         $is_search
 * @property integer        $list_order
 * @property \Carbon\Carbon $created_at
 * @property string         $deleted_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class PrdTag extends \Eloquent
{


	protected $table = 'prd_tag';

	protected $fillable = [
		'pinyin',
		'first_py',
		'title',
		'content_id',
		'cat_id',
	];

	public static function unformat($tags, $implode = '')
	{
		$tags   = trim($tags, '_,_');
		$return = $tags ? explode('_,_', $tags) : [];
		if ($implode) {
			return implode($implode, $return);
		}
		else {
			return $return;
		}
	}

	public static function format($tags)
	{
		return $tags ? '_,_' . implode('_,_', $tags) . '_,_' : '';
	}
}
