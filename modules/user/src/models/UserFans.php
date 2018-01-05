<?php namespace User\Models;


/**
 * App\Models\UserFans
 *
 * @property int $account_id 被关注者id
 * @property int $fans_id    粉丝id(操作者id)
 */
class UserFans extends \Eloquent
{


	protected $table = 'user_fans';

	public $timestamps = false;

	protected $fillable = [
		'account_id',
		'fans_id',
	];

}
