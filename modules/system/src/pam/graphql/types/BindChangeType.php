<?php namespace System\Pam\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

/**
 * Class SettingType.
 */
class BindChangeType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BindChange';
		$this->attributes['description'] = trans('system::bind_change.graphql.action_desc');
		$this->attributes['values']      = [
			'old_send' => [
				'description' => trans('system::bind_change.action.old_send'),
			],
			'old_validate' => [
				'description' => trans('system::bind_change.action.old_validate'),
			],
			'new_send' => [
				'description' => trans('system::bind_change.action.new_send'),
			],
			'new_validate' => [
				'description' => trans('system::bind_change.action.new_validate'),
			],
		];
	}
}
