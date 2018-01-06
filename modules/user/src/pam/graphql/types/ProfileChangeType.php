<?php namespace User\Pam\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;
use User\Models\UserProfile;

/**
 * Class SettingType.
 */
class ProfileChangeType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'profile_change';
		$this->attributes['description'] = trans('user::profile.graphql.type_desc');
		$this->attributes['values']      = [
			// todo 尽量使用常量
			UserProfile::TYPE_NICKNAME  => [
				'description' => trans('user::profile.db.nickname'),
			],
			UserProfile::TYPE_SEX       => [
				'description' => trans('user::profile.db.sex'),
			],
			UserProfile::TYPE_SIGNATURE => [
				'description' => trans('user::profile.db.signature'),
			],
			// todo
			UserProfile::TYPE_GAME      => [
				'description' => trans('user::profile.db.game'),
			],
			UserProfile::TYPE_HEAD_PIC  => [
				'description' => trans('user::profile.db.head_pic'),
			],
			UserProfile::TYPE_AREA      => [
				'description' => trans('user::profile.db.area'),
			],
		];
	}
}
