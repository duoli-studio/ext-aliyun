<?php namespace System\Pam\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

/**
 * Class SettingType.
 */
class AreaType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'Area';
		$this->attributes['description'] = trans('system::area.graphql.action_desc');
		$this->attributes['values']      = [
			'create' => [
				'description' => trans('system::area.action.create'),
			],
			'edit' => [
				'description' => trans('system::area.action.edit'),
			],
			'destroy' => [
				'description' => trans('system::area.action.destroy'),
			],
		];
	}
}
