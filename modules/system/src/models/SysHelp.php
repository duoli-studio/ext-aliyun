<?php namespace System\Models;

use Illuminate\Database\Query\Builder;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\FilterTrait;

/**
 * System\Model\Help
 *
 * @property int                 $id            帮助ID
 * @property int                 $cat_id        分类ID
 * @property string              $title         标题
 * @property string              $content       内容
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $created_at
 * @mixin \Eloquent
 * @property-read \System\Models\SysCategory $category
 * @method static Builder|SysHelp filter($input = array(), $filter = null)
 * @method static Builder|SysHelp pageFilter(PageInfo $pageInfo)
 * @method static Builder|SysHelp paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static Builder|SysHelp simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page')
 * @method static Builder|SysHelp whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysHelp whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysHelp whereLike($column, $value, $boolean = 'and')
 */
class SysHelp extends \Eloquent
{
	use FilterTrait;

	protected $table = 'sys_help';

	protected $fillable = [
		'cat_id',
		'title',
		'content',
	];

	public function category()
	{
		return $this->hasOne(SysCategory::class, 'id', 'cat_id');
	}
}