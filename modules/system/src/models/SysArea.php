<?php namespace System\Models;


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
 * @mixin \Eloquent
 */
class SysArea extends \Eloquent
{
	use FilterTrait;

	protected $table = 'sys_area';

	public $timestamps = false;

	protected $fillable = [
		'code',
		'title',
		'parent_id',
		'top_parent_id',
		'children',
	];

}

