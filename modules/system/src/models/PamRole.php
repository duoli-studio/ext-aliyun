<?php namespace System\Models;

use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\FilterTrait;
use System\Rbac\Contracts\RbacRoleContract;
use System\Rbac\Traits\RbacRoleTrait;

/**
 * \System\Models\PamRole
 * @property integer                         $id
 * @property string                          $name
 * @property string                          $title
 * @property string                          $description
 * @property string                          $type
 * @property boolean                         $is_system
 * @property Carbon                          $created_at
 * @property Carbon                          $updated_at
 * @property int                             $is_enable 是否可用
 * @property-read Collection|PamPermission[] $perms
 * @property-read Collection|PamAccount[]    $users
 * @method static Builder|PamRole filter($input = [], $filter = null)
 * @method static Builder|PamRole paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|PamRole simplePaginateFilter($perPage = null, $columns = [], $pageName = 'page')
 * @method static Builder|PamRole whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|PamRole whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|PamRole whereLike($column, $value, $boolean = 'and')
 * @method static Builder|PamRole pageFilter(PageInfo $pageInfo, $columns = [], $pageName = 'page')
 * @mixin \Eloquent
 */
class PamRole extends \Eloquent implements RbacRoleContract
{

	use RbacRoleTrait, FilterTrait;

	const BE_ROOT  = 'root';      // admin user
	const FE_USER  = 'user';      // web user
	const DEV_USER = 'develop';   // developer

	protected $table = 'pam_role';

	public $timestamps = false;

	protected $fillable = [
		'name',
		'title',
		'description',
		'type',
		'is_system',
	];


	/**
	 * 通过角色来获取账户类型, 由于角色在单条处理中不会存在变化, 故而可以进行静态缓存
	 * @param $role_id
	 * @return mixed
	 */
	public static function getAccountTypeByRoleId($role_id)
	{
		static $_cache;
		if (!isset($_cache[$role_id])) {
			$_cache[$role_id] = self::where('role_id', $role_id)->value('account_type');
		}
		return $_cache[$role_id];
	}


	/**
	 * 返回一维的角色对应
	 * @param null   $type
	 * @param string $key
	 * @return \Illuminate\Support\Collection
	 */
	public static function getLinear($type = null, $key = 'id')
	{
		return self::where('type', $type)->pluck('title', $key);
	}

	/**
	 * 根据账户类型获取角色
	 * @param string|null $accountType
	 * @param bool        $cache
	 * @return array
	 */
	public static function getAll($accountType = null, $cache = true)
	{
		static $roles = null;
		if (empty($roles) || !$cache) {
			if ($accountType) {
				$items = self::where('account_type', $accountType)->get()->toArray();
			}
			else {
				$items = self::all()->toArray();
			}
			$roles = LmArr::pluck($items, 'id');
		}
		return $roles;
	}


	/**
	 * 获取角色信息
	 * @param      $role_id
	 * @param null $key
	 * @param bool $cache
	 * @return null
	 */
	public static function info($role_id, $key = null, $cache = true)
	{
		$roles = self::getAll(null, $cache);
		return $key
			? (isset($roles[$role_id][$key]) ? $roles[$role_id][$key] : null)
			: $roles[$role_id];
	}
}