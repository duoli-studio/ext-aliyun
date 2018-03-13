<?php namespace Slt\Models;

use Carbon\Carbon;


/**
 * php artisan ide-helper:model 'App\Models\SiteUserUrlRelation'
 *
 * @property int    $id
 * @property int    $url_id          URL id
 * @property string $title           用户自有标题
 * @property int    $collection_id   导航ID
 * @property int    $list_order      显示排序
 * @property bool   $is_favor        是否喜欢的
 * @property int    $account_id      账号ID
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @mixin \Eloquent
 */
class SiteUserUrl extends \Eloquent
{

	protected $table = 'site_user_url';

	protected $primaryKey = 'id';

	public $timestamps = true;

	protected $fillable = [
		'title',
		'url_id',
		'is_star',
		'account_id',
	];


	public function siteUrl()
	{
		return $this->hasOne(SiteUrl::class, 'id', 'url_id');
	}

	public function siteUrlRelTag()
	{
		return $this->hasMany(SiteUrlRelTag::class, 'user_url_id', 'id');
	}
}
