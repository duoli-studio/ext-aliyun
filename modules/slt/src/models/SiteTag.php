<?php namespace Slt\Models;

use Carbon\Carbon;

/**
 * php artisan ide-helper:model 'App\Models\SiteCollection'
 *
 * @mixin \Eloquent
 * @property int    $id
 * @property string $title
 * @property string $icon
 * @property string $num
 * @property string $account_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SiteTag extends \Eloquent
{

	protected $table = 'site_tag';

	protected $primaryKey = 'id';

	public $timestamps = true;

	protected $fillable = [
		'title',
		'spell',
		'first_letter',
		'list_order',
		'ref_num',
	];

}
