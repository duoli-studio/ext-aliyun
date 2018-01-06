<?php namespace User\Fans\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;
use User\Models\UserProfile;

/**
 * Class SettingType.
 */
class FansHandleType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'fans_handle';
		$this->attributes['description'] = trans('user::fans.graphql.handle.type');
		$this->attributes['values']      = [
			'attention' => [
				'description' => trans('user::fans.graphql.handle.attention'),
			],
			'cancel'    => [
				'description' => trans('user::fans.graphql.handle.cancel'),
			],
		];
	}
}
