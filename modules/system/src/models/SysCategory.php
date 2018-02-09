<?php namespace System\Models;

use Illuminate\Database\Query\Builder;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\FilterTrait;


/**
 * System\Model\Help
 *
 * @property int                 $id               分类ID
 * @property string              $type             类型
 * @property int                 $parent_id        父级ID
 * @property string              $title            标题
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $created_at
 * @mixin \Eloquent
 * @method static Builder|SysCategory filter($input = array(), $filter = null)
 * @method static Builder|SysCategory pageFilter(PageInfo $pageInfo)
 * @method static Builder|SysCategory paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static Builder|SysCategory simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page')
 * @method static Builder|SysCategory whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysCategory whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysCategory whereLike($column, $value, $boolean = 'and')
 */
class SysCategory extends \Eloquent
{
	use FilterTrait;

	const TYPE_HELP     = 'help';
	const TYPE_ACTIVITY = 'activity';

	protected $table = 'sys_category';

	protected $fillable = [
		'type',
		'parent_id',
		'title',
	];

}

