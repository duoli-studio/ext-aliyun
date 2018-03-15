<?php namespace System\Models;

use Illuminate\Database\Eloquent\Builder;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\FilterTrait;

/**
 * System\Models\SysArea
 *
 * @property int    $id
 * @property string $code            编码
 * @property string $title           名称
 * @property string $parent_id       父级
 * @property string $top_parent_id   顶层ID
 * @property string $children        所有的子元素
 * @property int    $has_child       是否有子元素
 * @property int    $level           级别
 * @mixin \Eloquent
 * @method static Builder|SysArea filter($input = [], $filter = null)
 * @method static Builder|SysArea pageFilter(PageInfo $pageInfo)
 * @method static Builder|SysArea paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|SysArea simplePaginateFilter($perPage = null, $columns = [], $pageName = 'page')
 * @method static Builder|SysArea whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysArea whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysArea whereLike($column, $value, $boolean = 'and')
 */
class SysArea extends \Eloquent
{
	use FilterTrait;

	protected $table = 'sys_area';

	public $timestamps = false;

	protected $fillable = [
		'title',
		'parent_id',
		'has_child',   // 是否有子集
		'level',       // 级别
		'top_parent_id',
		'children',
	];
}

