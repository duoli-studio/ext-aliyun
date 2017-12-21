<?php namespace Slt\Models;

use Carbon\Carbon;


/**
 * php artisan ide-helper:model 'App\Models\SiteUrl'
 *
 * @property int               $id
 * @property string            $title        导航名称
 * @property string            $image        导航图标
 * @property string            $description  导航图标
 * @property string            $url          导航链接
 * @property string            $cat_ids      所属分类id
 * @property int               $list_order   显示排序
 * @property int               $hits         点击次数
 * @property bool              $is_suggest   是否推荐
 * @property Carbon            $created_at
 * @property Carbon            $updated_at
 * @property-read BaseCategory $category
 * @mixin \Eloquent
 */
class SiteUrl extends \Eloquent
{

	protected $table = 'site_url';

	protected $primaryKey = 'id';

	public $timestamps = true;

	protected $fillable = [
		'title',
		'icon',
		'url',
		'description',
		'cat_ids',
		'is_user',
		'is_suggest',
		'list_order',
		'hits',
	];


	public function category()
	{
		return $this->hasOne('App\Models\BaseCategory', 'id', 'cat_id');
	}

}
