<?php namespace User\Pam\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

/**
 * Class SettingType.
 */
class ProfileChangeType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'profile_change';
		$this->attributes['description'] = trans('user::change.graphql.type_desc');
		$this->attributes['values']      = [
			// todo 尽量使用常量
			'nickname'  => [
				'description' => trans('user::change.db.nickname'),
			],
			'sex'       => [
				'description' => trans('user::change.db.sex'),
			],
			'signature' => [
				'description' => trans('user::change.db.signature'),
			],
			// todo
			'game'      => [
				'description' => trans('user::change.db.signature'),
			],
			'head_pic'  => [
				'description' => trans('user::change.db.signature'),
			],
			'area'      => [
				'description' => trans('user::change.db.signature'),
			],
		];
	}
}
