<?php namespace System\Models;

use Illuminate\Database\Eloquent\Builder;
use Poppy\Framework\Classes\Traits\KeyParserTrait;

/**
 * @property int $id          配置id
 * @property string  $namespace   命名空间
 * @property string  $group       配置分组
 * @property string  $item        配置名称
 * @property string  $value       配置值
 * @property string  $description 配置介绍
 * @mixin \Eloquent
 * @method static Builder|SysConfig applyKey($key)
 */
class SysConfig extends \Eloquent
{
	use KeyParserTrait;

	// 数据库使用 0/1 来代表关/开
	const YES = 1;
	const NO  = 0;

	// time define
	const HOUR_MIN      = 60;
	const HALF_DAY_MIN  = 720;
	const DAY_MIN       = 1440;
	const HALF_WEEK_MIN = 5040;
	const WEEK_MIN      = 10080;
	const MONTH_MIN     = 43200;

	protected $table = 'sys_config';

	protected static $cache = [];

	protected $fillable = [
		'namespace',
		'group',
		'item',
		'value',
		'description',
	];

	public $timestamps = false;

	public static function kvYn($key = null)
	{
		$desc = [
			self::NO  => '否',
			self::YES => '是',
		];

		return kv($desc, $key);
	}

	/**
	 * Scope to find a setting record for the specified module (or plugin) name and setting name.
	 * @param Builder $query
	 * @param string  $key Specifies the setting key value, for example 'system:updates.check'
	 * @return Builder
	 */
	public function scopeApplyKey($query, $key)
	{
		list($namespace, $group, $item) = $this->parseKey($key);

		$query = $query
			->where('namespace', $namespace)
			->where('group', $group)
			->where('item', $item);

		return $query;
	}
}

