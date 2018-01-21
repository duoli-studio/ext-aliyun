<?php namespace System\Models;
use System\Classes\Traits\FilterTrait;


/**
 * System\Model\Help
 *
 * @property int                 $id            帮助ID
 * @property string              $type          帮助类型
 * @property int                 $cat_id        分类ID
 * @property string              $title         标题
 * @property string              $content       内容
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $created_at
 * @mixin \Eloquent
 */
class SysHelp extends \Eloquent
{
	use FilterTrait;

	protected $table = 'sys_help';

	protected $fillable = [
		'type',
		'cat_id',
		'title',
		'content',
	];

	public function category()
	{
		return $this->hasOne(SysCategory::class,'id','cat_id');
	}

}

