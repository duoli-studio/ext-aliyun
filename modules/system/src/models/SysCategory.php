<?php namespace System\Models;

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
 */
class SysCategory extends \Eloquent
{
	use FilterTrait;

	protected $table = 'sys_category';

	protected $fillable = [
		'type',
		'parent_id',
		'title',
	];

}

