<?php namespace Slt\Models;

/**
 * @mixin \Eloquent
 * @property integer        $id
 * @property integer        $prd_id
 * @property integer        $prd_version_id
 * @property string         $prd_content
 * @property string         $prd_content_origin
 * @property integer        $account_id
 * @property \Carbon\Carbon $created_at
 * @property string         $deleted_at
 * @property \Carbon\Carbon $updated_at
 */
class PrdVersion extends \Eloquent
{


	protected $table = 'prd_version';

	protected $primaryKey = 'id';
	protected $fillable   = [
		'prd_id',
		'prd_version_id',
		'prd_content',
		'prd_content_origin',
	];

}
